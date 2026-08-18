@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
<div class="rm-auth-card">
    <div class="rm-auth-head">
        <div class="rm-logo-lg">RM</div>
        <h4 class="fw-bold mb-1" style="color:#0f2942">Buat Akun</h4>
        <p class="text-muted small mb-0">Gunakan email unik yang belum pernah digunakan</p>
    </div>
    <div class="p-4 pt-2">
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label small fw-semibold">Nama</label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                       name="name" value="{{ old('name') }}" required autofocus>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold">Email</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}" required placeholder="nama@perusahaan.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-2">
                <div class="col-md-6 mb-3">
                    @include('partials.password_field', [
                        'id' => 'password', 'name' => 'password',
                        'label' => 'Password', 'autocomplete' => 'new-password',
                    ])
                </div>
                <div class="col-md-6 mb-3">
                    @include('partials.password_field', [
                        'id' => 'password-confirm', 'name' => 'password_confirmation',
                        'label' => 'Konfirmasi', 'autocomplete' => 'new-password',
                    ])
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-semibold">Daftar</button>
        </form>
        <div class="text-center mt-3 small text-muted">
            Sudah punya akun? <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Masuk</a>
        </div>
    </div>
</div>
@endsection
