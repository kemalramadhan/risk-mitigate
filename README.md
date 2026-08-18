# Risk Mitigate

Sistem Pendukung Keputusan (SPK) untuk **Mitigasi Risiko Proyek TI dan R&D**.
Sistem memadukan **knowledge base** hasil ekstraksi FP-Growth dengan metode
**AHP-Express** (Analytic Hierarchy Process) — termasuk *group decision* —
untuk merekomendasikan strategi mitigasi sesuai prioritas para pengambil keputusan.

## Tech Stack
- **Backend & Routing:** Laravel 13 (PHP 8.4)
- **Frontend:** Laravel Blade + Bootstrap 5 (nuansa Enterprise/IT GRC)
- **Algoritma:** Python (engine AHP, dipanggil Laravel via `Process`)
- **Database:** MySQL

---

## Arsitektur & Alur

### Fitur 1 — Pendaftaran Proyek & Triage
- User memilih **Domain** (IT / R&D) dan mengisi informasi umum proyek.
- **Mini-questionnaire (Triage)** berupa dropdown menghitung **Risk_Level**
  (Low / Medium / High / Critical) secara otomatis dari total skor karakteristik.
- Proyek memperoleh **Unique ID** (mis. `RM-IT-9F3A2B`).
- Owner dapat menambah anggota (expert) via email lalu **meng-enable** proyek.
- Konfigurasi skor triage: [`config/triage.php`](config/triage.php)
  (beberapa karakteristik R&D sengaja *incomplete* sesuai basis data penelitian).

### Fitur 2 — Pembobotan Expert
- Owner melakukan **pairwise comparison** (AHP-Express, skala Saaty 6-poin)
  terhadap seluruh anggota termasuk dirinya → bobot tiap expert (**Σ = 1**).
- Bila tidak dilakukan, bobot setiap expert **dianggap sama**.

### Fitur 3 — Perumusan Strategi Mitigasi
- Setiap partisipan mengisi form **AHP-Express** atas **9 faktor risiko** sesuai
  level proyek: pilih 1 faktor referensi, bandingkan terhadap 8 faktor lain
  (n−1 perbandingan, **Consistency Ratio selalu 0**).
- Setelah **seluruh** partisipan menilai, sistem menghitung:
  - **1 partisipan** → AHP-Express biasa (prioritas individual = hasil final).
  - **> 1 partisipan** → **AIP (Aggregation of Individual Priorities)** dengan
    *weighted geometric mean* (Françozo et al., 2023, Pers. 9) memakai bobot
    expert dari Fitur 2.
- Sistem merekomendasikan strategi mitigasi terurut bobot faktor, disimpan
  bersama timestamp. Dapat dilakukan lebih dari sekali per proyek.

### Metode AHP (referensi)
> Françozo et al. (2023), *A web-based software for group decision with analytic
> hierarchy process*, MethodsX 11, 102277.

- Engine: [`ahp/ahp_engine.py`](ahp/ahp_engine.py) — murni Python (tanpa dependensi eksternal).
- Jembatan Laravel: [`app/Services/AhpService.php`](app/Services/AhpService.php).
- Skala perbandingan: **Saaty 6-poin (1–6)** beserta resiprokalnya, lihat
  [`config/ahp.php`](config/ahp.php) dan [`app/Support/SaatyScale.php`](app/Support/SaatyScale.php).

---

## Instalasi

### 1. Dependensi
```bash
composer install
npm install
```

### 2. Environment
```bash
cp .env.example .env      # jika .env belum ada
php artisan key:generate
```

### 3. Database MySQL
Buat database + user (jalankan sebagai root, sesuaikan password di file SQL):
```bash
mysql -u root -p < database/setup_mysql.sql
```
Lalu set kredensial pada `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=risk_mitigate
DB_USERNAME=risk_mitigate
DB_PASSWORD=... (sesuai setup_mysql.sql)
```

### 4. Knowledge Base → JSON → Seed
Ekstrak ulang dari Excel (opsional, hasilnya sudah tersimpan di
`database/data/knowledge_base.json`):
```bash
python3 database/data/export_knowledge_base.py
```
Migrasi + seed:
```bash
php artisan migrate:fresh --seed
```
Seed menghasilkan **72 faktor risiko**, **216 strategi**, dan ratusan referensi
(2 domain × 4 level × 9 faktor × 3 strategi).

### 5. Build aset & jalankan
```bash
npm run build      # atau: npm run dev
php artisan serve
```
Buka `http://127.0.0.1:8000`, daftar dengan email unik, lalu mulai.

### 6. (Opsional) Data demo
Isi akun + proyek contoh untuk mencoba semua skenario:
```bash
php artisan db:seed --class=DemoSeeder
```
Membuat 4 akun (**password: `password`**): `budi@demo.com`, `sari@demo.com`,
`andi@demo.com`, `rina@demo.com`, beserta 4 proyek yang mencakup:
- **Migrasi Core Banking System** (IT/High) — perumusan **grup selesai** + bobot expert
- **Pengembangan Vaksin mRNA** (R&D/Medium) — perumusan **single selesai**
- **Company Profile Website Revamp** (IT/Low) — ronde **sedang berjalan**
- **Real-time Fraud Detection Engine** (IT/Critical) — **belum di-enable** (uji gerbang akses)

Login sebagai `budi@demo.com` untuk melihat proyek grup selesai + ronde berjalan.

---

## Struktur Data Utama
| Tabel | Keterangan |
|---|---|
| `risk_factors` | 9 faktor per (domain, level) |
| `mitigation_strategies` | 3 strategi per faktor |
| `strategy_references` | Sumber/Lokasi/Kutipan tiap strategi |
| `projects` | Proyek + hasil triage + status enable |
| `project_members` | Anggota + bobot expert |
| `expert_weight_judgments` | Judgment pembobotan expert (Fitur 2) |
| `formulations` | Ronde perumusan + hasil (JSON) |
| `formulation_participants` | Partisipan + vektor prioritas individual |
| `formulation_judgments` | Judgment AHP-Express tiap partisipan |

## Catatan Pengujian
Alur lengkap (triage → anggota → pembobotan → perumusan → hasil) telah divalidasi
end-to-end melalui service dan HTTP. Engine AHP dikonfirmasi menghasilkan nilai
yang sesuai contoh pada paper (mis. referensi vs 3,6 → 0.667 / 0.222 / 0.111).
