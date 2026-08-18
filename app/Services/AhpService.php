<?php

namespace App\Services;

use App\Models\Formulation;
use App\Models\Project;
use Illuminate\Support\Facades\Process;
use RuntimeException;

/**
 * Jembatan antara Laravel dan engine AHP berbasis Python (ahp/ahp_engine.py).
 *
 * Semua kalkulasi AHP-Express + agregasi grup (AIP weighted geometric mean)
 * dilakukan di Python sesuai brief ("Data Processing & Algorithm: Python").
 */
class AhpService
{
    /**
     * Jalankan engine Python dengan payload dan kembalikan hasil ter-decode.
     *
     * @param  array<string,mixed>  $payload
     * @return array<string,mixed>
     */
    public function run(array $payload): array
    {
        $python = config('ahp.python');
        $engine = base_path(config('ahp.engine'));
        $timeout = config('ahp.timeout');

        $result = Process::timeout($timeout)
            ->input(json_encode($payload, JSON_UNESCAPED_UNICODE))
            ->run([$python, $engine]);

        if (! $result->successful()) {
            throw new RuntimeException(
                'Engine AHP gagal dijalankan: '.trim($result->errorOutput() ?: $result->output())
            );
        }

        $decoded = json_decode($result->output(), true);

        if (! is_array($decoded)) {
            throw new RuntimeException('Output engine AHP tidak valid: '.$result->output());
        }

        if (($decoded['ok'] ?? false) !== true) {
            throw new RuntimeException('Engine AHP: '.($decoded['error'] ?? 'unknown error'));
        }

        return $decoded;
    }

    /**
     * Hitung vektor prioritas dari satu set judgment AHP-Express.
     * Dipakai untuk pembobotan expert (Fitur 2).
     *
     * @param  array<int>  $elements       id elemen (mis. user id), terurut
     * @param  int  $reference             id elemen referensi
     * @param  array<int|string,float>  $judgments  id_other => nilai (w_ref/w_other)
     * @return array<int,float>            id elemen => bobot (Σ = 1)
     */
    public function expressWeights(array $elements, int $reference, array $judgments): array
    {
        $out = $this->run([
            'operation'    => 'expert_weights',
            'elements'     => array_values($elements),
            'participants' => [[
                'id'        => 0,
                'reference' => $reference,
                'judgments' => $judgments,
            ]],
        ]);

        // key int-cast untuk kemudahan pemakaian di sisi PHP.
        $weights = [];
        foreach ($out['aggregated'] as $id => $w) {
            $weights[(int) $id] = (float) $w;
        }

        return $weights;
    }

    /**
     * Hitung prioritas AHP standar (pairwise penuh) untuk 1 partisipan.
     *
     * @param  array<int>  $elements  id faktor risiko, terurut
     * @param  int  $userId
     * @param  array<int,array{i:int,j:int,value:float}>  $comparisons  semua pasangan (i<j)
     * @return array<string,mixed>  output engine (aggregated, ranking, cr, consistency)
     */
    public function computeStandard(array $elements, int $userId, array $comparisons): array
    {
        return $this->run([
            'operation'    => 'formulation',
            'mode'         => 'standard',
            'elements'     => array_values($elements),
            'participants' => [[
                'id'          => $userId,
                'comparisons' => array_values($comparisons),
            ]],
        ]);
    }

    /**
     * Hitung hasil satu ronde perumusan strategi (Fitur 3).
     *
     * @param  array<int>  $elements  id faktor risiko (9), terurut
     * @param  array<int,array{weight:?float,reference:int,judgments:array}>  $participants
     *         keyed by user id
     * @return array<string,mixed>  output engine (individual, aggregated, ranking, method)
     */
    public function aggregate(array $elements, array $participants): array
    {
        $payload = [
            'operation'    => 'formulation',
            'elements'     => array_values($elements),
            'participants' => [],
        ];

        foreach ($participants as $userId => $p) {
            $payload['participants'][] = [
                'id'        => (int) $userId,
                'weight'    => $p['weight'] ?? null,
                'reference' => (int) $p['reference'],
                'judgments' => $p['judgments'] ?? [],
            ];
        }

        return $this->run($payload);
    }
}
