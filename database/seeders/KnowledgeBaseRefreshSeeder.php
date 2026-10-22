<?php

namespace Database\Seeders;

use App\Models\RiskFactor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Memperbarui metadata referensi (sumber, judul_sumber, lokasi, kutipan) pada
 * strategy_references SECARA IN-PLACE dari knowledge_base.json terbaru —
 * TANPA menghapus risk_factors / mitigation_strategies.
 *
 * Tujuan: menerapkan revisi Knowledge Base (mis. rev4) tanpa memicu
 * cascade delete pada formulation_judgments (data input AHP para pakar).
 *
 * Pencocokan berbasis posisi: faktor dicocokkan (domain, risk_level, name),
 * lalu strategi & referensi dicocokkan urut posisi. Struktur (jumlah strategi
 * dan jumlah referensi per strategi) WAJIB identik dengan yang ada di DB; bila
 * tidak, seeder dibatalkan tanpa menulis apa pun.
 *
 *   php artisan db:seed --class=KnowledgeBaseRefreshSeeder
 */
class KnowledgeBaseRefreshSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/knowledge_base.json');
        if (! file_exists($path)) {
            $this->command->error("File tidak ditemukan: {$path}");

            return;
        }

        $factors = json_decode(file_get_contents($path), true);
        if (! is_array($factors)) {
            $this->command->error('knowledge_base.json tidak valid.');

            return;
        }

        // ---- Fase 1: validasi struktur (tanpa menulis apa pun) ----
        $errors = [];
        foreach ($factors as $f) {
            $factor = RiskFactor::with('strategies.references')
                ->where('domain', $f['domain'])
                ->where('risk_level', $f['risk_level'])
                ->where('name', $f['name'])
                ->first();

            if (! $factor) {
                $errors[] = "Faktor tidak ada di DB: {$f['domain']}/{$f['risk_level']}/{$f['name']}";

                continue;
            }

            $dbStrategies = $factor->strategies;
            $jsonStrategies = $f['strategies'];
            if (count($dbStrategies) !== count($jsonStrategies)) {
                $errors[] = "Jumlah strategi beda pada {$f['name']}: DB=".count($dbStrategies).' JSON='.count($jsonStrategies);

                continue;
            }

            foreach ($jsonStrategies as $i => $s) {
                $dbRefs = $dbStrategies[$i]->references;
                $jsonRefs = $s['references'] ?? [];
                if (count($dbRefs) !== count($jsonRefs)) {
                    $errors[] = "Jumlah referensi beda pada {$f['name']} strategi#".($i + 1).": DB=".count($dbRefs).' JSON='.count($jsonRefs);
                }
            }
        }

        if ($errors !== []) {
            $this->command->error('Dibatalkan — struktur DB tidak cocok dengan knowledge_base.json:');
            foreach ($errors as $e) {
                $this->command->line("  - {$e}");
            }
            throw new RuntimeException('KnowledgeBaseRefreshSeeder dibatalkan: struktur tidak cocok. Tidak ada data yang diubah.');
        }

        // ---- Fase 2: terapkan update in-place ----
        $changed = 0;
        $scanned = 0;

        DB::transaction(function () use ($factors, &$changed, &$scanned) {
            foreach ($factors as $f) {
                $factor = RiskFactor::with('strategies.references')
                    ->where('domain', $f['domain'])
                    ->where('risk_level', $f['risk_level'])
                    ->where('name', $f['name'])
                    ->first();

                foreach ($f['strategies'] as $i => $s) {
                    $dbRefs = $factor->strategies[$i]->references;
                    foreach (($s['references'] ?? []) as $j => $r) {
                        $ref = $dbRefs[$j];
                        $scanned++;

                        $new = [
                            'sumber'       => $r['sumber'] ?? null,
                            'judul_sumber' => $r['judul_sumber'] ?? null,
                            'lokasi'       => $r['lokasi'] ?? null,
                            'kutipan'      => $r['kutipan'] ?? null,
                        ];

                        $dirty = false;
                        foreach ($new as $k => $v) {
                            if ((string) $ref->{$k} !== (string) $v) {
                                $dirty = true;
                                break;
                            }
                        }

                        if ($dirty) {
                            $ref->fill($new)->save();
                            $changed++;
                        }
                    }
                }
            }
        });

        $this->command->info("Refresh selesai. Referensi diperiksa: {$scanned}, diperbarui: {$changed}.");
        $this->command->line('risk_factors & mitigation_strategies tidak disentuh (formulation_judgments aman).');
    }
}
