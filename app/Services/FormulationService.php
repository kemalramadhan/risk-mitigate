<?php

namespace App\Services;

use App\Models\Formulation;
use App\Models\Project;
use App\Models\RiskFactor;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Orkestrasi perumusan strategi mitigasi (Fitur 3):
 * membuat ronde, mengumpulkan judgment, memanggil AhpService, lalu
 * menyusun & menyimpan hasil rekomendasi strategi terurut bobot.
 */
class FormulationService
{
    public function __construct(private AhpService $ahp)
    {
    }

    /**
     * Buat satu ronde perumusan baru: semua anggota proyek menjadi partisipan.
     */
    public function startRound(Project $project, int $createdBy): Formulation
    {
        return DB::transaction(function () use ($project, $createdBy) {
            $formulation = $project->formulations()->create([
                'created_by' => $createdBy,
                'status'     => 'open',
                'risk_level' => $project->risk_level,
            ]);

            foreach ($project->members()->get() as $member) {
                $formulation->participants()->create([
                    'user_id' => $member->user_id,
                    'weight'  => $member->weight,
                ]);
            }

            return $formulation;
        });
    }

    /**
     * Simpan judgment AHP-Express milik seorang partisipan.
     *
     * @param  array<int,float>  $judgments  other_factor_id => nilai (w_ref/w_other)
     */
    public function submitJudgments(Formulation $formulation, int $userId, int $referenceFactorId, array $judgments): void
    {
        DB::transaction(function () use ($formulation, $userId, $referenceFactorId, $judgments) {
            $formulation->judgments()
                ->where('user_id', $userId)
                ->delete();

            foreach ($judgments as $otherId => $value) {
                if ((int) $otherId === $referenceFactorId) {
                    continue;
                }
                $formulation->judgments()->create([
                    'user_id'             => $userId,
                    'reference_factor_id' => $referenceFactorId,
                    'other_factor_id'     => (int) $otherId,
                    'value'               => (float) $value,
                ]);
            }

            $participant = $formulation->participants()
                ->where('user_id', $userId)
                ->firstOrFail();

            $participant->update([
                'reference_factor_id' => $referenceFactorId,
                'submitted_at'        => now(),
            ]);
        });
    }

    /**
     * Simpan matriks pairwise penuh (AHP standar) untuk 1 partisipan.
     * Setiap pasangan (i, j) disimpan sebagai baris reference_factor_id = i,
     * other_factor_id = j.
     *
     * @param  array<int,array{i:int,j:int,value:float}>  $comparisons
     */
    public function submitStandardMatrix(Formulation $formulation, int $userId, array $comparisons): void
    {
        DB::transaction(function () use ($formulation, $userId, $comparisons) {
            $formulation->judgments()->where('user_id', $userId)->delete();

            foreach ($comparisons as $c) {
                $formulation->judgments()->create([
                    'user_id'             => $userId,
                    'reference_factor_id' => (int) $c['i'],
                    'other_factor_id'     => (int) $c['j'],
                    'value'               => (float) $c['value'],
                ]);
            }

            $formulation->participants()
                ->where('user_id', $userId)
                ->firstOrFail()
                ->update([
                    'reference_factor_id' => null,
                    'submitted_at'        => now(),
                ]);
        });
    }

    /**
     * Sinkronkan ronde perumusan yang masih TERBUKA dengan komposisi anggota
     * proyek terkini. Dipanggil saat anggota ditambah/dihapus.
     *
     * - Anggota baru ditambahkan sebagai partisipan; anggota keluar dihapus.
     * - Bila komposisi menyeberangi batas 1 <-> banyak anggota, metode berubah
     *   (AHP standar <-> AHP-Express). Format judgment tidak kompatibel,
     *   sehingga seluruh penilaian pada ronde tersebut direset.
     */
    public function syncOpenRounds(Project $project): void
    {
        $members = $project->members()->get(['user_id', 'weight']);
        $memberUserIds = $members->pluck('user_id')->map(fn ($v) => (int) $v)->all();
        $memberWeights = $members->pluck('weight', 'user_id');
        $currentCount = count($memberUserIds);

        foreach ($project->formulations()->where('status', 'open')->get() as $formulation) {
            $participantUserIds = $formulation->participants()
                ->pluck('user_id')->map(fn ($v) => (int) $v)->all();
            $prevCount = count($participantUserIds);
            $modeChanged = ($prevCount === 1) !== ($currentCount === 1);

            DB::transaction(function () use ($formulation, $memberUserIds, $participantUserIds, $memberWeights, $modeChanged) {
                foreach (array_diff($memberUserIds, $participantUserIds) as $uid) {
                    $formulation->participants()->create([
                        'user_id' => $uid,
                        'weight'  => $memberWeights[$uid] ?? null,
                    ]);
                }

                foreach (array_diff($participantUserIds, $memberUserIds) as $uid) {
                    $formulation->judgments()->where('user_id', $uid)->delete();
                    $formulation->participants()->where('user_id', $uid)->delete();
                }

                if ($modeChanged) {
                    // Format penilaian berbeda -> mulai ulang penilaian ronde ini.
                    $formulation->judgments()->delete();
                    $formulation->participants()->update([
                        'submitted_at'        => null,
                        'reference_factor_id' => null,
                        'priority_vector'     => null,
                    ]);
                }
            });
        }
    }

