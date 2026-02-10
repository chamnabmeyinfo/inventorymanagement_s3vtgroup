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
- **Dashboard:** http://localhost:8000/admin (after login)
- **API base:** http://localhost:8000/api

---

## Smart assistant features

The dashboard helps owners stay **hands-free and on track**:

- **Smart alerts** – Banner when products are out of stock or below reorder point
- **Quick stats** – Total products, in stock, out of stock, low stock, 7-day movement summary
- **Products needing attention** – Table with one-click “Record” to add stock
- **Recent activity** – Last 10 stock movements with relative time
- **Quick actions** – Record movement, add product, view reports

**Reorder point:** Set per product when editing. When stock ≤ reorder point, the product appears in alerts. If not set, uses `INVENTORY_LOW_STOCK_THRESHOLD` (default 5).

**Suppliers:** Track where stock comes from. Add suppliers (name, contact, phone, email). Link stock-in movements to suppliers. Set preferred supplier per product for reorder suggestions. Dashboard shows "Stock from suppliers" (received by supplier).

**Email alerts:** Set `INVENTORY_ALERT_EMAIL` in `.env` and configure `MAIL_*`. Run `php artisan inventory:check-low-stock` manually or via cron: `* * * * * php /path/to/artisan schedule:run` (runs daily at 08:00).

**Gmail / Google Workspace:** Use `MAIL_MAILER=smtp`, `MAIL_HOST=smtp.gmail.com`, `MAIL_PORT=587`, `MAIL_ENCRYPTION=tls`, your full email, and an [App Password](https://support.google.com/accounts/answer/185833) (required if 2FA is on). Or set these in Admin → Settings.

**Telegram alerts (free):** To receive low-stock alerts on Telegram, add to `.env`:
- `TELEGRAM_BOT_TOKEN` – Create a bot via [@BotFather](https://t.me/BotFather), send `/newbot`, copy the token.
- `TELEGRAM_CHAT_ID` – Message your bot (e.g. `/start`), then open `https://api.telegram.org/bot<TOKEN>/getUpdates` in a browser and find `"chat":{"id":123456789}`.

You can use Telegram alone, email alone, or both.

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
