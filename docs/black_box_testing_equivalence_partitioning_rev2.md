# BLACK-BOX TESTING — EQUIVALENCE PARTITIONING (Revisi 2)

**SPK Mitigasi Risiko Proyek TI & R&D** · Kemal Ramadhan (187221062)

**Metode AHP yang diimplementasikan:**
- **Solo (1 penilai)** → **AHP standar**: matriks pairwise penuh, n(n−1)/2 = **36 perbandingan** per level, **CR dihitung** (RI₉ = 1,45; konsisten bila CR ≤ 0,1).
- **Grup (>1 penilai)** → **AHP-Express** (n−1 = **8 perbandingan** per level, **CR = 0**) + agregasi **AIP weighted geometric mean** (Françozo et al., 2023, Pers. 9).
- **Skala Saaty 6 poin (2–6), TANPA nilai setara (1:1)** — setiap perbandingan wajib memihak salah satu sisi.
- Tidak ada asesmen/triage level risiko di awal; setiap assessment menilai **keempat level** (Low, Medium, High, Critical).

## Catatan Revisi terhadap dokumen sebelumnya

| Test case | Sebelumnya | Direvisi menjadi | Alasan |
|---|---|---|---|
| TC-AHP-02 | Batas bawah slider = 1 (*sama penting*) diterima | Batas bawah = **2** (*sedikit lebih penting*); nilai setara **tidak tersedia** | Sistem dirancang tanpa penilaian netral |
| TC-AHP-05 | 8 perbandingan → laporan **langsung** tampil, CR = 0 | Dipecah: **TC-AHP-05** (grup, 8 perbandingan, CR = 0, laporan menunggu semua penilai) & **TC-AHP-06** (solo, 36 perbandingan, laporan langsung, CR dihitung) | Solo memakai AHP standar, bukan AHP-Express |
| TC-AGG-03 | Solo → prioritas = hasil individual | Solo → **AHP standar**, prioritas = hasil individual tanpa agregasi, **CR ditampilkan** | Menyesuaikan metode solo |
| — | — | **TC-AHP-07** (baru): nilai setara ditolak server | Menguji aturan "tanpa netral" |

---

