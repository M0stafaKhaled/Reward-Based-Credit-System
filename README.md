# Reward-Based Laravel API

## Overview
This project is a scalable, modern Laravel API for a reward-based system. It supports user credit packages, product redemption, admin management, and advanced search using Laravel Scout and Meilisearch.

---

## Features
- **User Credit Packages:** Purchase, log, and manage credit packages.
- **Product Catalog:** List, search, and redeem products using points.
- **Admin Panel:** Manage products, offers, and credit packages.
- **Offer Pool:** Special offers for products.
- **Full-Text Search:** Fast, typo-tolerant search with Laravel Scout + Meilisearch.
- **API Resources:** Consistent, test-friendly JSON responses.
- **Test Coverage:** Feature and unit tests for all major endpoints.

---

## Technology Stack
- **Laravel 10**
- **MySQL 8** (default DB)
- **Redis** (cache/queue)
- **Meilisearch** (search engine)
- **Laravel Scout** (search integration)
- **Docker** (for local development)

---

## Setup Instructions

### 1. Docker Setup (Recommended)

#### Prerequisites
- [Docker](https://www.docker.com/get-started) installed

#### Steps
```bash
git clone <your-repo-url>
cd Reward-Based
cp .env.docker .env
# (Edit .env if needed)
docker-compose up --build -d
```

#### Services & Ports
- **App (PHP-FPM):** internal
- **Nginx:** [http://localhost:80](http://localhost:80)
- **MySQL:** `localhost:3306` (user: laravel, pass: secret)
- **Redis:** `localhost:6379`
- **Meilisearch:** [http://localhost:7700](http://localhost:7700)
- **phpMyAdmin:** [http://localhost:8081](http://localhost:8081)

#### First-Time Setup
```bash
docker-compose exec app composer install
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan scout:import "App\\Models\\Product"
```

---

### 2. Manual Setup (Without Docker)

#### Prerequisites
- PHP 8.1+
- Composer
- MySQL 8+
- Redis
- Meilisearch (download from https://www.meilisearch.com/)

#### Steps
```bash
git clone <your-repo-url>
cd Reward-Based
cp .env.example .env
# (Edit .env for DB, Redis, Meilisearch)
composer install
php artisan key:generate
php artisan migrate --seed
php artisan scout:import "App\\Models\\Product"
php artisan serve
```

#### Default Ports
- **App:** http://localhost:8000
- **MySQL:** 3306
- **Redis:** 6379
- **Meilisearch:** 7700

---

## Database
- **MySQL** is used for all persistent data (users, products, purchases, logs, etc).
- Migrations and seeders are provided.

---

## Search: Laravel Scout + Meilisearch
- **Scout** syncs Eloquent models to Meilisearch automatically.
- **Meilisearch** provides fast, typo-tolerant, and relevant search results.
- **Benefits:**
  - Millisecond search, even with large datasets
  - Typo tolerance, relevance ranking, and advanced filtering
  - Offloads search from MySQL, improving overall performance
- **How to use:**
  - Use `/api/products/search?query=...` for full-text search
  - Use `/api/products/suggestions?query=...` for instant suggestions

---

## Functionality
- **User Endpoints:**
  - Purchase credit packages
  - View credit log
  - Redeem products with points
  - Search and list products
- **Admin Endpoints:**
  - CRUD for products, offers, credit packages
  - Secure admin authentication
- **Offer Pool:**
  - Special offers for products
- **API Resources:**
  - Consistent JSON structure for all endpoints

---

## Testing & Coverage
- **PHPUnit** feature and unit tests for all major endpoints
- Run tests with:
```bash
# Docker
docker-compose exec app php artisan test
# Or locally
php artisan test
```
- Tests cover:
  - Credit package purchase & log
  - Product search, suggestions, trending
  - Redemption flow
  - Admin CRUD
  - API response structure

---

## Performance
- **Scout + Meilisearch**: Offloads search from MySQL, provides instant, typo-tolerant, and relevant results.
- **Redis**: Used for cache and queue, improving response time and scalability.
- **Docker**: Ensures consistent, reproducible local development.

---

## Contributing
Pull requests are welcome! Please write tests for new features and follow PSR coding standards.

---

## License
MIT
