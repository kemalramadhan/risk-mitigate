@extends('layouts.app')

@section('title', 'Assessment #'.$assessment->id)

@section('content')
@php
    // Apakah ada level yang siap dihitung (semua penilai submit) tapi belum dihitung.
    $anyReady = false;
    foreach ($byLevel as $f) {
        if ($f && $f->status !== 'completed' && $f->allSubmitted()) { $anyReady = true; break; }
    }
    $canCompute = ($isOwner || $assessment->created_by === $userId) && $anyReady;
@endphp

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="domain-chip">{{ $project->domain === 'RnD' ? 'R&D' : $project->domain }}</span>
            @if ($assessment->isCompleted())
                <span class="badge text-bg-success">Selesai</span>
            @else
                <span class="badge text-bg-warning">Berjalan</span>
            @endif
        </div>
        <h3 class="rm-page-title mb-0">Assessment #{{ $assessment->id }} — {{ $project->name }}</h3>
        <div class="rm-page-sub">
            <span class="mono-code">{{ $project->code }}</span> ·
            {{ $assessment->participants->count() }} penilai ·
            {{ $assessment->formulations->where('status','completed')->count() }}/4 level terhitung
        </div>
    </div>
    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-secondary">← Proyek</a>
</div>

@if ($canCompute)
    <div class="alert alert-success d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>Ada level yang penilaiannya sudah lengkap dan siap dihitung.</span>
        <form method="POST" action="{{ route('assessments.compute', $assessment) }}">
            @csrf
            <button class="btn btn-success btn-sm">Hitung Level yang Siap</button>
        </form>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-9">
        @php $anyCompleted = collect($byLevel)->contains(fn ($f) => $f && $f->status === 'completed'); @endphp
        @if ($anyCompleted)
            <div class="d-flex justify-content-end mb-2">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="toggle-sources" role="switch">
                    <label class="form-check-label small text-muted" for="toggle-sources">Tampilkan sumber referensi</label>
                </div>
            </div>
        @endif

        {{-- Tabs per level --}}
        <ul class="nav nav-tabs" role="tablist">
            @foreach ($levels as $i => $level)
                @php $f = $byLevel[$level]; @endphp
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $i === 0 ? 'active' : '' }}" data-bs-toggle="tab"
                            data-bs-target="#lvl-{{ $level }}" type="button" role="tab">
                        @include('partials.risk_badge', ['level' => $level])
                        @if ($f && $f->status === 'completed')
                            <span class="ms-1 text-success">✓</span>
                        @endif
                    </button>
                </li>
            @endforeach
        </ul>

        <div id="results-tabs" class="tab-content border border-top-0 rounded-bottom p-3 bg-white sources-hidden">
            @foreach ($levels as $i => $level)
                @php
                    $f = $byLevel[$level];
                    $me = $f ? $f->participants->firstWhere('user_id', $userId) : null;
                    $submitted = $f ? $f->participants->whereNotNull('submitted_at')->count() : 0;
                    $total = $f ? $f->participants->count() : 0;
                @endphp
                <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}" id="lvl-{{ $level }}" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="fw-semibold">Level @include('partials.risk_badge', ['level' => $level])</span>
                            <span class="text-muted small ms-2">{{ $submitted }}/{{ $total }} penilai selesai</span>
                        </div>
                        @if ($isAssessor && $f && $f->status !== 'completed')
                            <a href="{{ route('judgments.edit', $f) }}" class="btn btn-sm {{ ($me && $me->submitted_at) ? 'btn-outline-primary' : 'btn-primary' }}">
                                {{ ($me && $me->submitted_at) ? 'Ubah Penilaian' : 'Isi Penilaian' }}
                            </a>
                        @endif
                    </div>

                    @if ($f && $f->status === 'completed' && $f->result)
                        @include('partials.level_result', ['result' => $f->result])
                    @else
                        @php $pending = $total - $submitted; @endphp
                        <div class="alert alert-warning mb-0">
                            <div class="fw-semibold mb-1">Laporan level {{ $level }} belum tersedia.</div>
                            @if ($isAssessor && $me && ! $me->submitted_at)
                                Selesaikan penilaian AHP terlebih dahulu.
                            @elseif ($pending > 0)
                                Menunggu semua partisipan menyelesaikan level ini
                                ({{ $submitted }}/{{ $total }} selesai).
                            @else
                                Penilaian lengkap — hasil siap dihitung oleh pemilik proyek.
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Sidebar: penilai + progress --}}
    <div class="col-lg-3">
        <div class="card mb-4">
            <div class="card-header">Penilai</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach ($assessment->participants as $p)
                        <li class="list-group-item small">
                            <div class="fw-semibold">{{ $p->user->name }}</div>
                            @php
                                $done = 0;
                                foreach ($byLevel as $ff) {
                                    if ($ff && $ff->participants->firstWhere('user_id', $p->user_id)?->submitted_at) $done++;
                                }
                            @endphp
                            <div class="text-muted">{{ $done }}/4 level dinilai</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const cb = document.getElementById('toggle-sources');
    const box = document.getElementById('results-tabs');
    if (!cb || !box) return;
    const KEY = 'rm_show_sources';
    const shown = localStorage.getItem(KEY) === '1'; // default: tersembunyi
    cb.checked = shown;
    box.classList.toggle('sources-hidden', !shown);
    cb.addEventListener('change', function () {
        box.classList.toggle('sources-hidden', !cb.checked);
        localStorage.setItem(KEY, cb.checked ? '1' : '0');
    });
})();
</script>
@endsection
