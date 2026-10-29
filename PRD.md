# PRD & Tech Stack — Portal JDIH Universitas Insan Mahardika

## 1. Latar Belakang

Universitas Insan Mahardika (UIM) adalah nama baru dari **Institut Teknologi dan Kesehatan (ITEKES) Mahardika**. Situs JDIH (Jaringan Dokumentasi dan Informasi Hukum) yang lama, `jdih.mahardika.ac.id`, dibangun dengan stack lama dan punya beberapa bug fungsional (lihat bagian 7). Project ini adalah **pembangunan ulang** portal JDIH: branding UIM, data 40 dokumen dari situs lama sudah dimigrasi dan dibersihkan, plus panel admin baru.

JDIH menyediakan akses terpusat ke peraturan/dokumen hukum universitas (SK Rektor, SK Dekan, SK Yayasan, Standar/Kebijakan/Formulir SPMI, Pedoman, Panduan, dll), mengikuti semangat Perpres No. 33/2012 tentang Jaringan Dokumentasi dan Informasi Hukum Nasional.

## 2. Tujuan Produk

- Publik (siapa saja, tanpa login) bisa mencari & membaca dokumen hukum UIM dengan mudah — cari, filter jenis/tahun/status, lihat relasi antar-dokumen (mana yang mencabut/dicabut).
- Pengurus LP3M/pengelola JDIH bisa mengelola dokumen (tambah/ubah/hapus) tanpa perlu sentuh kode.
- Data dan relasi dokumen benar secara struktural (bug utama situs lama: field tanggal tidak nyambung, relasi mencabut/dicabut cuma teks bebas — sudah diperbaiki di sini).

## 3. Tech Stack

| Layer | Teknologi |
|---|---|
| Backend framework | **Laravel 13** (PHP ^8.3) |
| Database | **MySQL/MariaDB** |
| ORM | Eloquent |
| Frontend templating | **Blade** (server-rendered, tanpa SPA framework) |
| Styling | **Tailwind CSS v4** (via `@tailwindcss/vite`) |
| Build tool | Vite |
| Font | Instrument Sans (self-hosted via `laravel-vite-plugin/fonts`) |
| Interaktivitas ringan | Vanilla JS minimal (`resources/js/app.js`) — dropdown/nav pakai `<details>`/`<summary>` HTML native, tanpa Alpine/React/Vue |
| Auth | Session-based bawaan Laravel (`Auth::attempt`), role-based via middleware custom |
| Autentikasi lanjutan (rencana) | Google OAuth (Login with Google) — **belum diimplementasi**, ditunda sesuai keputusan tim |

**Kenapa Laravel**: dipilih supaya tim internal (familiar dengan PHP/Laravel) bisa maintain sendiri ke depannya, dibanding stack JS/serverless yang sempat dicoba di awal project.

## 4. Struktur Database

Database: MySQL/MariaDB. 4 tabel utama relevan secara bisnis: `documents`, `users`, plus tabel bawaan Laravel (`sessions`, `password_reset_tokens`, `cache`, `jobs`) yang tidak perlu dipikirkan tim non-teknis.

### Tabel `documents`

