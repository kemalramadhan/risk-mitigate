<?php

namespace App\Console\Commands;

use App\Models\Assessment;
use App\Models\ExpertWeightJudgment;
use App\Models\RiskFactor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Ekspor data uji coba pakar ke sekumpulan CSV (siap dibuka di Excel / dianalisis).
 *
 *   php artisan trials:export
 *
 * Menghasilkan folder bertimestamp di storage/app/private/exports/ berisi:
 *   - assessments_summary.csv : satu baris per (assessment, level) -> metode, mode, CR, status, dsb.
 *   - rankings.csv            : hasil akhir -> peringkat faktor + bobot (%) per level.
 *   - judgments.csv           : INPUT MENTAH pairwise tiap pakar (apa yang mereka lakukan).
 *   - participant_weights.csv : bobot tiap pakar (AIP) per level + status submit.
 *   - expert_weights.csv      : judgment pembobotan expert (Fitur 2) per proyek.
 */
class ExportTrialsCommand extends Command
{
    protected $signature = 'trials:export {--assessment=* : Batasi ke ID assessment tertentu}';

    protected $description = 'Ekspor data uji coba pakar (judgment mentah + hasil) ke CSV';

    public function handle(): int
    {
        $dir = storage_path('app/private/exports/trials_'.now()->format('Ymd_His'));
        File::ensureDirectoryExists($dir);

        // Peta id faktor -> display_name untuk menerjemahkan judgment mentah.
        $factorName = RiskFactor::pluck('display_name', 'id')->all();

        $query = Assessment::with([
            'project.owner',
            'creator',
            'formulations.participants.user',
            'formulations.judgments.user',
        ])->orderBy('id');

        $ids = array_filter((array) $this->option('assessment'));
        if ($ids) {
            $query->whereIn('id', $ids);
        }

        $assessments = $query->get();
        if ($assessments->isEmpty()) {
            $this->warn('Tidak ada assessment untuk diekspor.');

            return self::SUCCESS;
        }

        $summary = $this->open($dir, 'assessments_summary.csv', [
            'assessment_id', 'project', 'domain', 'owner', 'level',
            'status', 'method', 'mode', 'cr', 'consistency',
            'jml_penilai', 'computed_at',
        ]);
        $rankings = $this->open($dir, 'rankings.csv', [
            'assessment_id', 'project', 'level', 'rank', 'faktor', 'bobot_persen', 'bobot',
        ]);
        $judgments = $this->open($dir, 'judgments.csv', [
            'assessment_id', 'project', 'level', 'pakar', 'email',
            'faktor_referensi', 'faktor_pembanding', 'nilai',
        ]);
        $pweights = $this->open($dir, 'participant_weights.csv', [
            'assessment_id', 'project', 'level', 'pakar', 'email',
            'bobot_pakar', 'faktor_referensi', 'submitted_at',
        ]);

        foreach ($assessments as $a) {
            $proj = $a->project?->name ?? '-';
            $domain = $a->project?->domain ?? '-';
            $owner = $a->project?->owner?->name ?? $a->creator?->name ?? '-';

            foreach ($a->formulations as $f) {
                $res = $f->result ?? [];

                fputcsv($summary, [
                    $a->id, $proj, $domain, $owner, $f->risk_level,
                    $f->status, $res['method'] ?? '-', $res['mode'] ?? '-',
                    $this->flat($res['cr'] ?? ''), $this->flat($res['consistency'] ?? ''),
                    $f->participants->count(),
                    $f->computed_at?->toDateTimeString() ?? '',
                ]);

                foreach ($res['factors'] ?? [] as $fac) {
                    fputcsv($rankings, [
                        $a->id, $proj, $f->risk_level,
                        $fac['rank'] ?? '', $fac['display_name'] ?? '',
                        $fac['percentage'] ?? '', $fac['weight'] ?? '',
                    ]);
                }

                foreach ($f->judgments as $j) {
                    fputcsv($judgments, [
                        $a->id, $proj, $f->risk_level,
                        $j->user?->name ?? '-', $j->user?->email ?? '-',
                        $factorName[$j->reference_factor_id] ?? $j->reference_factor_id,
                        $factorName[$j->other_factor_id] ?? $j->other_factor_id,
                        $j->value,
                    ]);
                }

                foreach ($f->participants as $p) {
                    fputcsv($pweights, [
                        $a->id, $proj, $f->risk_level,
                        $p->user?->name ?? '-', $p->user?->email ?? '-',
                        $p->weight, $factorName[$p->reference_factor_id] ?? $p->reference_factor_id,
                        $p->submitted_at?->toDateTimeString() ?? '',
                    ]);
                }
            }
        }

        $ewOut = $this->open($dir, 'expert_weights.csv', [
            'project_id', 'project', 'pakar_referensi', 'pakar_pembanding', 'nilai',
        ]);
        ExpertWeightJudgment::with(['project', 'referenceUser', 'otherUser'])
            ->orderBy('project_id')
            ->get()
            ->each(function ($w) use ($ewOut) {
                fputcsv($ewOut, [
                    $w->project_id, $w->project?->name ?? '-',
                    $w->referenceUser?->name ?? $w->reference_user_id,
                    $w->otherUser?->name ?? $w->other_user_id,
                    $w->value,
                ]);
            });

        foreach ([$summary, $rankings, $judgments, $pweights, $ewOut] as $h) {
            fclose($h);
        }

        $this->info("Ekspor selesai: {$dir}");
        foreach (File::files($dir) as $file) {
            $this->line('  - '.$file->getFilename().' ('.number_format($file->getSize()).' bytes)');
        }

        return self::SUCCESS;
    }

    /** Ubah nilai array/objek menjadi string ringkas agar aman ditulis ke CSV. */
    private function flat(mixed $v): string
    {
        if (is_array($v)) {
            return json_encode($v, JSON_UNESCAPED_UNICODE);
        }

        return (string) $v;
    }

    /** @return resource */
    private function open(string $dir, string $name, array $header)
    {
        $h = fopen($dir.'/'.$name, 'w');
        fwrite($h, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8 dengan benar
        fputcsv($h, $header);

        return $h;
    }
}
