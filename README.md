# Website Demografi Kelurahan Sukorame

Sistem Informasi Geografis (SIG) berbasis web untuk visualisasi data demografi, profil, dan batas wilayah Kelurahan Sukorame. Proyek ini dikembangkan menggunakan framework Laravel untuk memberikan kemudahan dalam pengelolaan dan analisis data wilayah.

## Fitur Utama

-   **Pemetaan Batas Wilayah:** Visualisasi batas administratif Kelurahan Sukorame secara interaktif.
-   **Profil Desa:** Penyajian informasi umum dan profil lengkap kelurahan.
-   **Visualisasi Data Demografi:** Data kependudukan berdasarkan kategori (jenis kelamin, pekerjaan, pendidikan) dalam bentuk grafik dan tabel interaktif.

## Prasyarat

Pastikan perangkat Anda telah memenuhi persyaratan berikut sebelum melakukan instalasi:

-   PHP >= 8.1
-   Composer versi 2.x
-   Node.js & NPM (opsional, untuk pengelolaan aset frontend)
-   Database: MySQL, MariaDB, atau PostgreSQL

## Instalasi

Ikuti langkah-langkah berikut untuk menjalankan aplikasi di lingkungan lokal:

### 1. Clone Repositori

```bash
git clone https://github.com/aruhaxs/sukorame-demographics-website.git
cd sukorame-demographics-website
```

### 2. Instal Dependensi PHP

```bash
composer install
```

### 3. Salin File Konfigurasi Lingkungan

```bash
# Linux/macOS/Git Bash
cp .env.example .env

# Windows CMD
copy .env.example .env
```

### 4. Generate Kunci Aplikasi

```bash
php artisan key:generate
```

### 5. Konfigurasi Database

Edit file `.env` sesuai pengaturan database lokal Anda:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=user_database_anda
DB_PASSWORD=password_anda
```

Pastikan database `nama_database_anda` telah dibuat di sistem manajemen database Anda.

### 6. Migrasi Database

```bash
php artisan migrate
```

### 7. Menjalankan Server Pengembangan

```bash
php artisan serve
```

Aplikasi dapat diakses melalui [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

Untuk pertanyaan lebih lanjut atau kontribusi, silakan hubungi pengelola proyek melalui halaman [GitHub Issues](https://github.com/aruhaxs/sukorame-demographics-website/issues).
