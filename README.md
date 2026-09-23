# Web CBT Udayana Physics Championship (UPC)

Sistem Computer-Based Testing (CBT) dan platform pendaftaran kompetisi terintegrasi yang dibangun menggunakan **Laravel**. Sistem ini dirancang untuk menangani pendaftaran peserta, pembayaran otomatis (via Midtrans), manajemen perlombaan (penyisihan, semifinal, final), penjualan *merchandise*, hingga pelaksanaan ujian secara *real-time*.

## 🚀 Fitur Utama

* **Sistem Ujian Terpadu (CBT):** Manajemen soal, auto-save jawaban, dan kalkulasi skor otomatis berdasarkan bobot dan aturan nilai (benar, salah, kosong).
* **Manajemen Babak & Gelombang (Waves):** Mendukung pengaturan jadwal ujian spesifik untuk babak Penyisihan, Semifinal, dan Final.
* **Payment Gateway Terintegrasi:** Pembayaran tiket lomba dan *merchandise* menggunakan Midtrans.
* **Dashboard Multi-Role:** Akses khusus untuk Admin (manajemen soal, verifikasi, pengumuman) dan Peserta (ruang tunggu ujian, pustaka materi, transaksi).
* **Manajemen Media & Berkas:** Terintegrasi dengan Spatie Media Library untuk pengelolaan gambar soal, banner lomba, dan foto *merchandise*.

---

## 🛠️ Persyaratan Sistem (Prerequisites)

Sebelum melakukan instalasi, pastikan sistem Anda memiliki lingkungan berikut:

* **PHP** >= 8.2
* **Composer** (Package Manager PHP)
* **Node.js** & **NPM** (Untuk kompilasi aset Frontend via Vite)
* **Database** (MySQL / PostgreSQL / SQLite)

---

## ⚙️ Panduan Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi di lingkungan pengembangan lokal (*local development*):

**1. Ekstrak atau Clone Repositori**
Buka terminal dan arahkan ke direktori proyek.

**2. Instalasi Dependensi PHP**

```bash
composer install

```

**3. Instalasi Dependensi Node.js**

```bash
npm install

```

**4. Konfigurasi Environment**
Salin file `.env.example` menjadi `.env`.

```bash
cp .env.example .env

```

Buka file `.env` dan sesuaikan kredensial database Anda. Jika Anda ingin menggunakan SQLite (sesuai bawaan proyek `upc_cbt_db`), konfigurasikan seperti berikut:

```env
DB_CONNECTION=sqlite
# Hapus atau comment baris DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

```

**5. Generate Application Key**

```bash
php artisan key:generate

```

**6. Migrasi Database & Seeder**
Jalankan perintah ini untuk membangun struktur tabel dan memasukkan data awal (seperti akun Admin *default*).

```bash
php artisan migrate --seed

```

**7. Tautkan Storage (Media Library)**
Karena aplikasi menggunakan Spatie Media Library, Anda wajib menautkan folder *storage* agar gambar dapat diakses secara publik.

```bash
php artisan storage:link

```

**8. Build Aset Frontend (Tailwind & Vite)**
Untuk keperluan *development*:

```bash
npm run dev

```

Atau untuk *production*:

```bash
npm run build

```

**9. Jalankan Aplikasi**
Buka tab terminal baru dan jalankan server internal Laravel.

```bash
php artisan serve

```

Aplikasi kini dapat diakses melalui `http://localhost:8000`.

---

## 💳 Konfigurasi Midtrans (Payment Gateway)

Sistem ini menggunakan Midtrans untuk *checkout* pendaftaran dan *merchandise*. Tambahkan kunci API Midtrans Anda ke dalam file `.env`:

```env
MIDTRANS_SERVER_KEY=your_server_key_here
MIDTRANS_CLIENT_KEY=your_client_key_here
MIDTRANS_IS_PRODUCTION=false

```

---

## 🧪 Pengujian (Testing)

Proyek ini dilengkapi dengan *test suite* menggunakan Pest/PHPUnit (berada di folder `/tests`). Untuk menjalankan seluruh pengujian otomatis:

```bash
php artisan test

```
