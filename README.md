# Mini E-Wallet

Mini E-Wallet adalah aplikasi sederhana yang memungkinkan pengguna melihat saldo, melakukan transfer dana ke pengguna lain, dan melihat riwayat transaksi.

Aplikasi ini dibangun menggunakan Laravel, Inertia.js, React, dan MySQL dengan fokus pada konsistensi data, keamanan transaksi, dan kemudahan pengembangan di masa depan.

## Fitur

* Login dan Logout
* Dashboard saldo pengguna
* Transfer dana antar pengguna
* Riwayat transaksi
* Pagination dan sorting transaksi
* Validasi form dan error handling
* REST API untuk kebutuhan integrasi mobile di masa depan

---

## Teknologi yang Digunakan

### Backend

* Laravel 13
* MySQL
* Laravel Sanctum
* Spatie Permission

### Frontend

* React
* Inertia.js
* Tailwind CSS

---

## Desain Database

Aplikasi menggunakan tiga entitas utama:

### Users

Menyimpan informasi akun pengguna.

### Wallets

Menyimpan saldo pengguna.

### Transactions

Menyimpan riwayat transfer antar wallet.

Relasi:

```text
User (1) ── (1) Wallet

Wallet (1) ── (N) Transaksi Keluar

Wallet (1) ── (N) Transaksi Masuk
```

---

## Pertimbangan Desain

### Pemisahan Wallet dan User

Saldo disimpan pada tabel `wallets`, bukan langsung pada tabel `users`.

Pendekatan ini dipilih untuk memisahkan data profil pengguna dengan data finansial sehingga lebih mudah dikembangkan di masa depan, misalnya untuk:

* Multiple wallet
* Multi-currency
* Jenis wallet yang berbeda

### Konsistensi Data Transaksi

Proses transfer dilakukan menggunakan database transaction:

```php
DB::transaction(...)
```

Dengan pendekatan ini, seluruh proses transfer dianggap sebagai satu kesatuan.

Jika salah satu proses gagal, seluruh perubahan akan dibatalkan (rollback).

### Pencegahan Race Condition

Transfer menggunakan row-level locking:

```php
lockForUpdate()
```

Tujuannya untuk mencegah terjadinya race condition ketika beberapa request transfer dilakukan secara bersamaan terhadap wallet yang sama.

### Siap untuk Integrasi Mobile

Aplikasi menyediakan endpoint API yang dapat digunakan kembali oleh aplikasi mobile seperti Flutter tanpa perlu mengubah business logic yang sudah ada.

---

## Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd <project-name>
```

### 2. Install Dependency

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

Salin file environment:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Kemudian sesuaikan konfigurasi database pada file `.env`.

### 4. Jalankan Migration dan Seeder

```bash
php artisan migrate:fresh --seed
```

### 5. Jalankan Aplikasi

Backend:

```bash
php artisan serve
```

Frontend:

```bash
npm run dev
```

---

## Akun Seeder

| Nama   | Saldo Awal |
| ------ | ---------- |
| User A | Rp100.000  |
| User B | Rp100.000  |
| User C | Rp100.000  |

Password default:

```text
password
```

---

## Endpoint API

### Autentikasi

```http
POST /api/login
POST /api/logout
GET /api/me
```

### Dashboard

```http
GET /api/dashboard
```

### Transfer Dana

```http
POST /api/transfers
```

### Riwayat Transaksi

```http
GET /api/transactions
```

---

## Menjalankan Pengujian

```bash
php artisan test
```

---

## Build Production

```bash
npm run build
```

---

## Pengembangan Selanjutnya

Beberapa pengembangan yang dapat dilakukan di masa depan:

* Aplikasi Mobile (Flutter)
* Top Up Saldo
* Withdraw Saldo
* Notifikasi
* Multiple Wallet
* Multi-Currency
