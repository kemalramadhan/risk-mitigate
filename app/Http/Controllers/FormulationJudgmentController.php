<?php

namespace App\Http\Controllers;

use App\Models\Formulation;
use App\Models\RiskFactor;
use App\Services\AssessmentService;
use App\Services\FormulationService;
use App\Support\SaatyScale;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FormulationJudgmentController extends Controller
{
    public function __construct(
        private FormulationService $formulations,
        private AssessmentService $assessments,
    ) {
    }

    /** Faktor risiko untuk level yang dinilai formulation ini. */
    private function factorsFor(Formulation $formulation)
    {
        return RiskFactor::forContext(
            $formulation->project->domain,
            $formulation->risk_level
        )->get();
    }

    /** Form pairwise satu level milik penilai (standar penuh atau AHP-Express). */
    public function edit(Request $request, Formulation $formulation)
    {
        $this->ensureParticipantAccess($request, $formulation);

        $assessment = $formulation->assessment;

        if ($formulation->isCompleted()) {
            return redirect()->route('assessments.show', $assessment)
                ->with('status', "Level {$formulation->risk_level} sudah selesai dihitung.");
        }

        $factors = $this->factorsFor($formulation);
        $existing = $formulation->judgments()->where('user_id', $request->user()->id)->get();

        // ---- Mode STANDAR: matriks pairwise penuh (1 penilai) ----
        if ($formulation->isStandard()) {
            $positions = [];
            foreach ($existing as $j) {
                $positions[$j->reference_factor_id.'_'.$j->other_factor_id] =
                    SaatyScale::valueToPosition((float) $j->value);
            }

            return view('formulations.judge_standard', [
                'project'     => $formulation->project,
                'formulation' => $formulation,
                'assessment'  => $assessment,
                'factors'     => $factors,
                'positions'   => $positions,
                'scaleLabels' => config('ahp.scale_labels'),
            ]);
        }

        // ---- Mode AHP-EXPRESS (>1 penilai) ----
        $referenceId = $existing->first()->reference_factor_id ?? $factors->first()?->id;

        $positions = [];
        foreach ($existing as $j) {
            $positions[$j->other_factor_id] = SaatyScale::valueToPosition((float) $j->value);
        }

        return view('formulations.judge', [
            'project'     => $formulation->project,
            'formulation' => $formulation,
            'assessment'  => $assessment,
            'factors'     => $factors,
            'referenceId' => $referenceId,
            'positions'   => $positions,
            'scaleLabels' => config('ahp.scale_labels'),
        ]);
    }

    /** Simpan penilaian satu level; auto-hitung level itu bila semua sudah menilai. */
    public function update(Request $request, Formulation $formulation)
    {
        $this->ensureParticipantAccess($request, $formulation);

        abort_if($formulation->isCompleted(), 403, 'Level ini sudah selesai.');

        if ($formulation->isStandard()) {
            $this->storeStandard($request, $formulation);
        } else {
            $this->storeExpress($request, $formulation);
        }

        $formulation->refresh();
        $autoComputed = false;
        if ($formulation->allSubmitted() && ! $formulation->isCompleted()) {
            try {
                $this->formulations->compute($formulation);
                $this->assessments->refreshCompletion($formulation->assessment);
                $autoComputed = true;
            } catch (\Throwable $e) {
                return redirect()->route('assessments.show', $formulation->assessment_id)
                    ->withErrors(['compute' => $e->getMessage()]);
            }
        }

        $msg = $autoComputed
            ? "Penilaian level {$formulation->risk_level} tersimpan & dihitung."
            : "Penilaian level {$formulation->risk_level} tersimpan. Menunggu penilai lain.";

        return redirect()->route('assessments.show', $formulation->assessment_id)->with('status', $msg);
    }

    /** AHP-Express: 1 referensi vs (n-1) faktor lain. */
    private function storeExpress(Request $request, Formulation $formulation): void
    {
        $factorIds = $this->factorsFor($formulation)->pluck('id')->map(fn ($v) => (int) $v)->all();

        $validated = $request->validate([
            'reference_factor_id' => 'required|integer|in:'.implode(',', $factorIds),
            'pos'                 => 'array',
            'pos.*'               => SaatyScale::validationRule(),
        ]);

        $reference = (int) $validated['reference_factor_id'];
        $positions = $validated['pos'] ?? [];

        $judgments = [];
        foreach ($factorIds as $fid) {
            if ($fid === $reference) {
                continue;
            }
            $pos = (int) ($positions[$fid] ?? 1);
            $judgments[$fid] = SaatyScale::positionToValue($pos);
        }

        $this->formulations->submitJudgments($formulation, $request->user()->id, $reference, $judgments);
    }

    /** AHP standar: seluruh pasangan (i, j) dinilai. */
    private function storeStandard(Request $request, Formulation $formulation): void
    {
        $request->validate([
            'pos'     => 'required|array',
            'pos.*'   => 'array',
            'pos.*.*' => SaatyScale::validationRule(),
        ]);

        $factorIds = $this->factorsFor($formulation)->pluck('id')->map(fn ($v) => (int) $v)->all();
        $positions = $request->input('pos', []);

        $comparisons = [];
        foreach ($factorIds as $a => $i) {
            foreach ($factorIds as $b => $j) {
                if ($b <= $a) {
                    continue;
                }
                if (! isset($positions[$i][$j])) {
                    throw ValidationException::withMessages([
                        'pos' => 'Seluruh pasangan faktor harus dinilai.',
                    ]);
                }
                $comparisons[] = [
                    'i'     => $i,
                    'j'     => $j,
                    'value' => SaatyScale::positionToValue((int) $positions[$i][$j]),
                ];
            }
        }

        $this->formulations->submitStandardMatrix($formulation, $request->user()->id, $comparisons);
    }

    private function ensureParticipantAccess(Request $request, Formulation $formulation): void
    {
        $isParticipant = $formulation->participants()
            ->where('user_id', $request->user()->id)
            ->exists();

        abort_unless($isParticipant, 403, 'Anda bukan penilai pada assessment ini.');
    }
}
