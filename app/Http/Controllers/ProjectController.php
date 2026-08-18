<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    /** Domain yang tersedia. */
    private const DOMAINS = ['IT' => 'IT', 'RnD' => 'R&D'];

    /** Form pendaftaran proyek (tanpa triage/level risiko). */
    public function create(Request $request)
    {
        $domain = $request->query('domain');

        if (! $domain || ! array_key_exists($domain, self::DOMAINS)) {
            return view('projects.choose_domain', ['domains' => self::DOMAINS]);
        }

        return view('projects.create', [
            'domain'      => $domain,
            'domainLabel' => self::DOMAINS[$domain],
        ]);
    }

    /** Simpan proyek. Level risiko tidak ditentukan di sini. */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain'      => 'required|string',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'objective'   => 'nullable|string',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ], [
            'name.required' => 'Nama proyek wajib diisi.',
            'name.max'      => 'Nama proyek maksimal 255 karakter.',
        ]);

        if (! array_key_exists($validated['domain'], self::DOMAINS)) {
            throw ValidationException::withMessages(['domain' => 'Domain tidak valid.']);
        }

        $project = Project::create([
            'code'               => $this->generateCode($validated['domain']),
            'owner_id'           => $request->user()->id,
            'name'               => $validated['name'],
            'description'        => $validated['description'] ?? null,
            'domain'             => $validated['domain'],
            'objective'          => $validated['objective'] ?? null,
            'start_date'         => $validated['start_date'] ?? null,
            'end_date'           => $validated['end_date'] ?? null,
            'mitigation_enabled' => false,
        ]);

        $project->members()->create([
            'user_id' => $request->user()->id,
            'role'    => 'owner',
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', 'Proyek berhasil didaftarkan.');
    }

    public function show(Request $request, Project $project)
    {
        $this->authorizeMember($request, $project);

        $project->load([
            'owner',
            'members.user',
            'assessments.creator',
            'assessments.participants.user',
            'assessments.formulations',
        ]);

        return view('projects.show', [
            'project' => $project,
            'isOwner' => $project->isOwner($request->user()),
        ]);
    }

    /** Cari proyek berdasarkan nama atau unique ID. */
    public function search(Request $request)
    {
        $user = $request->user();
        $q = trim((string) $request->query('q', ''));

        $results = collect();
        if ($q !== '') {
            $memberProjectIds = $user->memberships()->pluck('project_id');

            $results = Project::query()
                ->with('owner')
                ->whereIn('id', $memberProjectIds)
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%");
                })
                ->latest()
                ->get();
        }

        return view('projects.search', compact('results', 'q'));
    }

    private function generateCode(string $domain): string
    {
        do {
            $code = 'RM-'.strtoupper($domain).'-'.strtoupper(Str::random(6));
        } while (Project::where('code', $code)->exists());

        return $code;
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
