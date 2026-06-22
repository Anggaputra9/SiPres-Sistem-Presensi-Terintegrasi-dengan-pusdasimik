# 🌐 Panduan Integrasi Multi-Laptop dengan WiFi Berbeda

## 📖 Ringkasan
Sistem ini memungkinkan multiple laptop dengan WiFi berbeda untuk saling berbagi data presensi melalui **Pusat Data** sebagai server sentral.

## 🏗️ Arsitektur Sistem

```
┌─────────────────┐         ┌─────────────────┐         ┌─────────────────┐
│  Laptop 1       │         │  Laptop 2       │         │  Laptop 3       │
│  WiFi A         │         │  WiFi B         │         │  WiFi C         │
│  sistem-presensi│         │  sistem-presensi│         │  sistem-presensi│
└────────┬────────┘         └────────┬────────┘         └────────┬────────┘
         │                           │                           │
         │         HTTPS/Internet    │                           │
         └───────────────┬───────────┴───────────────────────────┘
                         │
                         ↓
              ┌──────────────────────┐
              │   PUSAT DATA         │
              │   (Server Sentral)   │
              │   - DevTunnels       │
              │   - Cloud VPS        │
              └──────────────────────┘
```

---

## 🚀 Setup: 2 Metode

### Metode A: Development (DevTunnels) - GRATIS
**Cocok untuk:** Testing, development, demo
**Kelebihan:** Cepat, gratis, mudah
**Kekurangan:** Tidak stabil untuk production

### Metode B: Production (Cloud Server)
**Cocok untuk:** Production, penggunaan jangka panjang
**Kelebihan:** Stabil, cepat, reliable
**Kekurangan:** Butuh biaya hosting

---

## 🔧 METODE A: Setup dengan DevTunnels

### Langkah 1: Setup Pusat Data (Laptop Server)

```bash
cd pusat-data

# Install dependencies (jika belum)
composer install

# Copy .env
cp .env.example .env

# Generate key
php artisan key:generate

# Setup database
php artisan migrate

# Jalankan server
php artisan serve
```

### Langkah 2: Install & Jalankan DevTunnels

```bash
# Login ke Microsoft account (hanya sekali)
devtunnel user login

# Buat tunnel (hanya sekali)
devtunnel create pusat-data --allow-anonymous

# Jalankan tunnel
devtunnel host --port 8000 --protocol https
```

**Output contoh:**
```
Hosting port: 8000
Connect via browser: https://abc123xyz-8000.devtunnels.ms
```

**CATAT URL ini!** Contoh: `https://abc123xyz-8000.devtunnels.ms`

### Langkah 3: Update .env Pusat Data

Edit file `pusat-data/.env`:

```env
APP_URL=https://abc123xyz-8000.devtunnels.ms
SANCTUM_STATEFUL_DOMAINS=abc123xyz-8000.devtunnels.ms,localhost,127.0.0.1
```

```bash
# Clear cache
php artisan config:clear
```

### Langkah 4: Generate API Token

```bash
cd pusat-data
php artisan tinker
```

Di tinker, jalankan:
```php
// Buat API Client untuk sistem-presensi
$client = App\Models\ApiClient::create([
    'slug' => 'sistem-presensi',
    'nama' => 'Sistem Presensi',
    'is_active' => true
]);

// Generate token
$token = $client->createToken('access')->plainTextToken;

// Tampilkan token
echo $token;
```

**COPY TOKEN INI!** Contoh: `12|AbCdEfGhIjKlMnOpQrStUvWxYz1234567890`

### Langkah 5: Setup Sistem Presensi (Semua Laptop Client)

Di setiap laptop yang akan menjalankan sistem-presensi:

```bash
cd sistem-presensi

# Copy .env
cp .env.example .env

# Edit .env
```

Edit file `sistem-presensi/.env`:

```env
APP_NAME="Sistem Presensi Laptop 1"  # Ganti sesuai laptop
APP_URL=http://localhost:8001

# INTEGRASI PUSAT DATA
PUSAT_DATA_API_URL=https://abc123xyz-8000.devtunnels.ms/api
PUSAT_DATA_API_TOKEN=12|AbCdEfGhIjKlMnOpQrStUvWxYz1234567890
```

```bash
# Install dependencies
composer install

# Generate key
php artisan key:generate

# Migrate database
php artisan migrate

# Jalankan server
php artisan serve --port=8001
```

### Langkah 6: Test Koneksi

```bash
cd sistem-presensi
php artisan tinker
```

```php
// Test ping ke Pusat Data
$client = app(\App\Services\PusatDataClient::class);
$client->ping(); // Harus return true

// Cek status sinkronisasi
$status = $client->cekStatusSinkronisasi();
dd($status);
```

### Langkah 7: Sinkronisasi Data

**Push data dari sistem-presensi ke pusat-data:**

```bash
php artisan presensi:sync-to-pusat
```

---

## 🌍 METODE B: Setup dengan Cloud Server (Production)

### Pilihan Cloud Provider:

1. **Niagahoster** (Rp 40.000/bulan)
2. **DigitalOcean** ($6/bulan)
3. **Vultr** ($6/bulan)
4. **AWS Lightsail** ($3.5/bulan)

### Langkah Setup di VPS:

