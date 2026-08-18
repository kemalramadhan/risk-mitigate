{{--
    Field password dengan tombol ikon mata untuk toggle visibilitas.
    Params: $id, $name, $label, $autocomplete (opsional)
--}}
@php
    $pid   = $id ?? 'password';
    $pname = $name ?? 'password';
    $plabel = $label ?? 'Password';
    $pauto = $autocomplete ?? 'current-password';
@endphp

<label for="{{ $pid }}" class="form-label small fw-semibold">{{ $plabel }}</label>
<div class="input-group has-validation">
    <input id="{{ $pid }}" type="password"
           class="form-control @error($pname) is-invalid @enderror"
           name="{{ $pname }}" autocomplete="{{ $pauto }}" required>
    <button class="btn btn-outline-secondary pw-toggle" type="button"
            data-pw-target="{{ $pid }}" aria-label="Tampilkan password" tabindex="-1">
        <svg class="eye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
        </svg>
        <svg class="eye-off" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c6.5 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3.5 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
            <path d="M14.12 14.12A3 3 0 1 1 9.88 9.88"/><path d="m2 2 20 20"/>
        </svg>
    </button>
    @error($pname)<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
