@extends('layouts.app')

@section('title', 'Penilaian AHP')

@section('content')
@php
    $items = $factors->map(fn ($f) => [
        'id'       => $f->id,
        'title'    => $f->display_name,
        'subtitle' => $f->description ?? '',
    ])->values()->all();
@endphp

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h3 class="rm-page-title">Penilaian Pairwise (AHP-Express)</h3>
        <p class="rm-page-sub mb-0">
            Proyek <strong>{{ $project->name }}</strong> ·
            Level @include('partials.risk_badge', ['level' => $formulation->risk_level]) ·
            bandingkan {{ count($items) }} faktor risiko.
        </p>
    </div>
    <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-sm btn-outline-secondary">← Assessment</a>
</div>

<div class="alert alert-light border small">
    Pilih satu <strong>faktor referensi</strong>, lalu geser slider untuk menyatakan seberapa penting referensi dibanding tiap faktor lain
    (skala Saaty 1–6). Sisa perbandingan dihitung otomatis oleh sistem sehingga konsistensi selalu sempurna.
</div>

@include('partials.ahp_express_form', [
    'formAction'     => route('judgments.update', $formulation),
    'referenceField' => 'reference_factor_id',
    'items'          => $items,
    'referenceId'    => $referenceId,
    'positions'      => $positions,
    'scaleLabels'    => $scaleLabels,
    'submitLabel'    => 'Simpan Penilaian Saya',
])
@endsection
