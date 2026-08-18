@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
        <h3 class="rm-page-title">Dashboard</h3>
        <p class="rm-page-sub mb-0">Ringkasan proyek yang Anda miliki dan proyek tempat Anda menjadi expert.</p>
    </div>
    <a href="{{ route('projects.create') }}" class="btn btn-primary fw-semibold">＋ Daftarkan Proyek</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-4">
        <div class="stat-tile">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Proyek</div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="stat-tile">
            <div class="stat-value">{{ $stats['owned'] }}</div>
            <div class="stat-label">Sebagai Manajer</div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="stat-tile">
            <div class="stat-value">{{ $stats['shared'] }}</div>
            <div class="stat-label">Sebagai Penilai</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Proyek</span>
        <a href="{{ route('projects.search') }}" class="small text-decoration-none">Cari proyek →</a>
    </div>
    <div class="card-body p-0">
        @if ($projects->isEmpty())
            <div class="text-center text-muted py-5">
                <div style="font-size:2.5rem">▦</div>
                <p class="mb-2">Belum ada proyek.</p>
                <a href="{{ route('projects.create') }}" class="btn btn-sm btn-outline-primary">Daftarkan proyek pertama</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Proyek</th>
                            <th>Unique ID</th>
                            <th>Domain</th>
                            <th>Assessment</th>
                            <th>Peran</th>
                            <th>Anggota</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $project->name }}</div>
                                    <div class="small text-muted text-truncate" style="max-width:280px">{{ $project->description }}</div>
                                </td>
                                <td><span class="mono-code">{{ $project->code }}</span></td>
                                <td><span class="domain-chip">{{ $project->domain === 'RnD' ? 'R&D' : $project->domain }}</span></td>
                                <td>{{ $project->assessments_count }}</td>
                                <td>
                                    @if ($project->owner_id === auth()->id())
                                        <span class="badge text-bg-primary">Owner</span>
                                    @else
                                        <span class="badge text-bg-secondary">Expert</span>
                                    @endif
                                </td>
                                <td>{{ $project->members_count }}</td>
                                <td class="text-end">
                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">Buka</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
