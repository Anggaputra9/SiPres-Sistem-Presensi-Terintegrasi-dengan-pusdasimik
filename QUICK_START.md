# ⚡ Quick Start - Integrasi Multi-Laptop

## 🎯 Tujuan
Menghubungkan multiple laptop (WiFi berbeda) melalui Pusat Data sebagai server sentral.

---

## 📋 Ringkasan Setup

### Server (Pusat Data) - 1 Laptop
```bash
cd pusat-data
php artisan serve                                      # Port 8000
devtunnel host --port 8000 --protocol https            # Dapat URL publik
```

### Client (Sistem Presensi) - Multiple Laptop
```bash
cd sistem-presensi
# Edit .env → isi PUSAT_DATA_API_URL dan TOKEN
php artisan serve --port=8001
php artisan presensi:sync-to-pusat                     # Sync data
```

---

## 🚀 Step-by-Step (5 Menit)

### 1️⃣ Setup Server (Pusat Data)

```bash
cd pusat-data
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Terminal baru:
```bash
devtunnel host --port 8000 --protocol https
# Catat URL: https://xxxxx-8000.devtunnels.ms
```

### 2️⃣ Generate Token

```bash
php artisan tinker
```
```php
$client = App\Models\ApiClient::firstOrCreate(
    ['slug' => 'sistem-presensi'],
    ['nama' => 'Sistem Presensi', 'is_active' => true]
);
echo $client->createToken('access')->plainTextToken;
```

### 3️⃣ Setup Client (Setiap Laptop)

```bash
cd sistem-presensi
composer install
cp .env.example .env
nano .env  # Edit konfigurasi
```

Edit `.env`:
```env
PUSAT_DATA_API_URL=https://xxxxx-8000.devtunnels.ms/api
PUSAT_DATA_API_TOKEN=12|xxxxxxxxxxxxxxxxxxxxx
```

```bash
php artisan key:generate
php artisan migrate
php artisan serve --port=8001
```

### 4️⃣ Test Koneksi

```bash
php artisan tinker
```
```php
app(\App\Services\PusatDataClient::class)->ping(); // true = berhasil
```

### 5️⃣ Sync Data

```bash
php artisan presensi:sync-to-pusat
```

---

## ✅ Verifikasi

- [ ] Pusat Data running (php artisan serve)
- [ ] DevTunnels running dan dapat URL
- [ ] Token di-generate
- [ ] Sistem Presensi .env configured
- [ ] Test ping() return true
- [ ] Sync berhasil tanpa error

---

## 🔧 Commands Penting

### Di Sistem Presensi:
```bash
# Sync data ke Pusat Data
php artisan presensi:sync-to-pusat

# Test koneksi
php artisan tinker
>>> app(\App\Services\PusatDataClient::class)->ping()

# Cek status sinkronisasi
>>> app(\App\Services\PusatDataClient::class)->cekStatusSinkronisasi()
```

### Di Pusat Data:
```bash
# Lihat log presensi
php artisan tinker
>>> App\Models\LogPresensi::count()
>>> App\Models\LogPresensi::latest()->first()
```

---

## 🐛 Troubleshooting Cepat

| Masalah | Solusi |
|---------|--------|
| Connection refused | Cek Pusat Data & DevTunnels running |
| Unauthenticated | Cek token di .env benar |
| URL not found | Update URL DevTunnels di .env |
| Sync gagal | Cek internet & credentials |

---

## 📖 Dokumentasi Lengkap

Lihat: `INTEGRASI_MULTI_LAPTOP.md`

---

**Happy Coding! 🎉**
