# S3VT Group Inventory Management System

Inventory system for S3VT Group (s3vtgroup.com.kh), with sync API so the existing PHP site can consume product catalog and stock status.

## Tech stack

- **Framework:** Laravel 12
- **Database:** MySQL 8
- **Auth:** Laravel Sanctum (API tokens) + Session (Blade admin)
- **Frontend:** Blade templates (server-rendered)

## Setup

### 1. Install dependencies

```bash
composer install
```

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set:

- `DB_CONNECTION=mysql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Optional: `SYNC_API_KEY` – if set, sync endpoints require `X-API-Key` header

### 3. Database

```bash
php artisan migrate
php artisan db:seed
```

Default admin: **admin@s3vtgroup.com.kh** / **password**

### 4. Run

```bash
php artisan serve
```

- **Admin UI:** http://localhost:8000/admin/login
- **API base:** http://localhost:8000/api

---

## API overview

### Auth (API tokens)

- **POST /api/auth/login** – Body: `{ "email", "password" }` → returns `{ "token", "user" }`
- **GET /api/auth/me** – Requires `Authorization: Bearer <token>`

### Categories

- **GET /api/categories** – List (query: `sort`)
- **GET /api/categories/{id}**
- **POST /api/categories** – Create (auth: editor, admin)
- **PUT /api/categories/{id}** – Update
- **DELETE /api/categories/{id}** – Delete

### Products

- **GET /api/products** – List (query: `category_id`, `search`, `sort`)
- **GET /api/products/{id}**
- **POST /api/products** – Create
- **PUT /api/products/{id}** – Update
- **DELETE /api/products/{id}** – Delete

### Stock movements

- **POST /api/stock-movements** – Record movement (product_id, type, quantity, reference?, notes?)
- **GET /api/stock-movements** – List (query: product_id, from_date, to_date, limit)
- **GET /api/stock-movements/product/{id}** – History for one product

### Reports

- **GET /api/reports/out-of-stock**
- **GET /api/reports/low-stock?threshold=N**
- **GET /api/reports/movement-summary** – Query: from_date, to_date, product_id

### Sync API (for PHP site)

If `SYNC_API_KEY` is set, send header: `X-API-Key: <key>`.

- **GET /api/sync/products** – Query: category, limit, offset
- **GET /api/sync/products/{id}**
- **GET /api/sync/products/slug/{slug}**
- **GET /api/sync/categories**

---

## Project structure

- `app/Models/` – Category, Product, Stock, StockMovement, User
- `app/Http/Controllers/Api/` – REST API controllers
- `app/Http/Controllers/Admin/` – Blade admin controllers
- `database/migrations/` – Schema
- `resources/views/admin/` – Blade admin templates
- `routes/api.php` – API routes
- `routes/web.php` – Web (admin) routes
