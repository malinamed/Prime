# Prime Investor Dashboard

Dashboard financiar personal construit cu **Laravel 12**, **Vue.js 3 (Inertia.js)**, **PostgreSQL**, **Redis** si **Docker**.

Datele din dashboard sunt servite exclusiv din Redis — fara query-uri la baza de date si fara apeluri API directe la incarcare.

---

## Ce face aplicatia

Un job de fundal (`FetchAllMarketData`) ruleaza la fiecare 15 minute, apeleaza 3 API-uri externe si salveaza rezultatul agregat intr-un singur key Redis (`primeinvestor_data`). Dashboard-ul citeste doar acel key.

**Surse de date:**
- **Frankfurter API** — cursuri valutare EUR, USD, CHF fata de RON
- **CoinGecko API** — preturile Bitcoin si Ethereum
- **Alpha Vantage API** — indicii bursieri S&P 500 si Nasdaq-100 (SPY / QQQ)

Fiecare operatie de sync este logata in tabelul `market_data_logs` din PostgreSQL (audit trail, nu este citit de dashboard).

---

## Stack

| | |
|---|---|
| Backend | Laravel 12, PHP 8.3 |
| Frontend | Vue.js 3, Inertia.js, Vite 6 |
| Database | PostgreSQL 17 |
| Cache / Queue | Redis 7 |
| Containere | Docker Compose |

---

## Pornire (primul run)

> Necesita doar **Docker Desktop** pornit. Nu e nevoie de PHP sau Node instalate local.

```bash
# 1. Construieste si porneste containerele
#    (composer install ruleaza automat la primul start)
docker compose up -d --build

# 2. Instaleaza dependintele Node si construieste frontend-ul
docker compose exec app npm install && npm run build

# 3. Configurare Laravel
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate

# 4. Populeaza cache-ul Redis
docker compose exec app php artisan market:sync

# 5. Deschide http://localhost
```

> **Nota:** La primul `docker compose up --build`, containerul va rula `composer install` automat inainte de a porni serverul. Poate dura 1-2 minute in plus la primul start.

---

## Alpha Vantage API Key

Inregistreaza-te gratuit la [alphavantage.co](https://www.alphavantage.co/support/#api-key), apoi adauga cheia in `.env`:

```
ALPHA_VANTAGE_API_KEY=cheia_ta
```

Fara cheie, serviciul foloseste automat date mock realiste si afiseaza un badge "Demo" in UI.

---

## Comenzi utile

```bash
# Sync manual al datelor
docker compose exec app php artisan market:sync

# Verifica ce e in Redis
docker compose exec redis redis-cli GET primeinvestor_data

# Oprire
docker compose down
```
