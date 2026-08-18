@extends('layouts.app')

@section('title', 'Mulai Assessment')

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h3 class="rm-page-title">Mulai Assessment</h3>
        <p class="rm-page-sub mb-0">Proyek <strong>{{ $project->name }}</strong>. Pilih siapa yang akan melakukan AHP. Tiap penilai akan menilai <strong>4 level risiko</strong> (Low, Medium, High, Critical).</p>
    </div>
    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-secondary">← Kembali</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('assessments.store', $project) }}">
            @csrf
            <div class="card">
                <div class="card-header">Pilih Penilai</div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Centang penilai. Anda (manajer) boleh ikut menilai atau menyerahkan sepenuhnya ke anggota.
                        Jika hanya <strong>1 penilai</strong>, dipakai AHP standar (matriks penuh); jika <strong>lebih dari 1</strong>,
                        dipakai AHP-Express dan hasilnya digabung (AIP) memakai bobot expert.
                    </p>
                    @foreach ($members as $m)
                        <div class="form-check py-1 border-bottom">
                            <input class="form-check-input" type="checkbox" name="assessors[]"
                                   value="{{ $m->user_id }}" id="a{{ $m->user_id }}"
                                   {{ $m->role === 'owner' ? 'checked' : '' }}>
                            <label class="form-check-label d-flex justify-content-between" for="a{{ $m->user_id }}" style="width:100%">
                                <span>
                                    <span class="fw-semibold">{{ $m->user->name }}</span>
                                    @if ($m->role === 'owner')
                                        <span class="badge text-bg-primary ms-1">Manajer — Anda</span>
                                    @endif
                                    <span class="text-muted small d-block">{{ $m->user->email }}</span>
                                </span>
                            </label>
                        </div>
                    @endforeach

                    @error('assessors')<div class="text-danger small mt-2">{{ $message }}</div>@enderror

                    <button type="submit" class="btn btn-primary fw-semibold mt-3">Mulai Assessment</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">Bagaimana Prosesnya</div>
            <div class="card-body small text-muted">
                <ol class="ps-3 mb-0">
                    <li class="mb-2">Setiap penilai terpilih mengisi AHP untuk keempat level risiko.</li>
                    <li class="mb-2">Begitu semua penilai menyelesaikan sebuah level, sistem menghitung prioritas &amp; rekomendasi strategi level itu.</li>
                    <li class="mb-2">Assessment selesai saat keempat level terhitung.</li>
                    <li>Hasil rekomendasi dapat dilihat per level (Low, Medium, High, Critical).</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
