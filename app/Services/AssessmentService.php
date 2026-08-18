<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

/**
 * Orkestrasi assessment: satu putaran perumusan yang mencakup keempat level
 * risiko sekaligus. Tiap level direpresentasikan oleh satu Formulation,
 * sehingga seluruh logika AHP per level (standar/express + AIP + CR) dipakai
 * ulang dari FormulationService.
 */
class AssessmentService
{
    public function __construct(private FormulationService $formulations)
    {
    }

    /**
     * Mulai assessment baru dengan daftar penilai terpilih.
     *
     * @param  array<int>  $assessorUserIds  user id penilai (>= 1)
     */
    public function start(Project $project, int $createdBy, array $assessorUserIds): Assessment
    {
        $assessorUserIds = array_values(array_unique(array_map('intval', $assessorUserIds)));

        // Bobot expert (Fitur 2) untuk snapshot ke partisipan tiap formulation.
        $memberWeights = $project->members()->pluck('weight', 'user_id');

        return DB::transaction(function () use ($project, $createdBy, $assessorUserIds, $memberWeights) {
            $assessment = $project->assessments()->create([
                'created_by' => $createdBy,
                'status'     => 'open',
            ]);

            foreach ($assessorUserIds as $uid) {
                $assessment->participants()->create(['user_id' => $uid]);
            }

            // Satu formulation per level risiko, masing-masing berisi para penilai.
            foreach (Assessment::LEVELS as $level) {
                $formulation = $assessment->formulations()->create([
                    'project_id' => $project->id,
                    'created_by' => $createdBy,
                    'status'     => 'open',
                    'risk_level' => $level,
                ]);

                foreach ($assessorUserIds as $uid) {
                    $formulation->participants()->create([
                        'user_id' => $uid,
                        'weight'  => $memberWeights[$uid] ?? null,
                    ]);
                }
            }

            return $assessment;
        });
    }

    /**
     * Hitung seluruh level yang penilaiannya sudah lengkap namun belum dihitung,
     * lalu perbarui status penyelesaian assessment.
     */
    public function computeReady(Assessment $assessment): void
    {
        $assessment->load('formulations.participants');

        foreach ($assessment->formulations as $formulation) {
            if ($formulation->status !== 'completed' && $formulation->allSubmitted()) {
                $this->formulations->compute($formulation);
            }
        }

        $this->refreshCompletion($assessment);
    }

    /** Tandai assessment selesai bila keempat level sudah dihitung. */
    public function refreshCompletion(Assessment $assessment): void
    {
        $assessment->load('formulations');

        if ($assessment->allComputed() && ! $assessment->isCompleted()) {
            $assessment->update([
                'status'      => 'completed',
                'computed_at' => now(),
            ]);
        }
    }
}
