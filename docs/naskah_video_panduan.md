# Naskah Video Panduan — Sistem "Risk Mitigate"

**Untuk:** Pakar / Penilai yang akan mencoba sistem
**Durasi target:** ± 8–9 menit
**Format:** Screen recording + voice-over (narasi)

**Cara pakai naskah ini:**
- Kolom **[LAYAR]** = apa yang direkam/ditampilkan.
- Kolom **[NARASI]** = yang dibacakan.
- Ganti `[URL SISTEM]` dengan alamat yang Anda bagikan (mis. `https://xxxx.trycloudflare.com`).
- Nada bicara: santai, jelas, tidak terburu-buru. Jeda singkat tiap ganti langkah.

---

## Scene 0 — Pembuka (0:00 – 0:40)

**[LAYAR]** Judul: "Risk Mitigate — Sistem Pendukung Keputusan Mitigasi Risiko Proyek TI & R&D". Lalu tampilkan halaman login.

**[NARASI]**
> Halo, selamat datang. Video ini memandu Anda menggunakan **Risk Mitigate**, sebuah sistem pendukung keputusan untuk merumuskan **strategi mitigasi risiko proyek TI dan R&D**.
>
> Singkatnya, sistem ini membantu Anda menentukan **faktor risiko mana yang paling prioritas** menggunakan metode **AHP** (Analytic Hierarchy Process), lalu memberikan **rekomendasi strategi mitigasi** yang sesuai untuk empat tingkat risiko: Low, Medium, High, dan Critical. Mari kita mulai.

---

## Scene 1 — Registrasi & Login (0:40 – 1:30)

**[LAYAR]** Buka `[URL SISTEM]`. Klik "Daftar di sini". Isi Nama, Email, Password, Konfirmasi. Klik ikon **mata** untuk memperlihatkan password. Klik "Daftar" → masuk. (Atau tampilkan Login bila sudah punya akun.)

**[NARASI]**
> Pertama, buka alamat sistem yang dibagikan kepada Anda. Jika belum punya akun, klik **Daftar di sini**, lalu isi nama, **email unik yang belum pernah dipakai**, dan password.
>
> Butuh memastikan password benar? Klik **ikon mata** di kolom password untuk melihatnya. Setelah selesai, klik **Daftar**. Kalau sudah punya akun, cukup **Masuk** dengan email dan password Anda.

---

## Scene 2 — Mengenal Dashboard (1:30 – 2:10)

**[LAYAR]** Tampilkan Dashboard. Sorot menu sidebar kiri: **Dashboard**, **Daftarkan Proyek**, **Cari Proyek**, **Logout**. Sorot tiga kotak statistik: Total Proyek, Sebagai Manajer, Sebagai Penilai. Sorot tabel daftar proyek.

**[NARASI]**
> Setelah masuk, Anda berada di **Dashboard**. Di sidebar kiri ada menu utama: **Dashboard**, **Daftarkan Proyek**, **Cari Proyek**, dan **Logout**.
>
> Di bagian atas ada ringkasan: total proyek Anda, berapa yang Anda kelola **Sebagai Manajer**, dan berapa yang Anda ikut nilai **Sebagai Penilai**. Di bawahnya, daftar semua proyek yang bisa Anda akses.

---

## Scene 3 — Mendaftarkan Proyek (2:10 – 3:20)

**[LAYAR]** Klik "＋ Daftarkan Proyek". Muncul pilihan domain: **IT** dan **R&D** — klik salah satu (mis. IT). Lalu isi form: Nama Proyek, Deskripsi, Tujuan, dan Timeline (Mulai–Selesai). Klik "Daftarkan Proyek". Tampilkan halaman proyek + sorot **Unique ID** (mis. `RM-IT-9F3A2B`).

