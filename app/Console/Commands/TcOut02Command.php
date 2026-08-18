<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\RiskFactor;
use App\Models\User;
use App\Services\AssessmentService;
use App\Services\FormulationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Uji reproduksi TC-OUT-02 melalui ALUR LENGKAP sistem (bukan hanya engine):
 * submitJudgments -> compute -> baca result tersimpan.
 */
class TcOut02Command extends Command
{
    protected $signature = 'tc:out02';
    protected $description = 'Reproduksi TC-OUT-02 lewat FormulationService';

    public function handle(FormulationService $formulations, AssessmentService $assessments): int
    {
        User::whereIn('email', ['tcp1@x.test', 'tcp2@x.test', 'tcp3@x.test'])->delete();

        $p1 = User::create(['name' => 'P1', 'email' => 'tcp1@x.test', 'password' => Hash::make('x')]);
        $p2 = User::create(['name' => 'P2', 'email' => 'tcp2@x.test', 'password' => Hash::make('x')]);
        $p3 = User::create(['name' => 'P3', 'email' => 'tcp3@x.test', 'password' => Hash::make('x')]);

        $project = Project::create([
            'code' => 'RM-IT-'.strtoupper(Str::random(6)),
            'owner_id' => $p1->id, 'name' => 'TC-OUT-02', 'domain' => 'IT',
        ]);
        $project->members()->create(['user_id' => $p1->id, 'role' => 'owner']);
        $project->members()->create(['user_id' => $p2->id, 'role' => 'expert']);
        $project->members()->create(['user_id' => $p3->id, 'role' => 'expert']);

        // Bobot pakar SAMA RATA (tidak ada pembobotan) -> weight null.
        $assessment = $assessments->start($project, $p1->id, [$p1->id, $p2->id, $p3->id]);

        $factors = RiskFactor::forContext('IT', 'High')->get()->values();
        // F1..F9 mengikuti urutan posisi (cocok dengan dokumen).
        $F = [];
        foreach ($factors as $i => $f) {
            $F[$i + 1] = $f->id;
        }
        $this->line('Urutan faktor IT/High:');
        foreach ($factors as $i => $f) {
            $this->line(sprintf('  F%d = %s', $i + 1, $f->name));
        }

        $ref = $F[3]; // F3 = Tech_Environment_Stability_Legacy/Unstable

        // Nilai a(F3, Fx) sesuai dokumen (referensi menang -> intensitas; pembanding menang -> 1/intensitas).
        $inputs = [
            $p1->id => [1 => 3, 2 => 2, 4 => 4, 5 => 3, 6 => 5, 7 => 4, 8 => 2, 9 => 1 / 3],
            $p2->id => [1 => 2, 2 => 3, 4 => 1 / 3, 5 => 3, 6 => 5, 7 => 4, 8 => 4, 9 => 1 / 2],
            $p3->id => [1 => 1 / 2, 2 => 4, 4 => 3, 5 => 1 / 2, 6 => 4, 7 => 3, 8 => 2, 9 => 2],
        ];

        $high = $assessment->formulationForLevel('High');
        foreach ($inputs as $uid => $vals) {
            $judgments = [];
            foreach ($vals as $fcode => $value) {
                $judgments[$F[$fcode]] = $value; // key = other_factor_id
            }
            $formulations->submitJudgments($high, $uid, $ref, $judgments);
        }

        $formulations->compute($high);
        $result = $high->fresh()->result;

        // Peta id -> Fcode untuk tampilan.
        $idToCode = array_flip($F);
        $exp = [9 => 0.257296, 3 => 0.178399, 1 => 0.123695, 4 => 0.112384, 5 => 0.108057,
                8 => 0.070798, 2 => 0.061847, 7 => 0.049088, 6 => 0.038435];

        $this->line('');
        $this->line(sprintf('method=%s  cr=%s', $result['method'], $result['cr']));
        $this->line(sprintf('%-4s %-6s %12s %12s %12s', 'Rank', 'Kode', 'Sistem', 'Dokumen', 'Selisih'));
        $sum = 0;
        $maxdiff = 0;
        foreach ($result['factors'] as $row) {
            $code = $idToCode[$row['risk_factor_id']];
            $sys = $row['weight'];
            $doc = $exp[$code];
            $sum += $sys;
            $maxdiff = max($maxdiff, abs($sys - $doc));
            $this->line(sprintf('%-4d F%-5d %12.6f %12.6f %12.6f', $row['rank'], $code, $sys, $doc, abs($sys - $doc)));
        }
        $this->line(sprintf('Total bobot = %.6f | selisih maksimum = %.6f', $sum, $maxdiff));
        $this->line($maxdiff < 0.0001 ? 'HASIL: SESUAI DOKUMEN (dalam toleransi 0,0001)' : 'HASIL: BERBEDA');

        User::whereIn('email', ['tcp1@x.test', 'tcp2@x.test', 'tcp3@x.test'])->delete();
        $this->line('(data uji dibersihkan)');

        return self::SUCCESS;
    }
}
