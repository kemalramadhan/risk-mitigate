<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

/**
 * Pembobotan expert (Fitur 2): owner melakukan pairwise comparison
 * AHP-Express terhadap seluruh anggota (termasuk dirinya) untuk menetapkan
 * bobot tiap DM. Total bobot = 1. Bila tidak dilakukan, bobot dianggap sama.
 */
class ExpertWeightingService
{
    public function __construct(private AhpService $ahp)
    {
    }

    /**
     * Hitung & simpan bobot expert dari judgment AHP-Express milik owner.
     *
     * @param  int  $referenceUserId  user id referensi
     * @param  array<int,float>  $judgments  other_user_id => nilai (w_ref/w_other)
     * @return array<int,float>  user_id => bobot (Σ = 1)
     */
    public function computeAndStore(Project $project, int $referenceUserId, array $judgments): array
    {
        $memberUserIds = $project->members()->pluck('user_id')->map(fn ($v) => (int) $v)->all();

        $weights = $this->ahp->expressWeights($memberUserIds, $referenceUserId, $judgments);

        DB::transaction(function () use ($project, $referenceUserId, $judgments, $weights, $memberUserIds) {
            // Simpan judgment mentah (bisa direvisi kemudian).
            $project->expertWeightJudgments()->delete();
            foreach ($judgments as $otherId => $value) {
                if ((int) $otherId === $referenceUserId) {
                    continue;
                }
                $project->expertWeightJudgments()->create([
                    'reference_user_id' => $referenceUserId,
                    'other_user_id'     => (int) $otherId,
                    'value'             => (float) $value,
                ]);
            }

            // Simpan bobot ke tiap anggota.
            foreach ($memberUserIds as $uid) {
                $project->members()
                    ->where('user_id', $uid)
                    ->update(['weight' => $weights[$uid] ?? null]);
            }
        });

        return $weights;
    }

    /** Kembalikan ke asumsi bobot sama (hapus pembobotan). */
    public function resetToEqual(Project $project): void
    {
        DB::transaction(function () use ($project) {
            $project->expertWeightJudgments()->delete();
            $project->members()->update(['weight' => null]);
        });
    }
}
