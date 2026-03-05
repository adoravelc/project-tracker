# Project Tracker

Aplikasi **Task & Project Tracker** dengan arsitektur:
- **Backend**: Laravel 12 API + Sanctum
- **Frontend**: Vue 3 + TypeScript (Vite)

## Prasyarat

Pastikan sudah terpasang:
- PHP 8.2+
- Composer
- Node.js 20+
- npm
- PostgreSQL

## Cara Instalasi

### 1) Clone repository

```bash
git clone <repo-url>
cd project-tracker
```

### 2) Setup backend (Laravel)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Lalu atur koneksi database PostgreSQL pada file `.env` backend:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=project_tracker
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

Jalankan migrasi + seeder:

```bash
php artisan migrate:fresh --seed
```

### 3) Setup frontend (Vue)

```bash
cd ../frontend
npm install
```

## Cara Menjalankan Aplikasi

Buka **2 terminal** terpisah.

### Terminal 1: Jalankan backend API

```bash
cd backend
php artisan serve
```

Backend berjalan di: `http://127.0.0.1:8000`

### Terminal 2: Jalankan frontend

```bash
cd frontend
npm run dev
```

Frontend berjalan di: `http://127.0.0.1:5173`

### Akun default hasil seeder

- Email: `admin@energeek.id`
- Password: `admPa$$Energeek`

## Cara Menjalankan Testing

### Testing backend (Laravel / PHPUnit)

```bash
cd backend
php artisan test
```

Alternatif:

```bash
cd backend
composer test
```

### Testing frontend (Vitest)

```bash
cd frontend
npm run test:unit
```

## Build Production (Opsional)

### Frontend

```bash
cd frontend
npm run build
```

### Backend assets (jika diperlukan)

```bash
cd backend
npm run build
```