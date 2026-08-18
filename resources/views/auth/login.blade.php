@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="rm-auth-card">
    <div class="rm-auth-head">
        <div class="rm-logo-lg">RM</div>
        <h4 class="fw-bold mb-1" style="color:#0f2942">Risk Mitigate</h4>
        <p class="text-muted small mb-0">Sistem Pendukung Keputusan Mitigasi Risiko Proyek</p>
    </div>
    <div class="p-4 pt-2">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold">Email</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}" required autofocus placeholder="nama@perusahaan.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                @include('partials.password_field', [
                    'id' => 'password', 'name' => 'password',
                    'label' => 'Password', 'autocomplete' => 'current-password',
                ])
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label small" for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-semibold">Masuk</button>
        </form>
        <div class="text-center mt-3 small text-muted">
            Belum punya akun? <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Daftar di sini</a>
        </div>
    </div>
</div>
@endsection
