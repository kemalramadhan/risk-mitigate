# User Manual — Risk Mitigate

**Sistem Pendukung Keputusan Mitigasi Risiko Proyek TI & R&D**

Dokumen ini menjelaskan cara menggunakan sistem **Risk Mitigate** dari awal hingga
mendapatkan rekomendasi strategi mitigasi risiko. Ditujukan untuk **pakar/penilai**
dan **manajer proyek** yang mencoba sistem.

> **Catatan pengisian:** Ganti setiap penanda `[Screenshot: ...]` dengan tangkapan
> layar yang sesuai. Ganti `[URL SISTEM]` dengan alamat yang dibagikan kepada Anda.

---

## Daftar Isi
1. [Tentang Sistem](#1-tentang-sistem)
2. [Konsep & Istilah Penting](#2-konsep--istilah-penting)
3. [Memulai: Registrasi & Login](#3-memulai-registrasi--login)
4. [Dashboard](#4-dashboard)
5. [Mendaftarkan Proyek](#5-mendaftarkan-proyek)
6. [Mengelola Anggota Proyek](#6-mengelola-anggota-proyek)
7. [Pembobotan Expert (Opsional)](#7-pembobotan-expert-opsional)
8. [Memulai Assessment](#8-memulai-assessment)
9. [Mengisi Penilaian AHP](#9-mengisi-penilaian-ahp)
10. [Melihat Hasil & Rekomendasi Strategi](#10-melihat-hasil--rekomendasi-strategi)
11. [Mencari Proyek](#11-mencari-proyek)
12. [Pertanyaan Umum & Pemecahan Masalah](#12-pertanyaan-umum--pemecahan-masalah)

---

## 1. Tentang Sistem

Risk Mitigate membantu tim proyek menentukan **prioritas faktor risiko** dan
memberikan **rekomendasi strategi mitigasi** berbasis basis pengetahuan (Knowledge
Base). Prioritas dihitung dengan metode **AHP (Analytic Hierarchy Process)**, dan
untuk penilaian oleh banyak pakar digabungkan dengan metode **agregasi kelompok**.

Setiap proyek dinilai untuk **empat tingkat risiko**: **Low, Medium, High, Critical**,
masing-masing memiliki **9 faktor risiko** dan **3 strategi mitigasi per faktor**.

---

## 2. Konsep & Istilah Penting

| Istilah | Penjelasan |
|---|---|
| **Domain** | Jenis proyek: **IT** atau **R&D**. Menentukan faktor risiko & strategi yang dipakai. |
| **Manajer** | Pengguna yang mendaftarkan proyek. Mengelola anggota, bobot, dan assessment. |
| **Penilai / Pakar** | Pengguna yang melakukan penilaian AHP. Bisa manajer sendiri dan/atau anggota. |
| **Unique ID** | Kode identitas proyek, mis. `RM-IT-9F3A2B`. Dipakai untuk mencari proyek. |
| **Faktor Risiko** | Aspek yang berpotensi menimbulkan risiko (mis. *Technical Debt Level*). |
| **Assessment** | Satu putaran perumusan strategi yang mencakup **4 level risiko** sekaligus. |
| **AHP-Express** | Metode AHP ringkas: cukup **8 perbandingan** per level (dipakai bila penilai > 1). |
| **AHP Standar** | Perbandingan **semua pasangan faktor** (dipakai bila penilai hanya 1). |
| **Bobot Risiko** | Persentase prioritas sebuah faktor hasil perhitungan AHP. |
| **Strategi Mitigasi** | Rekomendasi tindakan dari Knowledge Base untuk menangani faktor risiko. |

---

## 3. Memulai: Registrasi & Login

1. Buka `[URL SISTEM]` di peramban (Chrome/Edge/Firefox).
2. **Belum punya akun?** Klik **Daftar di sini**, lalu isi:
   - **Nama**
   - **Email** — harus **unik** (belum pernah dipakai di sistem)
   - **Password** dan **Konfirmasi** password
   - Klik **ikon mata** 👁 untuk memperlihatkan/menyembunyikan password.
3. Klik **Daftar**.
4. **Sudah punya akun?** Klik **Masuk**, isi email & password, klik **Masuk**.

[Screenshot: Halaman registrasi & login]

---

## 4. Dashboard

Setelah masuk, Anda melihat **Dashboard**.

- **Sidebar kiri:** Dashboard · Daftarkan Proyek · Cari Proyek · Logout.
- **Kotak ringkasan:** Total Proyek, Sebagai Manajer, Sebagai Penilai.
- **Tabel proyek:** menampilkan Nama, Unique ID, Domain, jumlah Assessment, Peran
  Anda (Owner/Expert), jumlah Anggota, dan tombol **Buka**.

[Screenshot: Dashboard]

---

## 5. Mendaftarkan Proyek

1. Klik **Daftarkan Proyek** (di sidebar atau tombol **＋ Daftarkan Proyek**).
2. **Langkah 1 — Pilih Domain:** klik **IT** atau **R&D**.
3. **Langkah 2 — Isi Informasi Umum:**
   - **Nama Proyek** (wajib)
   - **Deskripsi**, **Tujuan Proyek** (opsional)
   - **Timeline** (tanggal Mulai & Selesai, opsional)
4. Klik **Daftarkan Proyek**.
5. Anda diarahkan ke halaman proyek. Catat **Unique ID** proyek Anda.

> Tidak ada pengisian tingkat risiko di tahap ini — sistem menilai keempat level
> saat assessment.

[Screenshot: Pilih domain & form pendaftaran proyek]

---

## 6. Mengelola Anggota Proyek

Di halaman proyek, panel **Anggota Proyek** (kanan) — **khusus manajer**:

- **Menambah anggota:** ketik **email** pengguna, pilih dari saran yang muncul, klik
  **Tambah Anggota**. (Pengguna harus **sudah terdaftar** lebih dulu.)
- **Menghapus anggota:** klik **Hapus** di sebelah nama anggota.

> Jika komposisi anggota berubah, pembobotan expert direset (perlu diatur ulang bila
> diperlukan).

[Screenshot: Panel Anggota Proyek]

---

## 7. Pembobotan Expert (Opsional)

Jika proyek dinilai **lebih dari satu** pakar, manajer dapat menetapkan **bobot** tiap
pakar (mis. suara ahli tertentu lebih berpengaruh).

1. Di halaman proyek, klik **⚖ Pembobotan Expert**.
2. Pilih **Elemen Referensi** (salah satu penilai sebagai acuan).
3. **Geser slider** untuk membandingkan tingkat kepentingan referensi terhadap penilai
   lain.
4. Klik **Hitung & Simpan Bobot**. Total bobot otomatis menjadi **1**.
5. Untuk membatalkan, klik **Reset ke Bobot Sama**.

> **Opsional.** Jika dilewati, semua pakar dianggap **berbobot sama**.

[Screenshot: Form Pembobotan Expert]

---

## 8. Memulai Assessment

1. Di halaman proyek, bagian **Assessment**, klik **＋ Mulai Assessment** (manajer).
2. **Pilih Penilai:** centang siapa yang akan menilai — bisa **Anda (Manajer)**,
   anggota lain, atau keduanya (minimal satu).
3. Klik **Mulai Assessment**.

Sistem membuat penilaian untuk **4 level** (Low, Medium, High, Critical). Mode dipilih
otomatis:
- **1 penilai** → **AHP Standar** (bandingkan semua pasangan faktor).
- **> 1 penilai** → **AHP-Express** + penggabungan penilaian semua pakar.

[Screenshot: Pilih penilai / Mulai Assessment]

---

## 9. Mengisi Penilaian AHP

Pada halaman assessment terdapat **tab Low, Medium, High, Critical**. Untuk tiap level:

1. Buka tab level, klik **Isi Penilaian** (bagi penilai).
2. **Pilih Elemen Referensi** (satu faktor sebagai acuan) — *hanya pada AHP-Express*.
3. Untuk setiap perbandingan, **geser slider ke faktor yang lebih penting**, lalu
   tentukan intensitasnya:

   | Intensitas | Arti |
   |---|---|
   | 2 | Sedikit lebih penting |
   | 3 | Cukup lebih penting |
   | 4 | Lebih penting |
   | 5 | Sangat lebih penting |
   | 6 | Mutlak lebih penting |

   > **Tidak ada nilai "sama penting"** — setiap perbandingan harus memihak salah satu
   > faktor. Setiap faktor dilengkapi **deskripsi** untuk membantu penilaian.
4. Klik **Simpan Penilaian Saya** (AHP-Express) atau **Simpan & Hitung Hasil**
   (AHP Standar).
5. Jika masih ada slider pada nilai **default** yang belum diubah, muncul
   **konfirmasi** — pastikan nilainya memang disengaja.
6. Ulangi untuk keempat level.

[Screenshot: Form penilaian pairwise AHP]

---

## 10. Melihat Hasil & Rekomendasi Strategi

- Hasil sebuah level muncul otomatis setelah **semua penilai** menyelesaikan level itu.
  Manajer juga dapat menekan **Hitung Level yang Siap**.
- Assessment berstatus **Selesai** bila keempat level telah dihitung.

Pada tiap tab level, Anda melihat daftar **faktor risiko terurut prioritas**:
- Label **Bobot Risiko** dengan angka **x%/100%** dan bar bobot.
- **Deskripsi** faktor.
- **Strategi mitigasi** (rekomendasi tindakan) dari Knowledge Base.

**Menampilkan sumber:** aktifkan switch **Tampilkan sumber referensi** di kanan atas
untuk melihat pengarang, judul, dan kutipan sumber tiap strategi. Secara default,
sumber disembunyikan agar laporan ringkas.

[Screenshot: Halaman hasil — Bobot Risiko & strategi mitigasi]
[Screenshot: Switch "Tampilkan sumber referensi" aktif]

---

## 11. Mencari Proyek

1. Klik **Cari Proyek** di sidebar.
2. Ketik **nama proyek** atau **Unique ID**.
3. Klik **Cari**, lalu **Buka** proyek dari hasil.

[Screenshot: Halaman Cari Proyek]

---

## 12. Pertanyaan Umum & Pemecahan Masalah

**Kenapa saya tidak bisa menambah anggota?**
Pengguna yang ditambahkan harus **sudah mendaftar** di sistem. Pastikan emailnya benar.

**Kenapa hasil sebuah level belum muncul?**
Hasil baru dihitung setelah **semua penilai** menyelesaikan level tersebut. Cek panel
**Penilai** untuk melihat progres (mis. "2/4 level dinilai").

**Kenapa tidak ada opsi "sama penting" di slider?**
Memang disengaja. Metode ini mengharuskan setiap perbandingan memihak satu faktor agar
prioritas lebih tegas.

**Faktor referensi otomatis jadi peringkat 1?**
Tidak. Referensi hanya titik acuan perbandingan; faktor lain bisa berbobot lebih tinggi.

**Apakah satu proyek bisa dinilai berkali-kali?**
Ya. Manajer dapat memulai **assessment baru** kapan pun.

**Akun demo untuk mencoba:**
`budi@demo.com`, `sari@demo.com`, `andi@demo.com`, `rina@demo.com` — password: `password`.

---

*Dokumen ini dapat diperbarui mengikuti perkembangan sistem.*