| Kolom | Tipe | Nullable | Keterangan |
|---|---|:---:|---|
| `id` | bigint, PK, auto-increment | | |
| `tipe_dokumen` | string(255) | ❌ | mis. "SK Rektor", "Standar SPMI", dll |
| `judul` | text | ❌ | |
| `nomor` | string(255) | ❌ | nomor dokumen |
| `tahun` | smallint unsigned | ❌ | |
| `status` | enum(`Berlaku`, `Tidak Berlaku`) | ❌ | default `Berlaku` |
| `tanggal_penetapan` | date | ✅ | tanggal SK ditandatangani |
| `tanggal_pengundangan` | date | ✅ | tanggal diumumkan/mulai berlaku |
| `tanggal_berakhir` | date | ✅ | |
| `tempat_penetapan` | string(255) | ✅ | |
| `penandatangan` | string(255) | ✅ | |
| `sumber` | string(255) | ✅ | mis. "Rektorat", "Ditjen Dikti" |
| `subjek` | string(255) | ✅ | |
| `bidang` | string(255) | ✅ | |
| `bahasa` | string(255) | ❌ | default "Indonesia" |
| `ringkasan` | text | ✅ | |
| `tags` | json | ✅ | array string |
| `mencabut_id` | bigint, FK → `documents.id` | ✅ | dokumen lain yang dicabut oleh dokumen ini (`ON DELETE SET NULL`) |
| `dicabut_dengan_id` | bigint, FK → `documents.id` | ✅ | dokumen lain yang mencabut dokumen ini (`ON DELETE SET NULL`) |
| `mencabut_teks` | string(255) | ✅ | fallback teks bebas kalau dokumen dirujuk tidak ada di DB ini |
| `dicabut_dengan_teks` | string(255) | ✅ | fallback teks bebas, sama seperti di atas |
| `file_url` | string(255) | ✅ | link PDF (rencana: Cloudflare R2) |
| `gdrive_url` | string(255) | ✅ | link Google Drive (sumber lama) |
| `legacy_source_id` | string(255) | ✅ | id dokumen di situs lama, untuk audit migrasi |
| `created_at`, `updated_at` | timestamp | | |
| `deleted_at` | timestamp | ✅ | soft delete — hapus dokumen tidak langsung hilang permanen dari DB |

Index: `tipe_dokumen`, `tahun`, `status`.

**Relasi mencabut/dicabut adalah foreign key sungguhan** (`mencabut_id`, `dicabut_dengan_id` → merujuk ke `documents.id` lain), bukan teks bebas seperti situs lama — jadi link antar-dokumen di halaman detail selalu benar dan tidak bisa salah ketik.

### Tabel `users`

| Kolom | Tipe | Nullable | Keterangan |
|---|---|:---:|---|
| `id` | bigint, PK, auto-increment | | |
| `name` | string(255) | ❌ | |
| `email` | string(255), unique | ❌ | dipakai untuk login |
| `email_verified_at` | timestamp | ✅ | bawaan Laravel, belum dipakai aktif |
| `password` | string(255), hashed | ❌ | |
| `role` | enum(`super_admin`, `editor`) | ❌ | default `editor` |
| `is_active` | boolean | ❌ | default `true`; akun nonaktif tidak bisa login |
| `google_id` | string(255), unique | ✅ | disiapkan untuk Login with Google (belum dipakai) |
| `picture_url` | string(255) | ✅ | disiapkan untuk Login with Google (belum dipakai) |
| `last_login_at` | timestamp | ✅ | |
| `remember_token` | string(100) | ✅ | bawaan Laravel ("ingat saya") |
| `created_at`, `updated_at` | timestamp | | |

### Data awal (seed)
40 dokumen hasil migrasi dari situs lama (`jdih.mahardika.ac.id`), diambil lewat API publiknya lalu dibersihkan field-nya. Skrip migrasi ada riwayatnya di project sebelah (`jdih` — Next.js, sudah tidak dipakai) di `scripts/migrate-legacy.mjs`; hasil akhirnya disalin ke `database/seeders/legacy-documents.json` di project ini. Seeder juga membuat 1 akun `super_admin` awal (`database/seeders/AdminUserSeeder.php`).

## 5. Fitur

### Publik (tanpa login)
- Beranda: hero + pencarian, statistik ringkas (total dokumen, dokumen berlaku, jumlah jenis, tahun terbaru), pencarian populer.
- Katalog dokumen dengan filter jenis/tahun/status + pencarian teks (judul, nomor, subjek).
- Navigasi submenu "Jenis Dokumen" dan "Tahun" (dropdown, otomatis terisi dari data).
- Halaman detail dokumen: metadata lengkap, ringkasan, status pencabutan dengan link ke dokumen terkait (kalau ada), tombol baca/unduh PDF atau ke Google Drive.
- Halaman "Tentang" — FAQ.
- Responsif (mobile/tablet/desktop), header sticky, animasi ringan (fade-in, smooth scroll, hover transition).