**[NARASI]**
> Untuk mulai, klik **Daftarkan Proyek**. Langkah pertama, pilih **domain** proyek: **IT** atau **R&D**. Pilihan ini menentukan daftar faktor risiko dan strategi yang dipakai nanti.
>
> Selanjutnya isi informasi umum: nama proyek, deskripsi, tujuan, dan perkiraan waktunya. Perhatikan, **Anda tidak perlu menentukan tingkat risiko di awal** — sistem akan menilai keempat level sekaligus nanti.
>
> Klik **Daftarkan Proyek**. Proyek Anda kini punya **ID unik** — catat ini, karena bisa dipakai untuk mencari proyek. Sebagai pendaftar, Anda otomatis menjadi **Manajer** proyek ini.

---

## Scene 4 — Menambah Penilai / Anggota (3:20 – 4:10)

**[LAYAR]** Di halaman proyek, sorot panel **Anggota Proyek** di kanan. Ketik email seorang rekan di kolom "Tambah Anggota", pilih dari saran yang muncul, klik "Tambah Anggota". Tampilkan anggota bertambah.

**[NARASI]**
> Sebuah proyek bisa dinilai oleh **beberapa pakar sekaligus**. Untuk menambahkan pakar lain, buka panel **Anggota Proyek** di kanan, lalu ketik **email** rekan Anda. Sistem akan menampilkan saran — pilih, lalu klik **Tambah Anggota**.
>
> Pastikan rekan tersebut **sudah mendaftar** di sistem terlebih dahulu, ya. Jika Anda ingin menilai sendirian, langkah ini bisa dilewati.

---

## Scene 5 — (Opsional) Pembobotan Expert (4:10 – 5:05)

**[LAYAR]** Klik "⚖ Pembobotan Expert". Tampilkan form: pilih **Elemen Referensi** (salah satu penilai), lalu geser slider membandingkan referensi dengan penilai lain. Klik "Hitung & Simpan Bobot". Tampilkan bobot tiap penilai (total = 1).

**[NARASI]**
> Jika sebuah proyek dinilai oleh beberapa pakar, Anda—sebagai manajer—bisa mengatur **bobot** tiap pakar. Misalnya penilaian seorang ahli keamanan diberi bobot lebih besar untuk proyek yang sensitif.
>
> Buka **Pembobotan Expert**, pilih satu pakar sebagai acuan, lalu geser slider untuk membandingkan tingkat kepentingannya dengan pakar lain. Klik **Hitung & Simpan Bobot**. Total bobot otomatis menjadi satu.
>
> Langkah ini **opsional**. Jika dilewati, semua pakar dianggap **berbobot sama**.

---

## Scene 6 — Memulai Assessment (5:05 – 5:50)

**[LAYAR]** Klik "＋ Mulai Assessment". Tampilkan daftar **Pilih Penilai** dengan checkbox (Manajer — Anda + anggota lain). Centang penilai. Klik "Mulai Assessment". Masuk ke halaman assessment dengan 4 tab: Low, Medium, High, Critical.

**[NARASI]**
> Sekarang inti dari sistem: **Assessment**, yaitu proses perumusan strategi mitigasi.
>
> Klik **Mulai Assessment**, lalu **pilih siapa yang akan menilai**. Anda bisa menilai sendiri, mendelegasikan ke anggota, atau keduanya. Klik **Mulai Assessment**.
>
> Setiap assessment mencakup **empat tingkat risiko** sekaligus — Low, Medium, High, dan Critical — masing-masing dengan sembilan faktor risiko. Jika hanya **satu** penilai, sistem memakai AHP dengan perbandingan lengkap; jika **lebih dari satu**, memakai metode AHP-Express yang lebih ringkas lalu menggabungkan penilaian semua pakar.

---

## Scene 7 — Mengisi Penilaian AHP (5:50 – 7:30)

