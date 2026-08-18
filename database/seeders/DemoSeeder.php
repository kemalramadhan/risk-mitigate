<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Formulation;
use App\Models\Project;
use App\Models\RiskFactor;
use App\Models\User;
use App\Services\AssessmentService;
use App\Services\ExpertWeightingService;
use App\Services\FormulationService;
use App\Support\SaatyScale;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Data demo: akun + proyek + assessment (perumusan 4 level).
 *   php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    private ExpertWeightingService $weighting;
    private AssessmentService $assessments;
    private FormulationService $formulations;

    public function run(): void
    {
        $this->weighting = app(ExpertWeightingService::class);
        $this->assessments = app(AssessmentService::class);
        $this->formulations = app(FormulationService::class);

        if (RiskFactor::count() === 0) {
            $this->command->error('Knowledge Base kosong. Jalankan: php artisan db:seed --class=KnowledgeBaseSeeder');

            return;
        }

        User::whereIn('email', ['budi@demo.com', 'sari@demo.com', 'andi@demo.com', 'rina@demo.com'])->delete();

        $budi = $this->user('Budi Santoso', 'budi@demo.com');
        $sari = $this->user('Sari Dewi', 'sari@demo.com');
        $andi = $this->user('Andi Wijaya', 'andi@demo.com');
        $rina = $this->user('Rina Putri', 'rina@demo.com');

        // 1) IT · Assessment GRUP selesai (3 penilai, AHP-Express + bobot expert).
        $p1 = $this->project($budi, 'IT', 'Migrasi Core Banking System',
            'Migrasi sistem inti perbankan dari platform legacy ke arsitektur modern.');
        $this->addMember($p1, $sari);
        $this->addMember($p1, $andi);
        $this->weighting->computeAndStore($p1, $budi->id, [
            $sari->id => SaatyScale::positionToValue(2),
            $andi->id => SaatyScale::positionToValue(1),
        ]);
        $a1 = $this->assessments->start($p1, $budi->id, [$budi->id, $sari->id, $andi->id]);
        $this->fillAll($a1, [$budi, $sari, $andi]);
        $this->assessments->computeReady($a1);

        // 2) R&D · Assessment SINGLE selesai (1 penilai, AHP standar per level).
        $p2 = $this->project($sari, 'RnD', 'Pengembangan Vaksin mRNA Generasi Baru',
            'Riset platform vaksin mRNA dengan target stabilitas termal tinggi.');
        $a2 = $this->assessments->start($p2, $sari->id, [$sari->id]);
        $this->fillAll($a2, [$sari]);
        $this->assessments->computeReady($a2);

        // 3) IT · Assessment BERJALAN (2 penilai; Budi selesai, Rina belum).
        $p3 = $this->project($budi, 'IT', 'Company Profile Website Revamp',
            'Perancangan ulang website profil perusahaan dengan teknologi modern.');
        $this->addMember($p3, $rina);
        $a3 = $this->assessments->start($p3, $budi->id, [$budi->id, $rina->id]);
        $this->fillAll($a3, [$budi]); // Rina belum menilai -> belum ada level terhitung

        // 4) IT · Belum ada assessment (demonstrasi mulai assessment).
        $p4 = $this->project($andi, 'IT', 'Real-time Fraud Detection Engine',
            'Mesin deteksi fraud real-time berbasis machine learning.');
        $this->addMember($p4, $budi);

        $this->command->info('DemoSeeder selesai.');
        $this->command->line('  Akun (password: <info>password</info>): budi@, sari@, andi@, rina@ demo.com');
        $this->command->line('  Login <info>budi@demo.com</info> -> proyek grup selesai + assessment berjalan.');
    }

    /** Isi penilaian seluruh level oleh daftar penilai, sesuai mode formulation. */
    private function fillAll(Assessment $assessment, array $users): void
    {
        foreach ($assessment->formulations as $f) {
            foreach ($users as $idx => $u) {
                if ($f->isStandard()) {
                    $this->submitStandard($f, $u);
                } else {
                    $this->submitExpress($f, $u, $idx);
                }
            }
        }
    }

    private function submitExpress(Formulation $f, User $u, int $offset): void
    {
        $factors = RiskFactor::forContext($f->project->domain, $f->risk_level)->get()->values();
        $ref = $factors->first()->id;
        $pattern = [2, -1, 3, 1, -2, 2, 1, -1, 3];

        $judgments = [];
        foreach ($factors as $i => $fac) {
            if ($fac->id === $ref) {
                continue;
            }
            $pos = $pattern[($i + $offset) % count($pattern)];
            $judgments[$fac->id] = SaatyScale::positionToValue($pos);
        }
        $this->formulations->submitJudgments($f, $u->id, $ref, $judgments);
    }

    private function submitStandard(Formulation $f, User $u): void
    {
        $factors = RiskFactor::forContext($f->project->domain, $f->risk_level)->get()->values();
        $w = [0.20, 0.17, 0.14, 0.12, 0.10, 0.09, 0.07, 0.06, 0.05];

        $comparisons = [];
        foreach ($factors as $a => $fi) {
            foreach ($factors as $b => $fj) {
                if ($b > $a) {
                    $comparisons[] = ['i' => $fi->id, 'j' => $fj->id, 'value' => $w[$a] / $w[$b]];
                }
            }
        }
        $this->formulations->submitStandardMatrix($f, $u->id, $comparisons);
    }

    private function user(string $name, string $email): User
    {
        return User::create(['name' => $name, 'email' => $email, 'password' => Hash::make('password')]);
    }

    private function project(User $owner, string $domain, string $name, string $desc): Project
    {
        $project = Project::create([
            'code'        => 'RM-'.strtoupper($domain).'-'.strtoupper(Str::random(6)),
            'owner_id'    => $owner->id,
            'name'        => $name,
            'description' => $desc,
            'domain'      => $domain,
            'objective'   => 'Menekan risiko proyek melalui strategi mitigasi berbasis prioritas AHP.',
            'start_date'  => now()->subMonth(),
            'end_date'    => now()->addMonths(5),
        ]);
        $project->members()->create(['user_id' => $owner->id, 'role' => 'owner']);

        return $project;
    }

    private function addMember(Project $project, User $user): void
    {
        $project->members()->create(['user_id' => $user->id, 'role' => 'expert']);
    }
}
