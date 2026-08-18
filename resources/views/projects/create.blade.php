@extends('layouts.app')

@section('title', 'Daftarkan Proyek')

@section('content')
<div class="mb-4">
    <h3 class="rm-page-title">Daftarkan Proyek Baru</h3>
    <p class="rm-page-sub mb-0">Domain <span class="domain-chip">{{ $domainLabel }}</span>. Isi informasi umum proyek. Level risiko tidak ditentukan di sini — perumusan strategi dilakukan untuk keempat level (Low, Medium, High, Critical) pada tahap assessment.</p>
</div>

<form method="POST" action="{{ route('projects.store') }}">
    @csrf
    <input type="hidden" name="domain" value="{{ $domain }}">

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Informasi Umum Proyek</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Proyek <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Deskripsi</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tujuan Proyek</label>
                        <textarea name="objective" rows="2" class="form-control">{{ old('objective') }}</textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6 mb-1">
                            <label class="form-label small fw-semibold">Mulai (Timeline)</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control">
                        </div>
                        <div class="col-md-6 mb-1">
                            <label class="form-label small fw-semibold">Selesai (Timeline)</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control @error('end_date') is-invalid @enderror">
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Langkah Berikutnya</div>
                <div class="card-body small text-muted">
                    <ol class="mb-3 ps-3">
                        <li>Daftarkan proyek.</li>
                        <li>Tambahkan anggota (opsional) sebagai penilai.</li>
                        <li>Mulai <strong>Assessment</strong>: pilih penilai (Anda dan/atau anggota).</li>
                        <li>Penilai melakukan AHP untuk <strong>4 level</strong>.</li>
                        <li>Lihat rekomendasi strategi per level.</li>
                    </ol>
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">Daftarkan Proyek</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
