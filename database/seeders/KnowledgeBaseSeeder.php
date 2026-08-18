<?php

namespace Database\Seeders;

use App\Models\MitigationStrategy;
use App\Models\RiskFactor;
use App\Models\StrategyReference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/knowledge_base.json');

        if (! file_exists($path)) {
            $this->command->error("File tidak ditemukan: {$path}");
            $this->command->warn('Jalankan dahulu: python3 database/data/export_knowledge_base.py');

            return;
        }

        $factors = json_decode(file_get_contents($path), true);

        if (! is_array($factors)) {
            $this->command->error('knowledge_base.json tidak valid.');

            return;
        }

        // Deskripsi faktor (kunci = nama dasar). Urut dari nama terpanjang agar
        // longest-prefix match tidak salah pilih (mis. Org_Process_Maturity vs
        // Organizational_Change_Frequency).
        $descriptions = require database_path('data/factor_descriptions.php');
        $baseNames = array_keys($descriptions);
        usort($baseNames, fn ($a, $b) => strlen($b) <=> strlen($a));
        $unmatched = [];

        // Bersihkan data lama (aman dijalankan ulang / idempotent).
        DB::table('strategy_references')->delete();
        DB::table('mitigation_strategies')->delete();
        DB::table('risk_factors')->delete();

        // Lacak posisi faktor per (domain, level) agar terurut 1..9.
        $positionTracker = [];
        $countFactors = 0;
        $countStrategies = 0;
        $countReferences = 0;

        foreach ($factors as $f) {
            $key = $f['domain'].'|'.$f['risk_level'];
            $positionTracker[$key] = ($positionTracker[$key] ?? 0) + 1;

            // Cari nama dasar terpanjang yang menjadi awalan nama faktor.
            $base = null;
            foreach ($baseNames as $candidate) {
                if (str_starts_with($f['name'], $candidate)) {
                    $base = $candidate;
                    break;
                }
            }
            if ($base === null) {
                $unmatched[] = $f['name'];
            }

            $factor = RiskFactor::create([
                'domain'       => $f['domain'],
                'risk_level'   => $f['risk_level'],
                'name'         => $f['name'],
                'base_name'    => $base,
                'display_name' => $f['display_name'],
                'description'  => $base ? $descriptions[$base] : null,
                'position'     => $positionTracker[$key],
            ]);
            $countFactors++;

            $sPos = 0;
            foreach ($f['strategies'] as $s) {
                $sPos++;
                $strategy = MitigationStrategy::create([
                    'risk_factor_id' => $factor->id,
                    'strategy_text'  => $s['text'],
                    'position'       => $sPos,
                ]);
                $countStrategies++;

                $rPos = 0;
                foreach ($s['references'] ?? [] as $r) {
                    $rPos++;
                    StrategyReference::create([
                        'mitigation_strategy_id' => $strategy->id,
                        'sumber'       => $r['sumber'] ?? null,
                        'judul_sumber' => $r['judul_sumber'] ?? null,
                        'lokasi'       => $r['lokasi'] ?? null,
                        'kutipan'      => $r['kutipan'] ?? null,
                        'position'     => $rPos,
                    ]);
                    $countReferences++;
                }
            }
        }

        $this->command->info("Knowledge Base ter-seed: {$countFactors} faktor, {$countStrategies} strategi, {$countReferences} referensi.");

        $described = RiskFactor::whereNotNull('description')->count();
        $this->command->info("Deskripsi faktor terisi: {$described}/{$countFactors}.");

        if ($unmatched !== []) {
            $this->command->error('Faktor tanpa deskripsi (nama dasar tidak dikenali):');
            foreach (array_unique($unmatched) as $name) {
                $this->command->line("  - {$name}");
            }
        }
    }
}
