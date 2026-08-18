<?php

/*
|--------------------------------------------------------------------------
| Pesan Validasi (Bahasa Indonesia)
|--------------------------------------------------------------------------
| Kunci yang tidak didefinisikan di sini otomatis jatuh ke fallback locale (en).
*/

return [

    'accepted'             => ':attribute harus disetujui.',
    'active_url'           => ':attribute bukan URL yang valid.',
    'after'                => ':attribute harus tanggal setelah :date.',
    'after_or_equal'       => ':attribute harus tanggal setelah atau sama dengan :date.',
    'alpha'                => ':attribute hanya boleh berisi huruf.',
    'alpha_dash'           => ':attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num'            => ':attribute hanya boleh berisi huruf dan angka.',
    'array'                => ':attribute harus berupa daftar.',
    'before'               => ':attribute harus tanggal sebelum :date.',
    'before_or_equal'      => ':attribute harus tanggal sebelum atau sama dengan :date.',
    'boolean'              => ':attribute harus bernilai benar atau salah.',
    'confirmed'            => 'Konfirmasi :attribute tidak cocok.',
    'date'                 => ':attribute bukan tanggal yang valid.',
    'date_equals'          => ':attribute harus tanggal yang sama dengan :date.',
    'different'            => ':attribute dan :other harus berbeda.',
    'digits'               => ':attribute harus terdiri dari :digits angka.',
    'email'                => 'Format :attribute tidak valid.',
    'exists'               => ':attribute yang dipilih tidak valid.',
    'filled'               => ':attribute wajib diisi.',
    'in'                   => ':attribute yang dipilih tidak valid.',
    'integer'              => ':attribute harus berupa angka bulat.',
    'not_in'               => ':attribute yang dipilih tidak valid.',
    'numeric'              => ':attribute harus berupa angka.',
    'present'              => ':attribute harus ada.',
    'required'             => ':attribute wajib diisi.',
    'required_if'          => ':attribute wajib diisi bila :other adalah :value.',
    'required_with'        => ':attribute wajib diisi bila terdapat :values.',
    'same'                 => ':attribute dan :other harus sama.',
    'string'               => ':attribute harus berupa teks.',
    'unique'               => ':attribute sudah terdaftar.',
    'uploaded'             => ':attribute gagal diunggah.',
    'url'                  => 'Format :attribute tidak valid.',

    'between' => [
        'numeric' => ':attribute harus bernilai antara :min dan :max.',
        'file'    => 'Ukuran :attribute harus antara :min dan :max kilobyte.',
        'string'  => ':attribute harus terdiri dari :min sampai :max karakter.',
        'array'   => ':attribute harus memiliki :min sampai :max item.',
    ],

    'max' => [
        'numeric' => ':attribute maksimal bernilai :max.',
        'file'    => 'Ukuran :attribute maksimal :max kilobyte.',
        'string'  => ':attribute maksimal :max karakter.',
        'array'   => ':attribute maksimal memiliki :max item.',
    ],

    'min' => [
        'numeric' => ':attribute minimal bernilai :min.',
        'file'    => 'Ukuran :attribute minimal :min kilobyte.',
        'string'  => ':attribute minimal :min karakter.',
        'array'   => ':attribute minimal harus memilih :min item.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pesan Kustom per Atribut
    |--------------------------------------------------------------------------
    */
    'custom' => [
        'assessors' => [
            'required' => 'Pilih minimal satu partisipan.',
            'min'      => 'Pilih minimal satu partisipan.',
        ],
        'assessors.*' => [
            'in' => 'Partisipan yang dipilih bukan anggota proyek ini.',
        ],
        'password' => [
            'min' => 'Password minimal :min karakter.',
        ],
        'email' => [
            'unique' => 'Email sudah terdaftar.',
            'email'  => 'Format email tidak valid.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nama Atribut
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'name'                  => 'Nama',
        'email'                 => 'Email',
        'password'              => 'Password',
        'password_confirmation' => 'Konfirmasi password',
        'domain'                => 'Domain',
        'description'           => 'Deskripsi',
        'objective'             => 'Tujuan proyek',
        'start_date'            => 'Tanggal mulai',
        'end_date'              => 'Tanggal selesai',
        'assessors'             => 'Partisipan',
        'reference_factor_id'   => 'Faktor referensi',
        'reference_user_id'     => 'Penilai referensi',
        'pos'                   => 'Nilai perbandingan',
        'q'                     => 'Kata kunci',
    ],

];
