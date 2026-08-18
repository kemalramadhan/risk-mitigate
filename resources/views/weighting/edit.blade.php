@extends('layouts.app')

@section('title', 'Pembobotan Expert')

@section('content')
@php
    $items = $members->map(fn ($m) => [
        'id'       => $m->user_id,
        'title'    => $m->user->name . ($m->role === 'owner' ? ' (Owner)' : ''),
        'subtitle' => $m->user->email,
    ])->values()->all();
@endphp

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h3 class="rm-page-title">Pembobotan Expert</h3>
        <p class="rm-page-sub mb-0">Fitur 2 · Proyek <strong>{{ $project->name }}</strong> — tentukan bobot relatif tiap expert (termasuk Anda). Total bobot otomatis = 1.</p>
    </div>
    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-secondary">← Kembali</a>
</div>

@if (count($items) < 2)
    <div class="alert alert-info">
        Proyek ini baru memiliki satu anggota. Tambahkan expert lain terlebih dahulu untuk melakukan pembobotan.
        Dengan satu anggota, bobot otomatis = 1.
    </div>
@else
    <div class="row g-4">
        <div class="col-lg-8">
            @include('partials.ahp_express_form', [
                'formAction'     => route('weighting.update', $project),
                'referenceField' => 'reference_user_id',
                'items'          => $items,
                'referenceId'    => $referenceId,
                'positions'      => $positions,
                'scaleLabels'    => $scaleLabels,
                'submitLabel'    => 'Hitung & Simpan Bobot',
            ])
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Bobot Saat Ini</div>
                <div class="card-body">
                    @if ($hasWeights)
                        <ul class="list-unstyled mb-0 small">
                            @foreach ($members as $m)
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span>{{ $m->user->name }}</span>
                                    <span class="mono-code">{{ number_format($m->weight, 4) }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <form method="POST" action="{{ route('weighting.reset', $project) }}" class="mt-3"
                              onsubmit="return confirm('Reset pembobotan menjadi sama rata?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger w-100">Reset ke Bobot Sama</button>
                        </form>
                    @else
                        <p class="text-muted small mb-0">Belum ada pembobotan. Saat ini bobot setiap expert dianggap sama.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
