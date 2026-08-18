@extends('layouts.app')

@section('title', 'Cari Proyek')

@section('content')
<div class="mb-4">
    <h3 class="rm-page-title">Cari Proyek</h3>
    <p class="rm-page-sub mb-0">Temukan proyek berdasarkan nama atau Unique ID untuk melakukan perumusan strategi mitigasi.</p>
</div>

<form method="GET" action="{{ route('projects.search') }}" class="card card-body mb-4">
    <div class="input-group">
        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Nama proyek atau Unique ID (mis. RM-IT-A1B2C3)" autofocus>
        <button class="btn btn-primary">Cari</button>
    </div>
</form>

@if ($q !== '')
    <div class="card">
        <div class="card-header">Hasil untuk “{{ $q }}” ({{ $results->count() }})</div>
        <div class="card-body p-0">
            @if ($results->isEmpty())
                <div class="p-4 text-center text-muted">Tidak ada proyek cocok yang dapat Anda akses.</div>
            @else
                <table class="table align-middle mb-0">
                    <thead class="table-light"><tr><th>Proyek</th><th>Unique ID</th><th>Domain</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($results as $project)
                            <tr>
                                <td class="fw-semibold">{{ $project->name }}</td>
                                <td><span class="mono-code">{{ $project->code }}</span></td>
                                <td><span class="domain-chip">{{ $project->domain === 'RnD' ? 'R&D' : $project->domain }}</span></td>
                                <td class="text-end"><a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">Buka</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endif
@endsection
