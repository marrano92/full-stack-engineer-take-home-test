# Asset Management – Laravel 12 + Vue 3 (Inertia) + Postgres

Production‑ready web app to manage company assets with owner history (timeline).  
Stack: **PHP 8.2+ / Laravel 12**, **Vue 3 + Inertia**, **Tailwind CSS**, **Postgres**.  
A production Docker image is provided and exposes **port 3000**.

> The UI follows the provided Figma wireframe (not pixel‑perfect, but very similar).  
> Figma: https://www.figma.com/proto/wPA674xCC2d7zcGh8DlDIE/Full-Stack-Engineer---Take-Home-test?node-id=0-1  
> Starter template used: https://github.com/SkillSherpaSA/full-stack-engineer-take-home-test

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Quick Start (Local)](#quick-start-local)
- [Local Setup with Postgres in Docker](#local-setup-with-postgres-in-docker)
- [Production Build with Docker (port 3000)](#production-build-with-docker-port-3000)
- [Environment Variables](#environment-variables)
- [Seed Data & Test Credentials](#seed-data--test-credentials)
- [Useful Commands](#useful-commands)
- [Data Model & Business Rules](#data-model--business-rules)
- [Project Structure](#project-structure)
- [Testing](#testing)
- [Design Choices & Trade‑offs](#design-choices--trade-offs)
- [Improvements with More Time](#improvements-with-more-time)

---

## Features

- **Authentication** with 2FA OTP system (email-based One-Time Password).
- **Assets list** with pagination, edit/delete, and create new asset.
- **Create / Update asset** with optional current owner.
- **Owner history (timeline)** for each asset (periods `owned_from` → `owned_to`).  
  A new history entry is created whenever the owner changes; the previous open period is closed.
- **Denormalized current owner** fields on `assets` for fast listing.
- **Production Docker image** (Vite build, optimized Composer autoloader, cached config/routes/views) on **port 3000**.

---

## Tech Stack

- **Backend:** PHP 8.2+, Laravel 12
- **Frontend:** Vue 3, Inertia.js, Tailwind CSS
- **Database:** PostgreSQL 14+
- **Container:** Docker (multi‑stage production build)
- **Auth:** 2FA OTP system with email verification

---

## Quick Start (Local)

```bash
# 1) Copy env and configure DB
cp .env.example .env

# 2) Install dependencies
composer install
npm ci

# 3) App key and database
php artisan key:generate
php artisan migrate --seed

# 4) Start email testing service
docker compose up mailhog -d  # Email UI: http://localhost:8025

# 5) Run dev servers
php artisan serve            # http://localhost:8000
npm run dev                  # Vite dev server
```

> If you prefer to run the database in Docker, see the next section.

---

## Local Setup with Postgres in Docker

The template includes a `docker-compose.yml` service for **Postgres**.

```bash
# Start only the DB
docker compose up db -d
```

Then make sure your `.env` points to that DB service and continue from step (2) in **Quick Start**.

---

## Production Build with Docker (port 3000)

The Docker configuration (located in `infra/prod/docker/`) builds the frontend and backend in a multi‑stage image and
exposes **port 3000**.

```bash
# Build and run the production stack
docker compose up --build
# App will be available at:
# http://localhost:3000/
```

**Notes**

- The image runs `composer install --no-dev --optimize-autoloader`, `npm ci && npm run build`, and caches
  config/routes/views.
- Ensure `APP_URL=http://localhost:3000` in the production `.env`.
- Ensure the dashboard of MailHog works on http://localhost:8025
- In CI/CD you should run `php artisan migrate --force` during deploy (if not baked into the entrypoint).

**Known Issues:** During Docker builds, you may encounter ESBuild version mismatches. If this occurs, clear
`node_modules` and reinstall dependencies. See `DEPLOYMENT_NOTES.md` for detailed troubleshooting.

> **Security:** Do not commit real secrets. For the test, reviewers will swap SMTP settings as per your instructions.

---

## Seed Data & Test Credentials

Local dev only:

- **User**: `test@example.com`
- **Password**: `password`

Seeders populate:

- a set of **Owners**;
- a set of **Assets** (some without owner, some with history built through the action).

```bash
php artisan migrate:fresh --seed
```

---

## Useful Commands

```bash
# Dev
php artisan serve
npm run dev

# Frontend build
npm run build

# Optimize & caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize:clear

# Database
php artisan migrate
php artisan migrate:fresh --seed

# REPL
php artisan tinker

# If you want to start from a Clean state
docker-compose down --volumes
```

---

## Data Model & Business Rules

### Tables

#### `owners`

- `id` (PK)
- `first_name` (string, required)
- `last_name` (string, required)
- `timestamps`, `deleted_at` (soft deletes)
- Indexes: `last_name`, `(first_name, last_name)`
- **Uniqueness constraint:** `UNIQUE (first_name, last_name)`

#### `assets`

- `id` (PK)
- `reference` (string, required)
- `serial_number` (string, **unique**, required)
- `description` (text, nullable)
- `current_owner_id` (FK → `owners.id`, **nullable**, `NULL ON DELETE`)
- `current_owned_from` (`DATETIME`, **nullable**)
- `timestamps`
- Indexes: `current_owner_id`, `reference`

#### `asset_owner_assignments` (history)

- `id` (PK)
- `asset_id` (FK → `assets.id`, `CASCADE ON DELETE`)
- `owner_id` (FK → `owners.id`, `CASCADE ON DELETE`)
- `owned_from` (`DATETIME`, required)
- `owned_to` (`DATETIME`, nullable)
- `timestamps`
- Indexes: `asset_id`, `owner_id`
- **Partial unique** (max one open assignment per asset):
  ```sql
  CREATE UNIQUE INDEX asset_open_assignment_unique
    ON asset_owner_assignments (asset_id)
    WHERE owned_to IS NULL;
  ```
- **Optional CHECK** (data sanity):
  ```sql
  ALTER TABLE asset_owner_assignments
    ADD CONSTRAINT owned_to_after_from
    CHECK (owned_to IS NULL OR owned_to >= owned_from);
  ```
- **Optional idempotency UNIQUE**:
  ```sql
  CREATE UNIQUE INDEX uniq_asset_owner_from
    ON asset_owner_assignments (asset_id, owner_id, owned_from);
  ```

### Business Rules

- The **current owner** is the one from the single “open” assignment (`owned_to IS NULL`).  
  For performance, the current owner is **denormalized** on `assets` (`current_owner_id`, `current_owned_from`).
- **Owner change** is handled atomically in a dedicated Action:
    1. If the new owner equals the current owner → **no‑op**;
    2. Otherwise **close** the open assignment (`owned_to = effectiveAt`);
    3. If the new owner is `NULL` → clear current owner fields on `assets`;
    4. If not `NULL` → **create** a new open assignment (`owned_from = effectiveAt`) and update the current owner fields
       on `assets`;
    5. All inside a **DB transaction** with `SELECT ... FOR UPDATE` on the asset row.

---

## Project Structure

The application follows Laravel's conventional structure with some additional organization:

**Backend (Laravel)**

- `app/Actions/` - Business logic actions for asset and owner operations
- `app/Http/Controllers/` - API controllers for assets and owners
- `app/Http/Requests/` - Form validation requests
- `app/Models/` - Eloquent models (Asset, Owner, AssetOwnerAssignment)

**Database**

- `database/migrations/` - Schema migrations for all tables
- `database/seeders/` - Data seeders for development
- `database/factories/` - Model factories for testing

**Frontend (Vue + Inertia)**

- `resources/js/Pages/` - Vue pages organized by feature (Assets, Auth)
- `resources/js/layouts/` - Shared layout components
- `resources/js/types/` - TypeScript type definitions

**Infrastructure**

- `infra/prod/docker/` - Production Docker configuration
- `docker-compose.yml` - Development and production orchestration

---

## Testing

A minimal suite can include:

- Action: owner change (no change, A→B, remove owner);
- Create asset with initial owner (history written);
- Soft delete asset keeps history intact.

```bash
php artisan test
```

---

## Design Choices & Trade‑offs

- **Normalized owners** with **soft deletes** to avoid breaking history and to allow dedup strategies.
- **History table** (`asset_owner_assignments`) with **partial unique** “only one open per asset”.  
  Integrity is enforced at the **database level** (not only in code).
- **Denormalized current owner** on `assets` to speed up the listing view.
- **Action** (`AssignOwnerToAsset`) as the single source of truth for owner changes: transactional, testable, reusable.
- **Postgres** features used: partial unique indexes and CHECK constraints to keep data consistent.

---

## Improvements with More Time

- **UI/UX**: owner autocomplete (server‑filtered), filters/sorting on the assets list, CSV export, toasts/loading
  states, i18n.
- **Security**: roles/policies, audit log of changes.
- **Enhanced Security**: role-based access control, audit logging of sensitive operations.
- **Testing**: broader coverage (feature & E2E), concurrency tests.
- **Ops**: healthchecks, CI pipeline (build, tests, linters), migrations on deploy.
