# Mini E-Wallet

Project ini dibuat sebagai bagian dari technical test untuk posisi **Fullstack Developer** di **PT Tiga Serangkai Inti Corpora**.

Aplikasi yang dibangun merupakan simulasi mini e-wallet yang memungkinkan pengguna melihat saldo, melakukan transfer dana antar pengguna, dan melihat riwayat transaksi.

Selain memenuhi kebutuhan fungsional yang diminta, implementasi juga berfokus pada konsistensi data transaksi, validasi, keamanan proses transfer, serta kemudahan pengembangan di masa depan.

---

## Fitur

### Authentication

* Login
* Logout

### Dashboard

* Menampilkan informasi pengguna
* Menampilkan saldo wallet
* Menampilkan transaksi terbaru

### Transfer Dana

* Transfer saldo antar pengguna
* Validasi nominal transfer
* Validasi saldo mencukupi
* Mencegah transfer ke diri sendiri

### Riwayat Transaksi

* Daftar transaksi masuk dan keluar
* Pagination
* Sorting berdasarkan tanggal transaksi

### API

* Endpoint API untuk autentikasi
* Endpoint API dashboard
* Endpoint API transfer
* Endpoint API histori transaksi

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

## Keputusan Teknis

### Pemisahan Wallet dan User

Saldo disimpan pada tabel `wallets` dan bukan langsung pada tabel `users`.

Pendekatan ini dipilih untuk memisahkan data profil pengguna dengan data finansial sehingga domain keuangan memiliki tanggung jawab yang lebih jelas.

Selain itu, struktur ini lebih mudah dikembangkan jika di masa depan dibutuhkan fitur seperti:

* Multiple Wallet
* Multi Currency
* Jenis Wallet yang berbeda

---

### Konsistensi Data Transaksi

Proses transfer melibatkan perubahan saldo pada dua wallet sekaligus dan pencatatan transaksi.

Untuk memastikan seluruh proses berjalan sebagai satu kesatuan, transfer dibungkus menggunakan database transaction:

```php
DB::transaction(...)
```

Jika salah satu proses gagal, seluruh perubahan akan dibatalkan (rollback).

---

### Pencegahan Race Condition

Transfer menggunakan row-level locking:

```php
lockForUpdate()
```

Pendekatan ini digunakan untuk mencegah kondisi ketika beberapa request transfer mengakses saldo wallet yang sama secara bersamaan sehingga konsistensi saldo tetap terjaga.

---

### API Ready

Meskipun aplikasi web menggunakan Inertia.js, backend tetap menyediakan endpoint API yang terpisah.

Pendekatan ini dipilih agar business logic yang sama dapat digunakan kembali oleh client lain seperti aplikasi mobile Flutter tanpa perlu melakukan perubahan pada proses bisnis yang sudah ada.

---

## Struktur Database

Aplikasi menggunakan tiga entitas utama:

### Users

Menyimpan data akun pengguna.

### Wallets

Menyimpan saldo pengguna.

### Transactions

Menyimpan riwayat transfer antar wallet.

Relasi:

```text
User (1) ── (1) Wallet

Wallet (1) ── (N) Sent Transactions

Wallet (1) ── (N) Received Transactions
```
---

## Endpoint API

### Authentication

```http
POST /api/login
POST /api/logout
GET /api/me
```

### Dashboard

```http
GET /api/dashboard
```

### Transfer

```http
POST /api/transfers
```

### Transaction History

```http
GET /api/transactions
```

---

## Instalasi

### Clone Repository

```bash
git clone <repository-url>
cd <project-name>
```

### Install Dependency

```bash
composer install
npm install
```

### Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan konfigurasi database pada file `.env`.

### Migration dan Seeder

```bash
php artisan migrate:fresh --seed
```

### Menjalankan Aplikasi

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

| Nama   | email              | Saldo Awal |
| ------ | ------------------ | ---------- |
| User A | usera@example.com  | Rp100.000  |
| User B | userb@example.com  | Rp100.000  |
| User C | userc@example.com  | Rp100.000  |

Password default:

```text
password
```

---

## Testing

Menjalankan seluruh test:

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

Beberapa pengembangan yang dapat dilakukan pada sistem ini:

* Aplikasi Mobile Flutter
* Top Up Saldo
* Withdraw Saldo
* Notifikasi
* Multiple Wallet
* Multi Currency
* Dashboard Admin
* Audit Log
