# Panduan Deploy — Risk Mitigate (PC Lab Windows + Docker + Cloudflare)

Panduan menjalankan sistem di komputer lab (Windows) memakai Docker, lalu
mengeksposnya ke internet lewat **Cloudflare Tunnel** agar bisa diakses expert.

Seluruh stack (PHP 8.4, MySQL, Python untuk engine AHP, dan tunnel) berjalan di
dalam container — tidak perlu memasang PHP/MySQL/Python secara manual.

---

## 1. Prasyarat (sekali saja)

1. **Docker Desktop for Windows** — <https://www.docker.com/products/docker-desktop/>
   (pilih backend **WSL2** saat instalasi). Jalankan Docker Desktop sampai statusnya *Running*.
2. **Git for Windows** — <https://git-scm.com/download/win>

## 2. Ambil kode

Buka **PowerShell** / **Command Prompt**:

```powershell
git clone https://github.com/kemalramadhan/risk-mitigate.git
cd risk-mitigate
```

## 3. Siapkan konfigurasi

```powershell
copy .env.docker.example .env
```

Buka `.env` dengan Notepad, ganti dua baris ini dengan password bebas (huruf/angka):

```
DB_PASSWORD=passwordku123
DB_ROOT_PASSWORD=rootku123
```

> Sisanya biarkan default. `APP_KEY` boleh dikosongkan — container akan membuat
> kunci sementara otomatis (lihat catatan di bagian 8 bila ingin kunci tetap).

## 4. Jalankan

```powershell
docker compose up -d --build
```

Build pertama memakan ~5–10 menit (mengunduh image + memasang dependensi).
Saat pertama kali jalan, database otomatis dimigrasi dan di-seed (Knowledge Base
+ akun demo).

## 5. Ambil URL publik untuk expert

```powershell
docker compose logs cloudflared
```

Cari baris berisi URL seperti:

```
https://xxxx-yyyy-zzzz.trycloudflare.com
```

**Itulah URL yang Anda bagikan ke expert.** Mereka bisa langsung membuka,
mendaftar, dan mencoba sistem.

- Akses lokal (di PC lab itu sendiri): <http://localhost:8000>
- Akun demo siap pakai: `budi@demo.com`, `sari@demo.com`, `andi@demo.com`,
  `rina@demo.com` — semua password: `password`.

> URL Cloudflare gratis ini **berubah setiap tunnel restart**. Untuk URL tetap,
> lihat bagian 8.

## 6. Menjaga tetap online

- Biarkan **PC lab menyala** dan Docker Desktop berjalan selama masa pengujian.
- Atur Windows agar **tidak sleep**: Settings → Power → Screen and sleep → *Never*.
- Container sudah `restart: unless-stopped`, jadi otomatis hidup lagi bila PC reboot
  (asal Docker Desktop di-set "start on login").

## 7. Perintah harian

| Tujuan | Perintah |
|---|---|
| Lihat status | `docker compose ps` |
| Lihat URL tunnel | `docker compose logs cloudflared` |
| Lihat log aplikasi | `docker compose logs -f app` |
| Berhenti (data DB tetap) | `docker compose down` |
| Jalankan lagi | `docker compose up -d` |
| **Update sistem** (setelah ada revisi) | `git pull` lalu `docker compose up -d --build` |
| Hapus TOTAL termasuk database | `docker compose down -v` |

## 8. Opsional

**Kunci aplikasi tetap (APP_KEY).** Agar sesi login tidak ter-reset tiap restart:
```powershell
docker compose run --rm app php artisan key:generate --show
```
Salin hasilnya (mis. `base64:....`) ke baris `APP_KEY=` di `.env`, lalu
`docker compose up -d` lagi.

**URL tetap (butuh domain di Cloudflare).** Quick Tunnel memberi URL acak. Bila
punya domain yang diarahkan ke Cloudflare, bisa dibuat named tunnel dengan
subdomain tetap — beri tahu bila perlu, nanti disiapkan konfigurasinya.

## 9. Troubleshooting

- **Halaman error / 500:** set `APP_DEBUG=true` di `.env`, `docker compose up -d`,
  buka lagi untuk melihat detail; kembalikan ke `false` setelah selesai.
- **Login tidak nyangkut saat tes via http://localhost:8000:** pastikan
  `SESSION_SECURE_COOKIE=false` di `.env` (default sudah false).
- **Build gagal / port 8000 dipakai:** ganti `"8000:80"` di `docker-compose.yml`
  ke port lain, mis. `"8080:80"`.
- **Cek engine AHP (Python):** `docker compose exec app python3 --version`.