    /**
     * Hitung hasil ronde: panggil engine, susun rekomendasi, simpan.
     */
    public function compute(Formulation $formulation): Formulation
    {
        $project = $formulation->project;

        /** @var \Illuminate\Support\Collection<int,RiskFactor> $factors */
        $factors = RiskFactor::forContext($project->domain, $formulation->risk_level)
            ->with('strategies.references')
            ->get();

        $elementIds = $factors->pluck('id')->all();

        $participants = $formulation->participants()->with('user')->get();
        $submitted = $participants->whereNotNull('submitted_at');

        if ($submitted->isEmpty()) {
            throw new RuntimeException('Belum ada partisipan yang menyerahkan penilaian.');
        }

        if ($formulation->isStandard()) {
            // AHP standar: 1 partisipan, matriks pairwise penuh.
            $participant = $submitted->first();
            $comparisons = $formulation->judgments()
                ->where('user_id', $participant->user_id)
                ->get(['reference_factor_id', 'other_factor_id', 'value'])
                ->map(fn ($j) => [
                    'i'     => (int) $j->reference_factor_id,
                    'j'     => (int) $j->other_factor_id,
                    'value' => (float) $j->value,
                ])->all();

            $output = $this->ahp->computeStandard($elementIds, $participant->user_id, $comparisons);
        } else {
            // AHP-Express + agregasi grup (AIP weighted geometric mean).
            // Bobot memakai pembobotan expert (Fitur 2) TERKINI dari proyek,
            // bukan snapshot lama, agar pembobotan setelah ronde dibuat berlaku.
            $memberWeights = $project->members()->pluck('weight', 'user_id');

            $payloadParticipants = [];
            foreach ($submitted as $participant) {
                $weight = $memberWeights[$participant->user_id] ?? null;
                $participant->weight = $weight; // segarkan untuk hasil + snapshot

                $judgments = $formulation->judgments()
                    ->where('user_id', $participant->user_id)
                    ->pluck('value', 'other_factor_id')
                    ->map(fn ($v) => (float) $v)
                    ->toArray();

                $payloadParticipants[$participant->user_id] = [
                    'weight'    => $weight,
                    'reference' => $participant->reference_factor_id,
                    'judgments' => $judgments,
                ];
            }

            $output = $this->ahp->aggregate($elementIds, $payloadParticipants);
        }

        $result = $this->buildResult($formulation, $factors, $submitted, $output);

        // Simpan snapshot vektor prioritas individual pada tiap partisipan.
        DB::transaction(function () use ($formulation, $submitted, $output, $result) {
            foreach ($submitted as $participant) {
                $vector = $output['individual'][(string) $participant->user_id] ?? null;
                $participant->update([
                    'priority_vector' => $vector,
                    'weight'          => $participant->weight, // snapshot bobot yang dipakai
                ]);
            }

            $formulation->update([
                'status'      => 'completed',
                'method'      => $output['method'],
                'computed_at' => now(),
                'result'      => $result,
            ]);
        });

        return $formulation->fresh();
    }

    /**
     * Susun struktur hasil: faktor terurut bobot + strategi terlampir.
     */
    private function buildResult(Formulation $formulation, $factors, $submitted, array $output): array
    {
        $factorsById = $factors->keyBy('id');
        $aggregated = $output['aggregated'];

        $rankedFactors = [];
        foreach ($output['ranking'] as $row) {
            $fid = (int) $row['element'];
            $factor = $factorsById->get($fid);
            if (! $factor) {
                continue;
            }

            $strategies = $factor->strategies->map(function ($s) {
                return [
                    'id'         => $s->id,
                    'text'       => $s->strategy_text,
                    'references' => $s->references->map(fn ($r) => [
                        'sumber'       => $r->sumber,
                        'judul_sumber' => $r->judul_sumber,
                        'lokasi'       => $r->lokasi,
                        'kutipan'      => $r->kutipan,
                    ])->all(),
                ];
            })->all();

            $rankedFactors[] = [
                'risk_factor_id' => $fid,
                'name'           => $factor->name,
                'display_name'   => $factor->display_name,
                'description'    => $factor->description,
                'weight'         => (float) $row['weight'],
                'percentage'     => round($row['weight'] * 100, 2),
                'rank'           => (int) $row['rank'],
                'strategies'     => $strategies,
            ];
        }

        // Ringkasan partisipan + bobot DM.
        $participantSummary = [];
        foreach ($submitted as $p) {
            $participantSummary[] = [
                'user_id'             => $p->user_id,
                'name'                => $p->user->name,
                'reference_factor_id' => $p->reference_factor_id,
                'weight'              => $p->weight,
                'priority_vector'     => $output['individual'][(string) $p->user_id] ?? null,
            ];
        }

        return [
            'method'       => $output['method'],
            'mode'         => $formulation->isStandard() ? 'standard' : 'express',
            'cr'           => $output['cr'] ?? 0.0,
            'consistency'  => $output['consistency'] ?? null,
            'computed_at'  => now()->toIso8601String(),
            'aggregated'   => $aggregated,
            'factors'      => $rankedFactors,
            'participants' => $participantSummary,
        ];
    }
}
