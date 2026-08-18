@extends('layouts.app')

@section('title', $project->name)

@section('content')
@php
    $members = $project->members;
    $hasWeights = $project->hasExpertWeights();
    $openAssessment = $project->assessments->firstWhere('status', 'open');
@endphp

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="domain-chip">{{ $project->domain === 'RnD' ? 'R&D' : $project->domain }}</span>
        </div>
        <h3 class="rm-page-title mb-0">{{ $project->name }}</h3>
        <div class="rm-page-sub">Unique ID: <span class="mono-code">{{ $project->code }}</span> · Owner: {{ $project->owner->name }}</div>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">← Dashboard</a>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-8">
        {{-- Overview --}}
        <div class="card mb-4">
            <div class="card-header">Informasi Proyek</div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-sm-3 text-muted">Deskripsi</dt>
                    <dd class="col-sm-9">{{ $project->description ?: '—' }}</dd>
                    <dt class="col-sm-3 text-muted">Tujuan</dt>
                    <dd class="col-sm-9">{{ $project->objective ?: '—' }}</dd>
                    <dt class="col-sm-3 text-muted">Timeline</dt>
                    <dd class="col-sm-9">
                        {{ optional($project->start_date)->format('d M Y') ?: '—' }}
                        &ndash; {{ optional($project->end_date)->format('d M Y') ?: '—' }}
                    </dd>
                </dl>
            </div>
        </div>

        {{-- Assessments --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Assessment — Perumusan Strategi (4 Level)</span>
                @if ($isOwner)
                    <a href="{{ route('assessments.create', $project) }}" class="btn btn-sm btn-primary">＋ Mulai Assessment</a>
                @endif
            </div>
            <div class="card-body p-0">
                @if ($project->assessments->isEmpty())
                    <div class="p-4 text-center text-muted">
                        Belum ada assessment.
                        @if ($isOwner) Klik “Mulai Assessment” untuk memilih penilai dan memulai AHP. @endif
                    </div>
                @else
                    <table class="table align-middle mb-0 small">
                        <thead class="table-light">
                            <tr><th>#</th><th>Status</th><th>Penilai</th><th>Progres</th><th>Dibuat oleh</th><th class="text-end">Aksi</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($project->assessments as $a)
                                <tr>
                                    <td>#{{ $a->id }}</td>
                                    <td>
                                        @if ($a->status === 'completed')
                                            <span class="badge text-bg-success">Selesai</span>
                                        @else
                                            <span class="badge text-bg-warning">Berjalan</span>
                                        @endif
                                    </td>
                                    <td>{{ $a->participants->count() }} orang</td>
                                    <td>{{ $a->formulations->where('status', 'completed')->count() }}/4 level</td>
                                    <td>{{ $a->creator->name }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('assessments.show', $a) }}" class="btn btn-sm btn-outline-primary">Buka</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    {{-- RIGHT --}}
    <div class="col-lg-4">
        @if ($isOwner)
            <div class="card mb-4">
                <div class="card-header">Kontrol Pemilik</div>
                <div class="card-body">
                    <a href="{{ route('weighting.edit', $project) }}" class="btn btn-outline-primary w-100">⚖ Pembobotan Expert</a>
                    <div class="text-center small mt-2">
                        @if ($hasWeights)
                            <span class="text-success">Bobot expert sudah ditetapkan</span>
                        @else
                            <span class="text-muted">Bobot expert: dianggap sama (default)</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Members --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Anggota Proyek</span>
                <span class="badge text-bg-light">{{ $members->count() }}</span>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach ($members as $m)
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold small">{{ $m->user->name }}
                                    @if ($m->role === 'owner')<span class="badge text-bg-primary ms-1">Owner</span>@endif
                                </div>
                                <div class="text-muted small">{{ $m->user->email }}</div>
                            </div>
                            <div class="text-end">
                                @if (!is_null($m->weight))
                                    <div class="mono-code" title="Bobot expert">{{ number_format($m->weight, 3) }}</div>
                                @endif
                                @if ($isOwner && $m->role !== 'owner')
                                    <form method="POST" action="{{ route('members.destroy', [$project, $m]) }}" class="d-inline"
                                          onsubmit="return confirm('Hapus anggota ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-link text-danger p-0 small">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>

                @if ($isOwner)
                    <form method="POST" action="{{ route('members.store', $project) }}" class="mt-3" autocomplete="off">
                        @csrf
                        <label class="form-label small fw-semibold">Tambah Anggota (cari email)</label>
                        <div class="position-relative">
                            <input type="email" name="email" id="member-email" class="form-control form-control-sm"
                                   placeholder="email@perusahaan.com" required>
                            <div id="member-suggest" class="list-group position-absolute w-100 shadow-sm" style="z-index:30"></div>
                        </div>
                        <button class="btn btn-sm btn-primary mt-2 w-100">Tambah Anggota</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">Kontak Pemilik</div>
            <div class="card-body small">
                <div class="fw-semibold">{{ $project->owner->name }}</div>
                <div class="text-muted">{{ $project->owner->email }}</div>
            </div>
        </div>
    </div>
</div>

@if ($isOwner)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('member-email');
    const box = document.getElementById('member-suggest');
    if (!input) return;
    let timer = null;
    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) { box.innerHTML = ''; return; }
        timer = setTimeout(async () => {
            try {
                const res = await fetch(`{{ route('members.lookup', $project) }}?q=` + encodeURIComponent(q));
                const users = await res.json();
                box.innerHTML = users.map(u =>
                    `<button type="button" class="list-group-item list-group-item-action py-1" data-email="${u.email}">
                        <span class="fw-semibold small">${u.name}</span>
                        <span class="text-muted small d-block">${u.email}</span>
                     </button>`).join('');
                box.querySelectorAll('[data-email]').forEach(btn => {
                    btn.addEventListener('click', () => { input.value = btn.dataset.email; box.innerHTML = ''; });
                });
            } catch (e) { box.innerHTML = ''; }
        }, 220);
    });
    document.addEventListener('click', e => { if (!box.contains(e.target) && e.target !== input) box.innerHTML = ''; });
});
</script>
@endif
@endsection
