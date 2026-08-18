<?php

namespace App\Support;

/**
 * Utilitas untuk menampilkan lokasi sumber (kolom Lokasi pada Knowledge Base).
 *
 * Pada tampilan hasil rekomendasi, nomor halaman tidak ditampilkan — cukup
 * bagian/section-nya saja.
 *   "Section 6.4.2, hlm. 11"                    -> "Section 6.4.2"
 *   "Section 1 hlm. 668; Section 3.1f hlm. 673" -> "Section 1; Section 3.1f"
 *   "Finding 1, Section 3.1, hlm. 10"           -> "Finding 1, Section 3.1"
 *   "Klausul 4 (g) — Faktor Manusia & Budaya"   -> tidak berubah
 */
class SourceLocation
{
    /**
     * Hapus penanda halaman ("hlm. ...") dari tiap segmen lokasi.
     * Segmen dipisahkan titik koma; koma TIDAK dipakai sebagai pemisah segmen
     * karena koma juga muncul di dalam label bagian.
     */
    public static function withoutPages(?string $lokasi): ?string
    {
        if ($lokasi === null || trim($lokasi) === '') {
            return null;
        }

        $segments = [];
        foreach (explode(';', $lokasi) as $segment) {
            // Buang mulai dari "hlm." sampai akhir segmen (termasuk koma pemisah
            // sebelumnya), mis. ", hlm. 723–724" atau " hlm. 726, 383".
            $clean = preg_replace('/\s*,?\s*hlm\.?\s*.*$/iu', '', $segment);

            // Bila teks mengandung byte non-UTF-8, mode /u mengembalikan null.
            // Ulangi tanpa mode unicode agar isi tidak hilang.
            if ($clean === null) {
                $clean = preg_replace('/\s*,?\s*hlm\.?\s*.*$/i', '', $segment) ?? $segment;
            }

            $clean = trim((string) $clean, " \t\n\r\0\x0B,");

            if ($clean !== '') {
                $segments[] = $clean;
            }
        }

        $result = implode('; ', $segments);

        return $result !== '' ? $result : null;
    }
}