```bash
# 1. SSH ke server
ssh root@your-server-ip

# 2. Install requirements
apt update
apt install nginx php8.2 php8.2-fpm php8.2-mysql mysql-server git composer

# 3. Clone project
cd /var/www
git clone your-repo pusat-data
cd pusat-data

# 4. Install dependencies
composer install --no-dev

# 5. Setup .env
cp .env.example .env
nano .env
```

Edit `.env`:
```env
APP_URL=https://pusatdata.yourdomain.com
DB_CONNECTION=mysql
DB_DATABASE=pusat_data
DB_USERNAME=root
DB_PASSWORD=your_password
```

```bash
# 6. Setup database
php artisan key:generate
php artisan migrate --force

# 7. Configure Nginx
nano /etc/nginx/sites-available/pusat-data
```

Nginx config:
```nginx
server {
    listen 80;
    server_name pusatdata.yourdomain.com;
    root /var/www/pusat-data/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

```bash
# 8. Enable site
ln -s /etc/nginx/sites-available/pusat-data /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx

# 9. Setup SSL (HTTPS)
apt install certbot python3-certbot-nginx
certbot --nginx -d pusatdata.yourdomain.com
```

### Update Sistem Presensi untuk Production

Edit `sistem-presensi/.env` di semua laptop:

```env
PUSAT_DATA_API_URL=https://pusatdata.yourdomain.com/api
PUSAT_DATA_API_TOKEN=your_token_here
```

---

## 🧪 Testing Integrasi

### Test 1: Koneksi API

```bash
# Dari sistem-presensi
curl -H "Authorization: Bearer YOUR_TOKEN" \
     https://abc123xyz-8000.devtunnels.ms/api/me
```

Expected response:
```json
{
  "id": 1,
  "slug": "sistem-presensi",
  "nama": "Sistem Presensi",
  "is_active": true
}
```

### Test 2: Kirim Data Presensi

```bash
cd sistem-presensi
php artisan presensi:sync-to-pusat
```

### Test 3: Cek Data di Pusat Data

Buka browser:
```
https://abc123xyz-8000.devtunnels.ms/admin/presensi
```

---

## 🔄 Workflow Harian

### Di Pusat Data (Laptop Server):

```bash
# Pagi hari
cd pusat-data
php artisan serve
devtunnel host --port 8000 --protocol https
```

Biarkan terminal tetap buka!

### Di Sistem Presensi (Laptop Client):

```bash
# Setiap laptop
cd sistem-presensi
php artisan serve --port=8001

# Otomatis sync setiap presensi masuk
# Atau manual sync:
php artisan presensi:sync-to-pusat
```

---

## 🐛 Troubleshooting

### Error: "Unauthenticated"
**Solusi:**
- Pastikan token benar di `.env`
- Generate token baru jika expired
- Cek API client aktif: `ApiClient::where('slug', 'sistem-presensi')->first()->is_active`

### Error: "Connection refused"
**Solusi:**
- Pastikan Pusat Data sudah running
- Cek DevTunnels masih aktif
- Test ping: `curl https://your-tunnel-url/api/me`

### Error: "CORS"
**Solusi:**
- Update `SANCTUM_STATEFUL_DOMAINS` di pusat-data
- Clear config: `php artisan config:clear`

### DevTunnels URL berubah
**Solusi:**
- Update URL di `sistem-presensi/.env`
- Clear config: `php artisan config:clear`

---

## 📊 Monitoring

### Cek Status Sinkronisasi:

```bash
# Di sistem-presensi
php artisan tinker
```

```php
$client = app(\App\Services\PusatDataClient::class);
$status = $client->cekStatusSinkronisasi();
print_r($status);
```

Output:
```php
[
    'total_presensi' => 150,
    'presensi_hari_ini' => 25,
    'sistem_aktif' => 3,
    'last_sync' => '2026-06-22 10:30:15'
]
```

---

## 🔒 Keamanan

1. **Jangan share token** di public
2. **Gunakan HTTPS** (DevTunnels sudah HTTPS)
3. **Ganti token** secara berkala
4. **Backup database** pusat-data secara regular
5. **Monitor log** untuk aktivitas mencurigakan

---

## 📝 Catatan Penting

- **DevTunnels**: Untuk development/testing saja, BUKAN production
- **Token**: Satu laptop = satu token (atau bisa sharing token antar laptop)
- **Database**: Setiap laptop punya database lokal sendiri
- **Pusat Data**: Mengumpulkan data dari semua laptop
- **Network**: Tidak perlu WiFi yang sama, asal ada internet

---

## ✅ Checklist Setup

**Pusat Data:**
- [ ] Laravel installed
- [ ] Database migrated
- [ ] Server running (php artisan serve)
- [ ] DevTunnels running
- [ ] API token generated
- [ ] .env updated dengan DevTunnels URL

**Sistem Presensi (Setiap Laptop):**
- [ ] Laravel installed
- [ ] Database migrated
- [ ] .env configured (URL & Token)
- [ ] Test koneksi berhasil
- [ ] Sync test berhasil

---

## 📞 Support

Jika ada masalah, cek:
1. Log Laravel: `storage/logs/laravel.log`
2. Status DevTunnels: terminal harus tetap running
3. Koneksi internet: semua laptop harus online

---

**Selamat mengintegrasikan sistem! 🚀**
