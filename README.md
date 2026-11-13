# SalesLedger

<p align="center">
  <strong>Aplikasi pembukuan &amp; scanning faktur berbasis AI untuk tim sales.</strong><br>
  Dibangun dengan Laravel 13, Tailwind CSS 4, Gemini Vision, Supabase Storage, dan Brevo.
</p>

---

## Daftar Isi

- [Tentang Project](#tentang-project)
- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Konsep & Arsitektur](#konsep--arsitektur)
- [Alur Kerja Utama](#alur-kerja-utama)
- [Skema Database](#skema-database)
- [Struktur Project](#struktur-project)
- [Daftar Route](#daftar-route)
- [Instalasi](#instalasi)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Build & Deploy (Docker)](#build--deploy-docker)
- [Testing](#testing)
- [Catatan Keamanan & Operasional](#catatan-keamanan--operasional)

---

## Tentang Project

**SalesLedger** adalah aplikasi web mobile-first (PWA) untuk membantu pemilik usaha dan tim sales dalam:

1. **Mencatat faktur/nota secara otomatis** — sales cukup memotret nota, lalu AI (Google Gemini Vision) mengekstrak nama toko, nomor faktur, dan total tagihan.
2. **Mengelola toko pelanggan** — admin menambahkan toko, menugaskan sales, dan memantau status pembayaran tiap toko.
3. **Memantau pembukuan** — admin melihat rekap tagihan, pembayaran, sisa kredit, serta mengunduh laporan Excel dan invoice PDF.
4. **Mencatat pembayaran bertahap** — sales dapat mencatat cicilan pembayaran hingga faktur lunas.

Aplikasi menerapkan model **multi-tenant sederhana**: setiap admin memiliki kode referal unik, dan setiap sales terhubung ke satu admin melalui kode tersebut. Data antar-admin terisolasi secara ketat — admin hanya bisa melihat toko, sales, dan faktur miliknya sendiri.

---

## Fitur Utama

### Autentikasi & Akun

- Registrasi dan login menggunakan **email atau username**.
- **Verifikasi email via OTP 6 digit** yang dikirim lewat email (berlaku 2 menit, tersimpan di cache).
- **Lupa password via OTP** (berlaku 5 menit).
- Kirim ulang OTP saat verifikasi gagal/kadaluarsa.
- Pemilihan role setelah verifikasi:
  - **Admin**: membuat kode referal unik (`admin_code`).
  - **Sales**: memasukkan kode referal admin untuk terhubung (`parent_admin_id`).
- Ganti password dari halaman profil (validasi `current_password`).

### Role Admin

- **Dashboard** berisi statistik hari ini/bulan ini: total nota, total tagihan, total dibayar, sisa kredit, jumlah lunas/belum lunas, serta 5 riwayat terbaru.
- **Pembukuan**:
  - Pencarian nomor faktur / nama toko.
  - Filter waktu (hari ini, minggu ini, semua) dan filter status (lunas/belum lunas).
  - Filter rentang tanggal kustom.
  - Detail faktur + preview/download invoice PDF.
  - **Export laporan ke Excel (.xlsx)** berdasarkan rentang tanggal.
- **Manajemen Toko** (CRUD):
  - Daftar toko beserta total tagihan, total dibayar, dan status pembayaran agregat.
  - Detail toko: riwayat faktur, statistik keuangan, sales yang memegang toko.
  - Soft delete (toko dihapus dari daftar aktif, data historis tetap aman).
- **Manajemen Sales**: daftar sales di bawah admin, jumlah toko pegangan, jumlah nota belum lunas, serta detail per sales.
- **Profil**: edit nama/no. telepon, ubah kode referal (custom code), ganti password.

### Role Sales

- **Dashboard** berisi ringkasan nota pribadi, total tagihan, pembayaran, sisa kredit, dan riwayat terbaru.
- **Upload/Scan Nota**:
  - Foto nota (JPEG/PNG, maks. 5 MB) dikirim ke Gemini 2.5 Flash untuk diekstrak.
  - Hasil ekstraksi (nama toko, nomor faktur, total tagihan) ditampilkan untuk direview/diedit.
  - Sales memilih toko, mengisi metode bayar (Cash/Transfer) dan nominal pembayaran awal.
  - Foto nota disimpan ke **Supabase Storage**, lalu URL-nya disimpan di database.
- **Riwayat Faktur**:
  - Pencarian dan filter status.
  - Dikelompokkan per tanggal (Hari ini, Kemarin, tanggal lengkap).
  - Detail faktur + invoice PDF (preview/download).
  - **Catat pembayaran** (cicilan) dengan validasi anti-overpay.
- **Toko Pegangan**: memilih toko mana saja (milik admin terkait) yang dipegang sales.
- **Profil**: edit data diri, ganti password.

### Notifikasi

Notifikasi in-app berbasis channel `database`:

- Admin menerima notifikasi saat sales menginput **faktur baru** atau **mencatat pembayaran**.
- Sales menerima notifikasi saat admin **menambah/mengubah/menghapus toko**.
- Halaman notifikasi dengan indikator unread, tandai dibaca, dan tandai semua dibaca.

### Lainnya

- **Invoice PDF** dibuat dengan Dompdf.
- **Export Excel** memakai `App\Support\XlsxWriter` (writer XLSX minimal tanpa dependency `ext-zip`).
- **PWA** (Progressive Web App) via `vite-plugin-pwa`, lengkap dengan halaman `offline.html`.
- **Cache user login** (`CachedUserProvider`) untuk mengurangi query database pada request terautentikasi.
- UI mobile-first dengan lebar maksimum `max-w-md`, Tailwind CSS 4, dan bottom navigation terpisah untuk admin/sales.

---

## Tech Stack

| Komponen | Teknologi |
| --- | --- |
| Framework | Laravel 13 (PHP ^8.3) |
| Database | PostgreSQL (`pdo_pgsql`, `pgsql`) — query memakai `ILIKE` |
| Frontend | Blade, Tailwind CSS 4, Vite 8, JavaScript vanilla |
| PWA | `vite-plugin-pwa` + Workbox |
| OCR / AI | Google Gemini 2.5 Flash (`generativelanguage.googleapis.com`) |
| Object Storage | Supabase Storage (S3-compatible, via Flysystem AWS S3) |
| Email | Brevo API (utama) atau SMTP (fallback) |
| PDF | Dompdf 3 |
| Testing | PHPUnit 12 |
| Code Style | Laravel Pint |

---

## Konsep & Arsitektur

### Isolasi data Admin–Sales

```
Admin (admin_code unik)
  ├── memiliki banyak Toko (tokos.admin_id)
  ├── memiliki banyak Sales (users.parent_admin_id)
  └── Sales
        ├── memegang banyak Toko (pivot sales_tokos)
        └── menginput banyak Faktur (fakturs.sales_id)
```

- `User::sales()` → daftar sales milik admin.
- `User::parentAdmin()` → admin pemilik sales.
- `Toko::ownedByAdmin()` → scope agar admin/sales hanya mengakses toko milik admin terkait.
- `Faktur::forAdmin()` / `forSales()` / `visibleTo()` → scope isolasi data faktur.
- `TokoPolicy` → otorisasi edit/hapus toko hanya untuk admin pemilik.

### Middleware

- `role:<admin|sales>` (`App\Http\Middleware\CheckRole`) memisahkan area admin dan sales.
- Route admin dan sales dimuat dari file terpisah (`routes/admin.php`, `routes/sales.php`).

### Performa

- Agregat dashboard dihitung menggunakan **window function** SQL (`count() over ()`, `sum() over ()`) dalam satu query.
- `CachedUserProvider` menyimpan objek user di cache selama `AUTH_USER_CACHE_TTL` detik (default 300) dan otomatis invalidated saat user disimpan/dihapus.
- Relasi `withCount`/`withSum` digunakan pada daftar toko dan sales untuk menghindari N+1.

---

## Alur Kerja Utama

### 1. Registrasi & Verifikasi

1. User mengisi form signup → `POST /api/register`.
2. Akun dibuat, user langsung login, lalu OTP 6 digit dikirim ke email.
3. OTP disimpan di cache dengan key `otp_<email>` (2 menit).
4. User memasukkan OTP → `POST /api/verify-otp` → `email_verified_at` diisi.
5. User memilih role:
   - Admin → `POST /api/set-admin` (membuat `admin_code`).
   - Sales → `POST /api/set-sales` (mencari admin berdasarkan `admin_code`, menyimpan `parent_admin_id`).

### 2. Input Faktur via AI

1. Sales membuka `/sales/input` dan memilih/memotret nota.
2. `POST /sales/api/scan` mengirim gambar ke Gemini dengan prompt ekstraksi.
3. Gemini mengembalikan JSON: `Nama Toko`, `Nomor Faktur`, `Total Tagihan`.
4. Sales mereview, memilih toko (`/sales/toko/search`), mengisi metode bayar & nominal.
5. `POST /sales/faktur/simpan`:
   - Upload foto ke disk `supabase`.
   - Simpan `Faktur` + status (`lunas` bila `total_dibayar >= total_tagihan`).
   - Kirim notifikasi ke admin.

### 3. Catat Pembayaran

1. Sales membuka riwayat → "Catat Pembayaran".
2. Nominal baru ditambahkan ke `total_dibayar` (dibatasi maksimal `total_tagihan`).
3. Status diperbarui menjadi `lunas` bila sudah terbayar penuh.
4. Admin menerima notifikasi pembayaran.

### 4. Export Pembukuan

1. Admin memilih rentang tanggal.
2. `GET /admin/pembukuan/export` memvalidasi tanggal, mengambil faktur milik admin.
3. Data disusun menjadi file XLSX (judul, header, total, auto-filter, format angka).
4. File diunduh sebagai `Pembukuan_YYYYMMDD_sd_YYYYMMDD.xlsx`.

---

## Skema Database

### `users`

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | bigint | PK |
| `full_name` | string | Nama lengkap |
| `username` | string unique | Login alternatif |
| `email` | string unique | Login & verifikasi |
| `phone_number` | string nullable | No. telepon |
| `email_verified_at` | timestamp nullable | Status verifikasi OTP |
| `password` | string | Hash password |
| `role` | enum(`admin`,`sales`) nullable | Diisi saat pilih role |
| `admin_code` | string unique nullable | Kode referal admin |
| `parent_admin_id` | FK `users.id` nullable | Admin pemilik sales |
| `remember_token`, `timestamps` | | |

### `tokos`

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | bigint | PK |
| `admin_id` | FK `users.id` nullable | Pemilik toko |
| `nama_toko` | string | |
| `no_telp` | string(20) nullable | |
| `alamat` | text nullable | |
| `provinsi`, `kota` | string nullable | |
| `kode_pos` | string(10) nullable | |
| `timestamps`, `deleted_at` | | Soft delete |

### `fakturs`

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | bigint | PK |
| `toko_id` | FK `tokos.id` | Toko tujuan |
| `nomor_faktur` | string nullable | |
| `tanggal_nota` | date nullable | |
| `tanggal_pembayaran` | date nullable | |
| `metode_bayar` | string nullable | Cash/Transfer |
| `total_tagihan` | decimal(15,2) nullable | |
| `total_dibayar` | decimal(15,2) default 0 | Akumulasi pembayaran |
| `status` | enum(`lunas`,`belum_lunas`) | |
| `foto_url` | string nullable | URL Supabase Storage |
| `sales_id` | FK `users.id` nullable | Sales penginput |
| `timestamps` | | |

### `sales_tokos` (pivot)

`id`, `user_id` (FK users), `toko_id` (FK tokos), `timestamps`.

### Tabel pendukung

`notifications`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `sessions`, `password_reset_tokens`.

---

## Struktur Project

```
app/
├── Auth/CachedUserProvider.php          # Provider auth dengan cache
├── Http/
│   ├── Controllers/
│   │   ├── InvoiceController.php         # Preview & download invoice PDF
│   │   ├── NotificationController.php    # Daftar & tandai notifikasi
│   │   ├── admin/                        # Dashboard, pembukuan, toko, sales
│   │   ├── auth/                         # Auth, role, ganti password
│   │   └── sales/                        # OCR, riwayat, toko pegangan
│   ├── Middleware/CheckRole.php          # Guard role admin/sales
│   └── Requests/TokoRequest.php          # Validasi data toko
├── Mail/                                 # OtpMail, ResetPasswordMail
├── Models/                               # Faktur, Toko, User
├── Notifications/SystemNotification.php  # Notifikasi database
├── Policies/TokoPolicy.php               # Otorisasi toko
├── Providers/AppServiceProvider.php      # Registrasi provider + force HTTPS
├── Services/BrevoMailService.php         # Brevo API / SMTP fallback
└── Support/
    ├── Format.php                        # Helper rupiah & tanggal
    └── XlsxWriter.php                    # Generator XLSX tanpa ext-zip

routes/
├── web.php      # Root, profil global, notifikasi, grouping role
├── auth.php     # Login, register, OTP, reset password, pilih role
├── admin.php    # Semua route area admin
├── sales.php    # Semua route area sales
└── console.php

resources/views/
├── admin/       # dashboard, pembukuan, toko, sales, profile
├── sales/       # home, ocr, riwayat, toko-pegangan, profile
├── auth/        # landing, login, signup, verify-email, select-role, forgot-password
├── components/  # head, back-header, search-input, success-icon
├── emails/      # template OTP & reset password
├── invoices/    # preview & PDF invoice
├── notifications/
├── partials/    # bottom nav admin/sales, form profil, loading
└── global-profile/
```

---

## Daftar Route

### Public & Auth

| Method | URI | Keterangan |
| --- | --- | --- |
| GET | `/` | Landing / redirect sesuai role |
| GET | `/login`, `/signup`, `/forgot-password` | Halaman auth |
| POST | `/api/register`, `/api/login` | Proses auth |
| POST | `/api/forgot-password/send-otp`, `/api/forgot-password/reset` | Reset password |
| POST | `/logout` | Logout |
| GET | `/verify-email` | Halaman verifikasi OTP |
| POST | `/api/verify-otp`, `/api/resend-otp` | Verifikasi/kirim ulang OTP |
| GET | `/select-role` | Pemilihan role |
| POST | `/api/set-admin`, `/api/set-sales` | Set role |
| GET/POST | `/profile/change-password` | Ganti password |
| GET/POST | `/notifications*` | Notifikasi |

### Area Sales (`/sales`, middleware `role:sales`)

| Method | URI | Keterangan |
| --- | --- | --- |
| GET | `/sales/home` | Dashboard sales |
| GET/POST | `/sales/profile`, `/sales/profile/edit`, `/sales/profile/update` | Profil |
| GET | `/sales/input` | Halaman scan nota |
| POST | `/sales/api/scan` | OCR Gemini |
| POST | `/sales/faktur/simpan` | Simpan faktur |
| GET | `/sales/toko/search` | Autocomplete toko |
| GET | `/sales/riwayat`, `/sales/riwayat/{id}` | Riwayat & detail |
| GET/POST | `/sales/riwayat/{id}/catat-pembayaran` | Catat pembayaran |
| GET | `/sales/riwayat/{id}/invoice[/download]` | Invoice PDF |
| GET/POST | `/sales/toko-pegangan` | Toko pegangan |

### Area Admin (`/admin`, middleware `role:admin`)

| Method | URI | Keterangan |
| --- | --- | --- |
| GET | `/admin/dashboard` | Dashboard admin |
| GET | `/admin/pembukuan` | List pembukuan |
| GET | `/admin/pembukuan/export` | Export Excel |
| GET | `/admin/pembukuan/{id}` | Detail faktur |
| GET | `/admin/pembukuan/{id}/invoice[/download]` | Invoice PDF |
| GET/POST | `/admin/toko*` | CRUD toko |
| GET/POST | `/admin/profile*` | Profil admin |
| GET/POST | `/admin/profile/custom-code*` | Kode referal |
| GET | `/admin/sales`, `/admin/sales/{id}` | Daftar & detail sales |

---

## Instalasi

### Prasyarat

- PHP **8.3+** dengan ekstensi `pdo_pgsql`, `pgsql`, `mbstring`, `xml`, `gd`, `bcmath`
- Composer 2
- Node.js 20+ dan npm
- PostgreSQL (disarankan; query aplikasi memakai `ILIKE`)
- Akun layanan opsional: Google AI Studio (Gemini), Supabase, Brevo

### Langkah Cepat

```bash
composer install
cp .env.example .env
php artisan key:generate
# sesuaikan konfigurasi database & API key di .env
php artisan migrate
npm install
npm run build
php artisan serve
```

Atau gunakan script bawaan:

```bash
composer run setup
```

Script `setup` menjalankan: `composer install`, menyalin `.env`, generate key, migrate, `npm install`, dan `npm run build`.

### Storage Supabase

1. Buat bucket di Supabase Storage (misalnya `faktur`).
2. Ambil kredensial S3-compatible (Access Key, Secret, Region, Endpoint).
3. Isi variabel `SUPABASE_*` di `.env`.
4. Pastikan disk `supabase` pada `config/filesystems.php` sudah benar (sudah disediakan).

---

## Konfigurasi Environment

Salin `.env.example` menjadi `.env`, lalu sesuaikan variabel berikut:

### Aplikasi & Database

| Variabel | Contoh | Keterangan |
| --- | --- | --- |
| `APP_NAME` | `SalesLedger` | Nama aplikasi |
| `APP_ENV` | `local` / `production` | `production` memaksa HTTPS |
| `APP_URL` | `http://localhost` | URL dasar |
| `DB_CONNECTION` | `pgsql` | Wajib PostgreSQL untuk `ILIKE` |
| `DB_URL` | `postgres://...` | Alternatif koneksi tunggal |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | | Kredensial DB |
| `SESSION_DRIVER` | `cookie` | |
| `CACHE_STORE` | `file` | Dipakai menyimpan OTP |
| `QUEUE_CONNECTION` | `database` | |
| `FILESYSTEM_DISK` | `local` | Foto faktur memakai disk `supabase` |

### AI & Storage

| Variabel | Keterangan |
| --- | --- |
| `GEMINI_API_KEY` | API key Google Gemini (OCR nota) |
| `SUPABASE_STORAGE_KEY` | Access key S3 Supabase |
| `SUPABASE_STORAGE_SECRET` | Secret key S3 Supabase |
| `SUPABASE_STORAGE_REGION` | Region project |
| `SUPABASE_STORAGE_BUCKET` | Nama bucket |
| `SUPABASE_STORAGE_ENDPOINT` | Endpoint S3 Supabase |
| `SUPABASE_PROJECT_URL` | URL publik untuk membentuk URL foto |

### Email / Brevo

| Variabel | Keterangan |
| --- | --- |
| `BREVO_API_KEY` | Jika diisi, email dikirim lewat Brevo API (disarankan) |
| `BREVO_TIMEOUT` | Timeout request API (detik) |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_SCHEME` | Fallback SMTP |
| `MAIL_FROM_ADDRESS` | **Harus** email pengirim yang sudah diverifikasi di Brevo |
| `MAIL_FROM_NAME` | Nama pengirim |

> Jika `BREVO_API_KEY` kosong, aplikasi otomatis memakai Laravel Mail/SMTP. Jika keduanya tidak lengkap, pendaftaran tetap berhasil dan pengguna bisa memakai tombol "Kirim Ulang Kode".

### Lainnya

| Variabel | Keterangan |
| --- | --- |
| `AUTH_USER_CACHE_TTL` | Lama cache user login (detik, default 300) |
| `AWS_*` | Opsional untuk disk S3 umum |

---

## Menjalankan Aplikasi

Mode development (server + queue listener + log viewer + Vite sekaligus):

```bash
composer run dev
```

Perintah tersebut menjalankan:

- `php artisan serve`
- `php artisan queue:listen --tries=1 --timeout=0`
- `php artisan pail --timeout=0`
- `npm run dev`

Atau jalankan manual:

```bash
php artisan serve
npm run dev
```

---

## Build & Deploy (Docker)

`Dockerfile` disediakan untuk deployment (mis. Railway/Wasmer):

```bash
docker build -t sales-ledger .
docker run -p 8080:8080 --env-file .env sales-ledger
```

Image menjalankan:

1. PHP 8.3 CLI + ekstensi `pdo_pgsql`, `pgsql`, `pdo_mysql`, `gd`, `bcmath`, dll.
2. `composer install --no-dev --optimize-autoloader`.
3. Node.js 20 + `npm install && npm run build`.
4. `php artisan serve --host=0.0.0.0 --port=${PORT:-8080}`.

Saat `APP_ENV=production`, `AppServiceProvider` memaksa skema URL HTTPS.

---

## Pemeliharaan Foto Faktur

Foto faktur disimpan di bucket Supabase (`fakturs`), sedangkan database hanya menyimpan `foto_url` dan `foto_path`. Penghapusan faktur **melalui aplikasi/Eloquent** otomatis menghapus filenya dari storage. Namun, jika baris `fakturs` dihapus langsung lewat Supabase Table Editor atau SQL Editor, event model Laravel tidak berjalan sehingga file bisa tertinggal.

Gunakan command berikut untuk membersihkan file yatim (tidak lagi direferensikan oleh tabel `fakturs`):

```bash
# Lihat daftar file yang akan dihapus (tanpa menghapus)
php artisan faktur:purge-orphans --dry-run --older-than=0

# Hapus semua file yang tidak punya data faktur
php artisan faktur:purge-orphans --older-than=0
```

Opsi `--older-than` (default `60` menit) mencegah file yang baru diunggah ikut terhapus. Command ini juga terjadwal otomatis setiap hari melalui scheduler (`routes/console.php`). Pastikan cron `php artisan schedule:run` berjalan di server.

## Testing

```bash
composer test
# atau
php artisan test
```

Contoh test yang tersedia: `tests/Feature/BrevoMailServiceTest.php` menguji pengiriman OTP lewat Brevo API dan fallback ke Laravel Mail ketika API key kosong.

---

## Catatan Keamanan & Operasional

- **Isolasi data** dilakukan berlapis: middleware role, scope query (`forAdmin`, `forSales`, `ownedByAdmin`), dan policy.
- **OTP** disimpan di cache (bukan di database) dan dihapus setelah dipakai/kadaluarsa.
- **Upload file** divalidasi sebagai gambar `jpeg/png/jpg` maksimal 5 MB dan dicek `isValid()` sebelum diunggah.
- **Anti-overpay**: `total_dibayar` selalu dibatasi maksimal `total_tagihan`.
- **Faktur sudah lunas** tidak dapat diedit pembayarannya.
- **Soft delete toko** menjaga integritas riwayat faktur.
- Jangan commit file `.env`; semua kredensial (DB, Gemini, Supabase, Brevo) harus disimpan aman.
- Karena query memakai `ILIKE` dan window function, **PostgreSQL** adalah database yang didukung; SQLite pada `.env.example` hanya cocok untuk eksperimen terbatas.
- Untuk produksi, jalankan queue worker dan scheduler sesuai kebutuhan (`QUEUE_CONNECTION=database`).
