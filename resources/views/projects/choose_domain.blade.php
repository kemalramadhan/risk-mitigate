@extends('layouts.app')

@section('title', 'Pilih Domain')

@section('content')
<div class="mb-4">
    <h3 class="rm-page-title">Daftarkan Proyek Baru</h3>
    <p class="rm-page-sub mb-0">Langkah 1 dari 2 — Pilih domain proyek. Domain menentukan karakteristik triage dan basis pengetahuan strategi mitigasi.</p>
</div>

<div class="row g-3">
    @foreach ($domains as $code => $label)
        <div class="col-md-6">
            <a href="{{ route('projects.create', ['domain' => $code]) }}" class="text-decoration-none">
                <div class="card h-100 border-2" style="transition:transform .1s">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="rm-logo" style="width:44px;height:44px;font-size:1rem;border-radius:12px">
                                {{ $code === 'RnD' ? 'R&D' : $code }}
                            </span>
                            <h5 class="mb-0 fw-bold" style="color:#0f2942">Domain {{ $label }}</h5>
                        </div>
                        <p class="text-muted small mb-0">
                            @if ($code === 'IT')
                                Proyek Teknologi Informasi — dinilai melalui kematangan proses, pengalaman tim, dan stabilitas lingkungan teknologi.
                            @else
                                Proyek Research &amp; Development — dinilai melalui kematangan proses, pengalaman manajer proyek, stabilitas kebutuhan, dan volatilitas industri.
                            @endif
                        </p>
                        <div class="mt-3"><span class="btn btn-sm btn-outline-primary">Pilih {{ $label }} →</span></div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endsection
