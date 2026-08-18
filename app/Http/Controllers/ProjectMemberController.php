<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectMemberController extends Controller
{

    /** Cari user berdasarkan email (untuk menambah anggota). */
    public function lookup(Request $request, Project $project)
    {
        $this->authorizeOwner($request, $project);

        $q = trim((string) $request->query('q', ''));
        $existingIds = $project->members()->pluck('user_id');

        $users = collect();
        if (strlen($q) >= 2) {
            $users = User::query()
                ->where('email', 'like', "%{$q}%")
                ->whereNotIn('id', $existingIds)
                ->limit(8)
                ->get(['id', 'name', 'email']);
        }

        return response()->json($users);
    }

    /** Tambah anggota (expert) berdasarkan email unik. */
    public function store(Request $request, Project $project)
    {
        $this->authorizeOwner($request, $project);

        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Pengguna tidak ditemukan.']);
        }

        if ($project->members()->where('user_id', $user->id)->exists()) {
            return back()->withErrors(['email' => 'Anggota sudah terdaftar.']);
        }

        $project->members()->create([
            'user_id' => $user->id,
            'role'    => 'expert',
        ]);

        $this->onMembershipChanged($project);

        return back()->with('status', "{$user->name} ditambahkan sebagai expert.");
    }

    public function destroy(Request $request, Project $project, ProjectMember $member)
    {
        $this->authorizeOwner($request, $project);

        abort_if($member->project_id !== $project->id, 404);

        if ($member->role === 'owner') {
            return back()->withErrors(['member' => 'Pemilik proyek tidak dapat dihapus.']);
        }

        $member->delete();
        $this->onMembershipChanged($project);

        return back()->with('status', 'Anggota dihapus dari proyek.');
    }

    /** Enable/disable proyek untuk perumusan strategi oleh anggota. */
    public function toggleEnable(Request $request, Project $project)
    {
        $this->authorizeOwner($request, $project);

        $project->update(['mitigation_enabled' => ! $project->mitigation_enabled]);

        $msg = $project->mitigation_enabled
            ? 'Proyek di-enable untuk perumusan strategi mitigasi.'
            : 'Proyek dinonaktifkan untuk perumusan strategi mitigasi.';

        return back()->with('status', $msg);
    }

    /**
     * Perubahan komposisi anggota mereset pembobotan expert (komposisi DM
     * berubah sehingga bobot lama tidak lagi valid). Assessment yang sedang
     * berjalan TIDAK terpengaruh karena penilainya dipilih eksplisit.
     */
    private function onMembershipChanged(Project $project): void
    {
        $project->expertWeightJudgments()->delete();
        $project->members()->update(['weight' => null]);
    }

    private function authorizeOwner(Request $request, Project $project): void
    {
        abort_unless(
            $project->isOwner($request->user()),
            403,
            'Hanya pemilik proyek yang dapat melakukan aksi ini.'
        );
    }
}
