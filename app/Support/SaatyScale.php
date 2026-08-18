<?php

namespace App\Support;

/**
 * Skala Saaty 6-poin untuk pairwise comparison.
 *
 * TANPA nilai netral (1/1): setiap perbandingan HARUS memihak salah satu sisi.
 * UI mengirim posisi slider bilangan bulat -5..-1 atau 1..5 (0 tidak diizinkan):
 *   -5 -> 1/6, -4 -> 1/5, ... -1 -> 1/2,
 *    1 -> 2,   2 -> 3,   ... 5 -> 6.
 *
 * Posisi > 0 berarti elemen referensi lebih penting (rasio w_ref/w_other > 1);
 * posisi < 0 berarti elemen pembanding lebih penting (rasio < 1).
 */
class SaatyScale
{
    public const MIN_POS = -5;
    public const MAX_POS = 5;

    /** Aturan validasi Laravel untuk sebuah posisi slider (tanpa netral 0). */
    public static function validationRule(): string
    {
        return 'integer|between:'.self::MIN_POS.','.self::MAX_POS.'|not_in:0';
    }

    /** Konversi posisi slider menjadi rasio w_ref/w_other. */
    public static function positionToValue(int $pos): float
    {
        $pos = max(self::MIN_POS, min(self::MAX_POS, $pos));

        // Netral tidak diperbolehkan; jika 0 terlanjur masuk, pihakkan minimal
        // ke referensi (rasio 2) agar tidak pernah ada penilaian setara.
        if ($pos === 0) {
            $pos = 1;
        }

        return $pos > 0 ? (float) ($pos + 1) : 1.0 / (1 - $pos);
    }

    /** Konversi rasio kembali menjadi posisi slider (untuk pre-fill form). */
    public static function valueToPosition(float $value): int
    {
        if ($value >= 1) {
            return max(1, (int) round($value) - 1);
        }

        return min(-1, -((int) round(1 / $value) - 1));
    }

    /** Intensitas 2..6 dari sebuah posisi (untuk label). */
    public static function intensity(int $pos): int
    {
        return abs($pos) + 1;
    }
}