| ID | Modul | Tipe | Kelas Ekuivalensi | Input / Kondisi Uji | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|---|---|
| TC-REG-01 | 1. Registrasi | Valid | Seluruh data registrasi lengkap & valid | Nama + email valid + password & konfirmasi cocok | Akun dibuat, diarahkan ke dashboard | | |
| TC-REG-02 | 1. Registrasi | Invalid | Email sudah terdaftar | email yang sudah ada di sistem | Ditolak, **"Email sudah terdaftar."** | | |
| TC-REG-03 | 1. Registrasi | Invalid | Konfirmasi password tidak cocok | password: Abc12345 / konfirmasi: Abc99999 | Ditolak, **"Konfirmasi Password tidak cocok."** | | |
| TC-REG-04 | 1. Registrasi | Invalid | Password di bawah batas minimum | password: "123" | Ditolak, **"Password minimal 8 karakter."** | | |
| TC-REG-05 | 1. Registrasi | Invalid | Format email salah | kemal.unair (tanpa @) | Ditolak, **"Format email tidak valid."** | | |
| TC-REG-06 | 1. Registrasi | Invalid | Field wajib kosong | salah satu field dikosongkan | Ditolak, **"Nama wajib diisi."** (dst. per field) | | |
| TC-AUTH-01 | 2. Autentikasi | Valid | Email terdaftar + password benar | budi@demo.com / password | Login sukses, redirect ke dashboard | | |
| TC-AUTH-02 | 2. Autentikasi | Invalid | Password salah | budi@demo.com / salah99 | Ditolak, **"Kredensial yang dimasukkan salah."** | | |
| TC-AUTH-03 | 2. Autentikasi | Invalid | Email tidak terdaftar | asing@mail.com / apapun1 | Ditolak, **"Kredensial yang dimasukkan salah."** | | |
| TC-AUTH-04 | 2. Autentikasi | Invalid | Format email salah | kemal.unair / Password123 | Ditolak, **"Format email tidak valid."** | | |
| TC-AUTH-05 | 2. Autentikasi | Invalid | Field kosong | (kosong) / (kosong) | Ditolak, **"Email wajib diisi."** | | |
| TC-PRJ-01 | 3. Manajemen Proyek | Valid | Domain dipilih lalu data proyek valid | Domain=IT, nama "SIM Kepegawaian" | Proyek tersimpan + Unique ID, manajer jadi owner, faktor mengikuti domain | | |
| TC-PRJ-02 | 3. Manajemen Proyek | Invalid | Nama proyek kosong | (kosong) | Ditolak, **"Nama proyek wajib diisi."** | | |
| TC-PRJ-03 | 3. Manajemen Proyek | Invalid | Nama proyek melebihi batas | string 300 karakter | Ditolak, **"Nama proyek maksimal 255 karakter."** | | |
| TC-PAR-01 | 4. Manajemen Partisipan | Valid | Email anggota terdaftar ditambahkan | sari@demo.com | Anggota masuk daftar anggota proyek | | |
| TC-PAR-02 | 4. Manajemen Partisipan | Valid | Hapus anggota dari proyek | klik hapus pada anggota | Anggota terhapus dari daftar | | |
| TC-PAR-03 | 4. Manajemen Partisipan | Invalid | Email tidak terdaftar di sistem | orangluar@mail.com | Ditolak, **"Pengguna tidak ditemukan."** | | |
| TC-PAR-04 | 4. Manajemen Partisipan | Invalid | Menambah anggota yang sudah ada | email anggota existing | Ditolak, **"Anggota sudah terdaftar."** | | |
| TC-PAR-05 | 4. Manajemen Partisipan | Invalid | Format email salah | bukan-email | Ditolak, **"Format email tidak valid."** | | |
| TC-PAR-06 | 4. Manajemen Partisipan | Valid | Tambah anggota ke proyek | 1 email anggota valid | Anggota bertambah; bobot expert direset & berlaku saat assessment baru dibuat | | |
| TC-DEL-01 | 5. Delegasi AHP | Valid | Manajer mengerjakan AHP sendiri | centang dirinya sendiri saat "Mulai Assessment" | Form AHP tampil untuk manajer (mode **AHP standar**, 1 penilai) | | |
| TC-DEL-02 | 5. Delegasi AHP | Valid | Manajer delegasikan ke anggota terdaftar | centang 1 anggota, mulai assessment | Anggota terpilih menerima tugas AHP | | |
| TC-DEL-03 | 5. Delegasi AHP | Valid | Delegasi ke beberapa anggota sekaligus | centang 2+ anggota | Semua terpilih menerima tugas AHP (mode **AHP-Express**) | | |
| TC-DEL-04 | 5. Delegasi AHP | Invalid | Delegasi tanpa memilih partisipan | tidak mencentang siapa pun | Ditolak, **"Pilih minimal satu partisipan."** | | |
| TC-DEL-05 | 5. Delegasi AHP | Invalid | Delegasi ke non-anggota proyek | user di luar proyek | Ditolak, **"Partisipan yang dipilih bukan anggota proyek ini."** (opsi tidak tersedia di UI) | | |
| TC-AHP-01 | 6. Form AHP | Valid | Nilai perbandingan dalam skala 2–6 | geser slider ke intensitas 4 | Diterima | | |
| TC-AHP-02 | 6. Form AHP | Valid | **(boundary) batas bawah slider** | slider pada intensitas **2** (sedikit lebih penting) | Diterima. Nilai setara (1:1) **tidak tersedia** pada slider | | |
| TC-AHP-03 | 6. Form AHP | Valid | (boundary) batas atas slider | slider pada intensitas **6** (mutlak lebih penting) | Diterima | | |
| TC-AHP-04 | 6. Form AHP | Valid | Konfirmasi slider yang tidak diubah | sebagian slider dibiarkan pada nilai default | Sistem menampilkan konfirmasi: *"N dari M perbandingan masih pada nilai default… Apakah memang disengaja?"* — OK = simpan, Batal = tinjau ulang | | |
| TC-AHP-05 | 6. Form AHP (grup) | Valid | **n−1 perbandingan 1 level (AHP-Express)** | 8 perbandingan (1 acuan vs 8 faktor) terisi | Penilaian tersimpan, **CR = 0**. Laporan level tampil **setelah seluruh penilai** menyelesaikan level tsb. | | |
| TC-AHP-06 | 6. Form AHP (solo) | Valid | **Matriks penuh 1 level (AHP standar)** | 36 perbandingan terisi oleh 1 penilai | Laporan level **langsung tampil**, bobot terhitung, **CR dihitung & ditampilkan** (badge konsisten/tidak) | | |
| TC-AHP-07 | 6. Form AHP | Invalid | **Nilai setara (netral) tidak diizinkan** | kirim nilai perbandingan setara (posisi 0) via request | Ditolak validasi server — skala tanpa nilai setara | | |
| TC-AGG-01 | 7. Agregasi Grup (AIP) | Valid | Agregasi 2+ partisipan, bobot rata | 2 partisipan submit AHP lengkap 1 level | Prioritas grup = weighted geometric mean, bobot terbagi rata | | |
| TC-AGG-02 | 7. Agregasi Grup (AIP) | Valid | Agregasi dengan bobot DM berbeda | owner set bobot antar-DM (Pembobotan Expert) | Prioritas grup tertimbang sesuai bobot (Σ bobot = 1) | | |
| TC-AGG-03 | 7. Agregasi Grup (AIP) | Valid | **(boundary) hanya 1 partisipan (solo)** | manajer sendiri | Metode = **AHP standar**; prioritas = hasil individual **tanpa agregasi**; **CR ditampilkan** | | |
| TC-AGG-04 | 7. Agregasi Grup (AIP) | Invalid | Ada partisipan belum submit | 1 dari 2 selesai | Pending, **"Menunggu semua partisipan menyelesaikan level ini (1/2 selesai)."** | | |
| TC-OUT-01 | 8. Output Rekomendasi | Valid | Tampilkan rekomendasi 4 level | AHP + agregasi selesai | Strategi mitigasi terurut prioritas untuk tiap level (tab Low/Medium/High/Critical) | | |
| TC-OUT-02 | 8. Output Rekomendasi | Valid | Urutan sesuai bobot AHP | bobot valid | Faktor & strategi terurut bobot tertinggi → terendah (%) | | |
| TC-OUT-03 | 8. Output Rekomendasi | Valid | Rekomendasi dari KB statis | level High dipilih | Strategi sesuai KB FP-Growth (9 faktor × 3 strategi), sumber/kutipan langsung tampil | | |
| TC-OUT-04 | 8. Output Rekomendasi | Invalid | Akses laporan sebelum AHP selesai | penilai belum submit | Ditolak, **"Selesaikan penilaian AHP terlebih dahulu."** | | |

---

## Akun demo untuk pengujian
`php artisan db:seed --class=DemoSeeder` — password semua akun: **`password`**

| Email | Peran demo |
|---|---|
| budi@demo.com | Manajer: proyek grup selesai (3 penilai) + assessment berjalan |
| sari@demo.com | Manajer proyek solo (AHP standar, 4 level selesai) + penilai |
| andi@demo.com | Manajer proyek tanpa assessment (uji "Mulai Assessment") + penilai |
| rina@demo.com | Penilai dengan tugas tertunda (uji TC-AGG-04 & TC-OUT-04) |