**[LAYAR]** Di salah satu tab level, klik "Isi Penilaian". Tampilkan form AHP-Express: dropdown **Elemen Referensi**, lalu baris-baris perbandingan dengan slider dua arah. Geser satu slider ke kiri/kanan; sorot tampilan "◀ ... lebih penting" dan intensitas (mis. "4× ..."). Sorot deskripsi faktor. Klik "Simpan Penilaian Saya". Ulangi singkat untuk level lain. Tampilkan pop-up konfirmasi bila ada slider default.

**[NARASI]**
> Untuk menilai, buka sebuah level lalu klik **Isi Penilaian**.
>
> Pada metode AHP-Express, pertama pilih **satu faktor sebagai referensi**. Lalu Anda cukup membandingkan referensi itu terhadap delapan faktor lain — sisanya dihitung otomatis oleh sistem.
>
> Caranya: **geser slider ke arah faktor yang menurut Anda lebih penting**, lalu tentukan seberapa jauh, dari **intensitas 2** — sedikit lebih penting, hingga **6** — jauh lebih penting. Perhatikan, **tidak ada nilai "sama penting"**; Anda harus memihak salah satu. Setiap faktor juga dilengkapi **deskripsi singkat** untuk membantu Anda menilai.
>
> Setelah selesai satu level, klik **Simpan Penilaian Saya**, lalu lanjut ke level berikutnya. Jika ada perbandingan yang belum Anda ubah, sistem akan **mengingatkan** — pastikan setiap penilaian memang disengaja. Ulangi hingga keempat level selesai.

---

## Scene 8 — Melihat Hasil & Rekomendasi (7:30 – 8:40)

**[LAYAR]** Tampilkan hasil pada sebuah level (setelah semua penilai selesai). Sorot: urutan faktor dengan **Bobot Risiko** dan angka **x%/100%**, bar bobot, deskripsi faktor, dan **strategi mitigasi**. Klik switch **"Tampilkan sumber referensi"** untuk memperlihatkan sumber. Pindah antar-tab level.

**[NARASI]**
> Begitu **semua penilai** menyelesaikan sebuah level, sistem otomatis menghitung hasilnya. Jika Anda manajer, Anda juga bisa menekan **Hitung Level yang Siap**.
>
> Hasilnya ditampilkan **per level**. Setiap faktor risiko diurutkan berdasarkan **Bobot Risiko**-nya, dari yang paling prioritas. Di bawah tiap faktor terdapat **strategi mitigasi** dari basis pengetahuan sistem — inilah rekomendasi tindakan Anda.
>
> Ingin melihat **sumber ilmiah** tiap strategi? Aktifkan switch **Tampilkan sumber referensi** di kanan atas. Anda bisa berpindah antar tab **Low, Medium, High, Critical** untuk melihat prioritas dan strategi di tiap tingkat risiko.

---

## Scene 9 — Cari Proyek & Penutup (8:40 – 9:20)

**[LAYAR]** Klik menu **Cari Proyek**, ketik nama atau Unique ID, klik Cari, buka hasilnya. Lalu tampilkan kembali dashboard / logo penutup.

**[NARASI]**
> Terakhir, untuk kembali ke proyek kapan saja, gunakan menu **Cari Proyek** — cari berdasarkan **nama** atau **ID unik** proyek.
>
> Itu saja alur lengkapnya: daftar, buat proyek, tambahkan pakar, jalankan assessment, isi penilaian AHP, dan lihat rekomendasi strategi mitigasi. Terima kasih telah mencoba **Risk Mitigate**. Selamat menggunakan!

---

## Checklist perekaman
- [ ] Siapkan akun uji + minimal satu proyek contoh sebelum merekam.
- [ ] Sembunyikan data pribadi/email asli bila perlu (pakai akun demo: `budi@demo.com`, dst, password `password`).
- [ ] Rekam pada resolusi minimal 1080p, perbesar kursor bila memungkinkan.
- [ ] Untuk Scene 8, gunakan proyek yang **assessment-nya sudah selesai** agar hasil tampil.
- [ ] Total durasi jangan lebih dari ~9 menit; potong jeda-jeda kosong saat editing.
