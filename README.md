# Prime Investor — Personal Wealth and Market Watch Dashboard

A Laravel 12 + Vue.js 3 (Inertia.js) financial dashboard application.
All dashboard data is served from Redis — no direct API calls or database queries occur on page load.

---

## Architecture Overview

```
Browser  -->  DashboardController  -->  Redis::get('primeinvestor_data')
                                              |
                              [populated by background job]
                                              |
                         FetchAllMarketData (Queued Job, every 15 min)
                          |           |              |
                   Frankfurter    CoinGecko    AlphaVantage
                   (Currency)     (Crypto)      (Indices)
                          |           |              |
                     MarketDataLog (PostgreSQL audit log)
```

## Technical Stack

| Layer        | Technology                  |
|--------------|-----------------------------|
| Framework    | Laravel 12 (PHP 8.3)        |
| Frontend     | Vue.js 3 + Inertia.js       |
| Database     | PostgreSQL 17               |
| Cache/Queue  | Redis 7                     |
| Build Tool   | Vite 6                      |
| Containers   | Docker + Laravel Sail       |

---

## Prerequisites

- Docker Desktop running on Windows
- No local PHP or Node required (everything runs inside Docker)

---

## Initial Setup

### Step 1: Build and start containers

```bash
docker compose up -d --build
```

This starts three services:
- `app` — Laravel PHP 8.3 application server (port 80)
- `scheduler` — runs `php artisan schedule:work`
- `queue` — runs `php artisan queue:work redis`
- `pgsql` — PostgreSQL 17 database (port 5432)
- `redis` — Redis 7 cache and queue (port 6379)

### Step 2: Install PHP dependencies

```bash
docker compose exec app composer install
```

### Step 3: Generate application key

```bash
docker compose exec app php artisan key:generate
```

### Step 4: Install Node dependencies and build frontend assets

```bash
docker compose exec app npm install
docker compose exec app npm run build
```

### Step 5: Run database migrations

```bash
docker compose exec app php artisan migrate
```

### Step 6: Populate the Redis cache (first-run sync)

```bash
docker compose exec app php artisan market:sync
```

This fetches data from all three external APIs and writes the combined result
to the Redis key `primeinvestor_data`. The dashboard will display data immediately
after this command completes.

### Step 7: Open the dashboard

Visit: http://localhost

---

## Alpha Vantage API Key (Optional)

The `AlphaVantageService` includes a realistic mock fallback for S&P 500 and Nasdaq-100
data. If no API key is configured, mock data is used automatically and a "Demo" badge
is shown in the UI.

To use real data:
1. Register for a free key at https://www.alphavantage.co/support/#api-key
2. Add it to `.env`:
   ```
   ALPHA_VANTAGE_API_KEY=your_key_here
   ```
3. Re-run `php artisan market:sync` inside the container.

---

## Background Scheduler

The `scheduler` Docker service runs `php artisan schedule:work` continuously.
`FetchAllMarketData` is dispatched every 15 minutes automatically.

Each fetch operation writes one record per source to the `market_data_logs` table
in PostgreSQL. This provides a historical audit log of all sync operations.

---

## Redis Cache Key

The dashboard reads from a single Redis key:

```
primeinvestor_data
```

To inspect the cached data manually:

```bash
docker compose exec redis redis-cli GET primeinvestor_data
```

---

## File Structure

```
app/
  Console/Commands/SyncMarketData.php     -- artisan market:sync
  Http/Controllers/DashboardController.php
  Http/Middleware/HandleInertiaRequests.php
  Jobs/FetchAllMarketData.php
  Models/MarketDataLog.php
  Providers/AppServiceProvider.php
  Services/
    AlphaVantageService.php
    CoinGeckoService.php
    FrankfurterService.php

config/
  app.php  cache.php  database.php  queue.php  redis.php  services.php

database/
  migrations/
    2024_01_01_000001_create_market_data_logs_table.php
    2024_01_01_000002_create_sessions_table.php
    2024_01_01_000003_create_cache_table.php
    2024_01_01_000004_create_jobs_table.php

docker/8.3/
  Dockerfile  php.ini  start-container  supervisord.conf

resources/
  css/app.css
  js/
    Pages/Dashboard.vue
    app.js  bootstrap.js
  views/app.blade.php

routes/
  console.php   -- scheduler registration
  web.php       -- GET / -> DashboardController@index
```

---

## Development (with Vite hot reload)

```bash
docker compose exec app npm run dev
```

Then open http://localhost in your browser. Vite will serve assets with HMR.

---

## Stopping the application

```bash
docker compose down
```

To also remove volumes (wipes PostgreSQL and Redis data):

```bash
docker compose down -v
```
