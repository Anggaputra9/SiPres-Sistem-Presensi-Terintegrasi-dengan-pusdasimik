# Sistem Presensi

Aplikasi presensi (kehadiran) yang mendukung 3 role: **Admin**, **Guru**, dan **Murid**.
Terintegrasi dengan **Pusat Data API** untuk validasi mahasiswa & dosen.

## 🎯 Fitur Utama

### 👤 Admin
- Konfigurasi API token Pusat Data via UI (tanpa edit `.env`)
- Test koneksi ke Pusat Data
- Manajemen user (CRUD guru & murid)
- Dashboard ringkasan sistem (total user, kelas, sesi, kehadiran)

### 👨‍🏫 Guru
- Membuat kelas & mendaftarkan murid (auto-fetch dari Pusat Data via NIM)
- Membuat sesi presensi dengan QR code & kode referral
- Menutup/membuka sesi presensi
- Tandai kehadiran manual (jika murid tidak bisa scan)

### 🎓 Murid
- Login pakai NIM (auto-provision dari Pusat Data)
- Scan QR / input kode referral untuk presensi
- Lihat riwayat kehadiran

## 🚀 Setup

### 1. Konfigurasi `.env`
```env
DB_DATABASE=sistem_presensi
DB_USERNAME=root
DB_PASSWORD=

PUSAT_DATA_API_URL=http://localhost:8000/api
PUSAT_DATA_API_TOKEN=                # boleh kosong, diisi via halaman admin
PUSAT_DATA_API_TIMEOUT=10
```

### 2. Install dependency & generate key
```bash
composer install
php artisan key:generate
```

### 3. Buat database
```bash
E:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE sistem_presensi;"
```

### 4. Migrate & seed
```bash
php artisan migrate
php artisan db:seed
```

Seeder akan membuat:
- **Admin**  → username `admin`, password `admin123`
- **Guru** demo (jika token Pusat Data sudah valid) → NIP `198703152012012001`
- **Murid** demo (jika token Pusat Data sudah valid) → NIM `2021001..2021007`

> Jika token belum dikonfigurasi, seeder hanya membuat user admin. Lanjutkan ke langkah 5.

### 5. Jalankan aplikasi
```bash
php artisan serve --port=8001
```
Akses: <http://localhost:8001>

### 6. Konfigurasi API Token (sekali setelah deploy)
1. Login sebagai **Admin** (`admin` / `admin123`)
2. Buka menu **Konfigurasi API**
3. Minta token dari admin **Pusat Data** (lihat bagian berikutnya)
4. Paste token & klik **Simpan**, lalu **Test Koneksi**
5. Jalankan ulang `php artisan db:seed` untuk provision guru & murid demo (opsional)

## 🔐 Cara Mendapatkan API Token dari Pusat Data

1. Login ke sistem **Pusat Data** sebagai admin (`admin` / `password`)
2. Buka menu **API Clients**
3. Buat client baru (misal: `Sistem Presensi`) — atau pakai yang sudah ada
4. Klik **Issue Token** → beri nama (misal: `presensi-prod`)
5. **Copy token plaintext** yang ditampilkan (hanya muncul sekali)
6. Kirim ke admin Sistem Presensi via channel aman
7. Admin Sistem Presensi paste token di menu **Konfigurasi API**

Token disimpan di tabel `system_settings` dan di-load otomatis oleh `PusatDataClient` (override nilai `.env`).

## 🧑‍💻 Akun Demo

| Role  | Username             | Password               |
|-------|----------------------|------------------------|
| Admin | `admin`              | `admin123`             |
| Guru  | `198703152012012001` | `198703152012012001`   |
| Murid | `2021001`            | `2021001`              |
| Murid | `2021002` … `2021007`| (sama dengan NIM)      |

## 🗄️ Skema Database

- `users` — semua user (admin/guru/murid) dengan kolom `role` (enum)
- `system_settings` — key/value untuk konfigurasi runtime (mis. API token)
- `kelas` — kelas yang dibuat guru
- `kelas_murid` — pivot enrolment murid ke kelas
- `sesi_presensi` — sesi presensi tiap kelas (punya `kode_referal` & QR)
- `kehadiran` — log presensi murid per sesi

## 🔗 Integrasi Pusat Data

`PusatDataClient` (di `app/Services`) memanggil endpoint:
- `GET /api/mahasiswa/{nim}` — lookup murid
- `GET /api/dosen/{nip}` — lookup guru

Dipakai oleh:
- **LoginController** — auto-provision user saat login pertama
- **KelasController** — tambah murid ke kelas
- **DemoSeeder** — bootstrap data demo

## 📝 Catatan
- Data master mahasiswa/dosen TIDAK disimpan permanen — selalu validasi ke Pusat Data
- Field `last_synced_at` di tabel `users` mencatat kapan data terakhir disinkronkan
- Token API Pusat Data **wajib** dikonfigurasi sebelum guru/murid bisa login pertama kali
