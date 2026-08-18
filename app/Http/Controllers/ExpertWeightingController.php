<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ExpertWeightingService;
use App\Support\SaatyScale;
use Illuminate\Http\Request;

class ExpertWeightingController extends Controller
{
    public function __construct(private ExpertWeightingService $weighting)
    {
    }

    /** Form pairwise comparison antar expert (owner). */
    public function edit(Request $request, Project $project)
    {
        $this->authorizeOwner($request, $project);

        $members = $project->members()->with('user')->get();

        // Referensi default: judgment tersimpan atau anggota pertama.
        $existing = $project->expertWeightJudgments()->get();
        $referenceId = $existing->first()->reference_user_id ?? $members->first()?->user_id;

        // Pre-fill posisi slider dari judgment tersimpan.
        $positions = [];
        foreach ($existing as $j) {
            $positions[$j->other_user_id] = SaatyScale::valueToPosition((float) $j->value);
        }

        return view('weighting.edit', [
            'project'     => $project,
            'members'     => $members,
            'referenceId' => $referenceId,
            'positions'   => $positions,
            'scaleLabels' => config('ahp.scale_labels'),
            'hasWeights'  => $project->hasExpertWeights(),
        ]);
    }

    /** Hitung & simpan bobot expert dari judgment. */
    public function update(Request $request, Project $project)
    {
        $this->authorizeOwner($request, $project);

        $memberIds = $project->members()->pluck('user_id')->map(fn ($v) => (int) $v)->all();

        $validated = $request->validate([
            'reference_user_id' => 'required|integer|in:'.implode(',', $memberIds),
            'pos'               => 'array',
            'pos.*'             => SaatyScale::validationRule(),
        ]);

        $reference = (int) $validated['reference_user_id'];
        $positions = $validated['pos'] ?? [];

        // Bangun judgment: value = w_ref / w_other.
        $judgments = [];
        foreach ($memberIds as $uid) {
            if ($uid === $reference) {
                continue;
            }
            $pos = (int) ($positions[$uid] ?? 0);
            $judgments[$uid] = SaatyScale::positionToValue($pos);
        }

        $weights = $this->weighting->computeAndStore($project, $reference, $judgments);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', 'Pembobotan expert tersimpan. Total bobot = '.round(array_sum($weights), 4).'.');
    }

    /** Kembalikan ke asumsi bobot sama. */
    public function reset(Request $request, Project $project)
    {
        $this->authorizeOwner($request, $project);

        $this->weighting->resetToEqual($project);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', 'Pembobotan direset. Bobot setiap expert dianggap sama.');
    }

    private function authorizeOwner(Request $request, Project $project): void
    {
        abort_unless(
            $project->isOwner($request->user()),
            403,
            'Hanya pemilik proyek yang dapat melakukan pembobotan expert.'
        );
    }
}
