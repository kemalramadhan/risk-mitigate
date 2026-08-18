<?php

return [
    /*
    | Interpreter Python untuk menjalankan engine AHP. Ubah bila perlu path
    | absolut, mis. "/usr/local/bin/python3".
    */
    'python' => env('AHP_PYTHON', 'python3'),

    /* Lokasi script engine (relatif terhadap base path aplikasi). */
    'engine' => env('AHP_ENGINE', 'ahp/ahp_engine.py'),

    /* Timeout eksekusi (detik). */
    'timeout' => (int) env('AHP_TIMEOUT', 30),

    /*
    | Skala Saaty 6-poin yang dipakai pada seluruh pairwise comparison.
    | Nilai yang boleh dipilih pada slider (beserta resiprokalnya).
    */
    'scale' => [1, 2, 3, 4, 5, 6],

    // Intensitas 2..6 (tanpa 1 = setara, karena penilaian netral dihapus).
    'scale_labels' => [
        2 => 'Sedikit lebih penting',
        3 => 'Cukup lebih penting',
        4 => 'Lebih penting',
        5 => 'Sangat lebih penting',
        6 => 'Mutlak lebih penting',
    ],
];