### Admin (`/admin`, perlu login)
- Login session-based (`/login`), role `super_admin` dan `editor`.
- CRUD dokumen (`/admin/dokumen`) — **editor** bisa tambah & ubah, **hanya super_admin** yang bisa hapus (dicek di server, bukan cuma disembunyikan di UI).
- Pengaturan akun sendiri (`/admin/akun`) — ubah nama & password.
- Kelola pengguna (`/admin/pengguna`, khusus super_admin) — tambah akun baru (password sementara di-generate otomatis), ubah role/status aktif, hapus akun (tidak bisa hapus/turunkan diri sendiri).

## 6. Role & Permission

| Aksi | Editor | Super Admin |
|---|:---:|:---:|
| Lihat portal publik | ✅ | ✅ |
| Login admin | ✅ | ✅ |
| Tambah/ubah dokumen | ✅ | ✅ |
| Hapus dokumen | ❌ | ✅ |
| Ubah akun sendiri | ✅ | ✅ |
| Kelola akun pengguna lain | ❌ | ✅ |

## 7. Perbaikan dari Situs Lama

Ditemukan lewat inspeksi API & kode situs lama sebelum migrasi:

1. **Field tanggal tidak nyambung** — frontend lama membaca `tanggal_penetapan`/`tanggal_pengundangan` yang sebenarnya tidak ada di response API-nya (field asli cuma `tanggal_berlaku`), jadi selalu tampil "-" meski data sebenarnya ada. Di sini field-nya didesain ulang jadi jelas & konsisten dari database sampai tampilan.
2. **Relasi mencabut/dicabut tidak benar-benar link** — cuma teks bebas, dan logic tampilnya salah (hanya muncul kalau dokumen berstatus "Tidak Berlaku", padahal dokumen yang *mencabut* biasanya justru berstatus "Berlaku"). Di sini sudah jadi foreign key asli + selalu tampil kalau ada relasi, terlepas dari status.
3. **Branding belum UIM** — situs lama masih pakai nama "Institut Teknologi dan Kesehatan Mahardika" di semua tempat. Sudah diupdate penuh ke UIM di sini.

## 8. Yang Belum Dikerjakan / Rencana Lanjutan

- **Login with Google (OAuth)** — kolom database sudah disiapkan, implementasi ditunda. Rencana pakai Google Cloud project **"Mahardika SSO"** milik akun `it@mahardika.ac.id` (reuse project yang sudah ada, bukan bikin baru — kuota project akun ini terbatas).
- **Penyimpanan file native** — saat ini dokumen masih pakai link Google Drive (dari data lama) untuk file PDF. Rekomendasi ke depan: pindah ke **Cloudflare R2** (gratis 10GB, tanpa biaya egress) sebagai penyimpanan utama, Drive tetap sebagai cadangan/link sumber asli.
- **Sinkronisasi data terbaru** — data 40 dokumen adalah snapshot dari situs lama pada saat migrasi. Kalau situs lama masih aktif diisi sebelum cutover, perlu migrasi ulang sebelum go-live supaya tidak ada dokumen baru yang hilang.

## 9. Deployment

- Perlu hosting yang support **PHP 8.3+, Composer, akses CLI/SSH** (untuk `php artisan migrate`, dsb) — bukan hosting statis biasa.
- Domain target: tetap **`jdih.mahardika.ac.id`** (domain lama dipertahankan meski institusi sudah berganti nama jadi UIM).
- Environment butuh: MySQL/MariaDB, Node.js (build-time saja, untuk `npm run build` asset Tailwind/Vite).
- Setelah clone repo: `composer install`, set `.env` (DB + `APP_KEY`), `npm install && npm run build`, `php artisan migrate --seed` (seeder otomatis mengisi 40 dokumen awal + 1 akun admin — set `ADMIN_SEED_EMAIL`/`ADMIN_SEED_PASSWORD` di `.env` supaya passwordnya bukan random).

## 10. Repository

- GitHub (private): `https://github.com/bangjamz/jdih-uim-laravel`
