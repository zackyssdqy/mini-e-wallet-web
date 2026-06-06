# Mini E-Wallet

Laravel 13 mini e-wallet built with Inertia.js, React, Tailwind CSS, Sanctum, and Spatie Permission. The app uses MySQL and keeps transfer processing synchronous with database transactions and row-level locking for wallet balance consistency.

## Features

- Authentication with Laravel Breeze
- Wallet-based balance tracking
- Safe money transfer with validation, DB transaction, and `lockForUpdate()`
- Transaction history with pagination and sorting
- Dashboard with balance summary and recent activity
- Web UI and JSON API with consistent responses

## Tech Stack

- Laravel 13
- MySQL
- Inertia.js
- React (JavaScript)
- Tailwind CSS
- Sanctum
- Spatie Permission

## Local Setup

1. Install dependencies:

```bash
composer install
npm install
```

2. Configure `.env` for MySQL.

3. Run migrations and seed data:

```bash
php artisan migrate --seed
```

4. Start the app:

```bash
php artisan serve
npm run dev
```

## Default Seed Users

- User A
- User B
- User C

All seed users start with a wallet balance of `100000` and password `password`.

## Testing

```bash
php artisan test
```

## Build

```bash
npm run build
```

## Notes

- Queue is intentionally not used for the current requirements.
- API endpoints are protected with Sanctum.
- Transfer validation is enforced in both the request layer and the service layer.
- Balance lives on `wallets`, not `users`.
