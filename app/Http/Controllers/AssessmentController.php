<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Project;
use App\Services\AssessmentService;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function __construct(private AssessmentService $assessments)
    {
    }

    /** Form pemilihan penilai (manajer + anggota). */
    public function create(Request $request, Project $project)
    {
        $this->authorizeOwner($request, $project);

        $project->load('members.user');

        return view('assessments.create', [
            'project' => $project,
            'members' => $project->members, // termasuk owner (role owner)
        ]);
    }

    /** Mulai assessment dengan penilai terpilih. */
    public function store(Request $request, Project $project)
    {
        $this->authorizeOwner($request, $project);

        $memberIds = $project->members()->pluck('user_id')->map(fn ($v) => (int) $v)->all();

        $validated = $request->validate([
            'assessors'   => 'required|array|min:1',
            'assessors.*' => 'integer|in:'.implode(',', $memberIds),
        ]);

        $assessment = $this->assessments->start(
            $project,
            $request->user()->id,
            $validated['assessors']
        );

        return redirect()
            ->route('assessments.show', $assessment)
            ->with('status', 'Assessment dimulai. Penilai dapat mulai melakukan AHP untuk keempat level.');
    }

    public function show(Request $request, Assessment $assessment)
    {
        $assessment->load([
            'project.owner',
            'creator',
            'participants.user',
            'formulations.participants.user',
        ]);

        $project = $assessment->project;
        $this->authorizeMember($request, $project);

        // Formulation per level (terurut Low..Critical).
        $byLevel = [];
        foreach (Assessment::LEVELS as $level) {
            $byLevel[$level] = $assessment->formulationForLevel($level);
        }

        $userId = $request->user()->id;
        $isAssessor = $assessment->isParticipant($request->user());

        return view('assessments.show', [
            'assessment' => $assessment,
            'project'    => $project,
            'byLevel'    => $byLevel,
            'levels'     => Assessment::LEVELS,
            'isOwner'    => $project->isOwner($request->user()),
            'isAssessor' => $isAssessor,
            'userId'     => $userId,
        ]);
    }

    /** Hitung seluruh level yang penilaiannya sudah lengkap. */
    public function compute(Request $request, Assessment $assessment)
    {
        $project = $assessment->project;

        abort_unless(
            $project->isOwner($request->user()) || $assessment->created_by === $request->user()->id,
            403,
            'Hanya pemilik proyek atau pembuat assessment yang dapat menghitung hasil.'
        );

        try {
            $this->assessments->computeReady($assessment);
        } catch (\Throwable $e) {
            return back()->withErrors(['compute' => $e->getMessage()]);
        }

        return redirect()
            ->route('assessments.show', $assessment)
            ->with('status', 'Level yang penilaiannya lengkap telah dihitung.');
    }

    private function authorizeOwner(Request $request, Project $project): void
    {
        abort_unless($project->isOwner($request->user()), 403, 'Hanya pemilik proyek yang dapat melakukan aksi ini.');
    }

    private function authorizeMember(Request $request, Project $project): void
    {
        abort_unless(
            $project->isOwner($request->user()) || $project->hasMember($request->user()),
            403,
            'Anda tidak memiliki akses ke proyek ini.'
        );
    }
}
