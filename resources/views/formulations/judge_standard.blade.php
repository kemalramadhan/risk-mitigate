@extends('layouts.app')

@section('title', 'Penilaian AHP Standar')

@php
    $fArr = $factors->values();
    $totalPairs = $fArr->count() * ($fArr->count() - 1) / 2;
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h3 class="rm-page-title">Penilaian Pairwise — AHP Standar</h3>
        <p class="rm-page-sub mb-0">
            Proyek <strong>{{ $project->name }}</strong> ·
            Level @include('partials.risk_badge', ['level' => $formulation->risk_level]) ·
            {{ $totalPairs }} perbandingan
        </p>
    </div>
    <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-sm btn-outline-secondary">← Assessment</a>
</div>

<div class="alert alert-light border small">
    Karena assessment ini hanya memiliki <strong>satu penilai</strong>, digunakan <strong>AHP standar</strong>:
    setiap faktor dibandingkan dengan <strong>setiap faktor lainnya</strong> ({{ $totalPairs }} perbandingan).
    Geser slider ke sisi faktor yang lebih penting (skala Saaty 1–6, tanpa nilai setara).
    Sistem akan menghitung bobot prioritas beserta <strong>Consistency Ratio (CR)</strong>.
</div>

<form method="POST" action="{{ route('judgments.update', $formulation) }}" id="std-form">
    @csrf

    @foreach ($fArr as $a => $fi)
        @if ($a < $fArr->count() - 1)
            <div class="card mb-3">
                <div class="card-header">
                    <div>
                        <span class="fw-semibold">{{ $fi->display_name }}</span>
                        <span class="text-muted small">— dibandingkan dengan faktor berikut</span>
                    </div>
                    @if (!empty($fi->description))
                        <div class="factor-desc mt-1 fw-normal">{{ $fi->description }}</div>
                    @endif
                </div>
                <div class="card-body">
                    @foreach ($fArr as $b => $fj)
                        @if ($b > $a)
                            @php
                                $key = $fi->id.'_'.$fj->id;
                                $prefilled = isset($positions[$key]);
                                $pos = $positions[$key] ?? 1;
                                $idx = $pos > 0 ? 6 - $pos : 5 - $pos;
                                $hid = 'h_'.$fi->id.'_'.$fj->id;
                            @endphp
                            <div class="pairwise-row">
                                <div class="d-flex justify-content-between align-items-start mb-2 gap-3">
                                    <span class="side ref">{{ $fi->display_name }}</span>
                                    <span class="text-muted small pt-1">vs</span>
                                    <span class="side other text-end">{{ $fj->display_name }}</span>
                                </div>
                                @if (!empty($fj->description))
                                    <div class="factor-desc text-end mb-2">{{ $fj->description }}</div>
                                @endif
                                <input type="range" class="pairwise-slider std-slider" min="1" max="10" step="1"
                                       value="{{ $idx }}" data-hidden="{{ $hid }}"
                                       data-touched="{{ $prefilled ? '1' : '0' }}"
                                       data-left="{{ $fi->display_name }}" data-right="{{ $fj->display_name }}">
                                <input type="hidden" id="{{ $hid }}" name="pos[{{ $fi->id }}][{{ $fj->id }}]" value="{{ $pos }}">
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="text-muted" style="font-size:.7rem">◀ {{ $fi->display_name }} lebih penting</span>
                                    <span class="text-muted" style="font-size:.7rem">{{ $fj->display_name }} lebih penting ▶</span>
                                </div>
                                <div class="text-center mt-1 disp" data-for="{{ $hid }}"></div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <div class="text-muted small">Skala Saaty: 2 = sedikit lebih penting · 6 = mutlak lebih penting (tanpa nilai setara)</div>
        <button type="submit" class="btn btn-primary fw-semibold">Simpan &amp; Hitung Hasil</button>
    </div>

    <script type="application/json" id="std-labels">@json($scaleLabels)</script>
</form>

<script>
(function () {
    const labels = JSON.parse(document.getElementById('std-labels').textContent);
    const idxToPos = (idx) => (idx <= 5 ? 6 - idx : 5 - idx);

    document.querySelectorAll('.std-slider').forEach(function (sl) {
        const hidden = document.getElementById(sl.dataset.hidden);
        const disp = document.querySelector('.disp[data-for="' + sl.dataset.hidden + '"]');
        const left = sl.dataset.left, right = sl.dataset.right;
        const update = function () {
            const pos = idxToPos(parseInt(sl.value));
            hidden.value = pos;
            const intensity = Math.abs(pos) + 1;
            const favored = pos > 0 ? left : right;
            disp.innerHTML = '<span class="pairwise-value">' + intensity + '×</span>' +
                '<span class="text-muted small ms-2"><strong>' + favored + '</strong> — ' + (labels[intensity] || '') + '</span>';
        };
        sl.addEventListener('input', function () { sl.dataset.touched = '1'; update(); });
        update();
    });

    // Konfirmasi bila masih ada perbandingan pada nilai default (belum disentuh).
    document.getElementById('std-form').addEventListener('submit', function (e) {
        const all = document.querySelectorAll('.std-slider').length;
        const untouched = document.querySelectorAll('.std-slider[data-touched="0"]').length;
        if (untouched > 0) {
            const ok = confirm(
                untouched + ' dari ' + all + ' perbandingan masih pada nilai default dan belum Anda ubah.\n\n' +
                'Apakah nilai tersebut memang disengaja?\n\n' +
                'OK = lanjut simpan · Batal = tinjau kembali penilaian.'
            );
            if (!ok) { e.preventDefault(); }
        }
    });
})();
</script>
@endsection
