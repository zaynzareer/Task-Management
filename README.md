# TaskFlow — Task Management Application

A single-user task management SPA built with **Laravel 12**, **Blade**, **TailwindCSS**, and **Alpine.js**. Users can register, log in, and manage their personal tasks through a dashboard that communicates with a JSON API backed by Laravel's web session guard.

This project was developed as part of a technical assessment for an internship application.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Environment Variables](#environment-variables)
- [Setup](#setup)
- [Running the Application](#running-the-application)
- [API Reference](#api-reference)
- [Design Decisions & Assumptions](#design-decisions--assumptions)

---

## Features

| Area | Detail |
|---|---|
| **Authentication** | Registration, login, logout, password reset (Laravel Breeze) |
| **Task CRUD** | Create, read, update, and delete tasks via a fully reactive dashboard |
| **Status workflow** | Strict forward-only progression: `pending → in_progress → completed` — backward or skip transitions are rejected at both API and UI level |
| **Soft deletes** | Tasks are soft-deleted; a dedicated **Deleted** filter in the sidebar lets users view removed tasks |
| **Pagination** | Server-side, 6 tasks per page, works with filters |
| **Priority levels** | Low (0) / Medium (1) / High (2) |
| **Authorization** | Users can only access their own tasks (policies are enforced on every endpoint) |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Auth scaffold | Laravel Breeze |
| Frontend | Blade, TailwindCSS v3, Alpine.js |
| Build tool | Vite |
| HTTP client (JS) | Axios (via `window.axios` from `bootstrap.js`) |
| Database | MySQL |
| API auth | Laravel web session guard (cookie + CSRF token) |

---

## Requirements

- PHP **≥ 8.2** with extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`
- Composer **≥ 2**
- Node.js **≥ 18** and npm
- MySQL **≥ 8** (or MariaDB ≥ 10.3)

---

## Environment Variables

Copy `.env.example` to `.env` before starting. All variables are documented below with their purpose and default values.

### Application

| Variable | Default | Description |
|---|---|---|
| `APP_NAME` | `Task Management` | Name shown in browser title and emails |
| `APP_ENV` | `local` | Environment (`local`, `production`, `testing`) |
| `APP_KEY` | *(generated)* | 32-byte encryption key — **never share or commit this** |
| `APP_DEBUG` | `true` | Show detailed error pages — set `false` in production |
| `APP_URL` | `http://localhost` | Full base URL of the application |
| `APP_LOCALE` | `en` | Default application locale |
| `APP_FALLBACK_LOCALE` | `en` | Fallback locale when a translation is missing |
| `APP_FAKER_LOCALE` | `en_US` | Locale used by Faker in factories and seeders |

### Database

| Variable | Default | Description |
|---|---|---|
| `DB_CONNECTION` | `mysql` | Driver (`mysql`, `pgsql`, `sqlite`) |
| `DB_HOST` | `127.0.0.1` | Database host |
| `DB_PORT` | `3306` | Database port |
| `DB_DATABASE` | `task-management` | Database name — **create this before migrating** |
| `DB_USERNAME` | `root` | Database user |
| `DB_PASSWORD` | *(empty)* | Database password |

### Session

| Variable | Default | Description |
|---|---|---|
| `SESSION_DRIVER` | `database` | Where sessions are stored — `database` requires the sessions table (included in migrations) |
| `SESSION_LIFETIME` | `120` | Minutes before an idle session expires |
| `SESSION_ENCRYPT` | `false` | Encrypt session payload at rest |
| `SESSION_DOMAIN` | `null` | Set to your domain (e.g. `example.com`) in production |

### Logging

| Variable | Default | Description |
|---|---|---|
| `LOG_CHANNEL` | `stack` | Log channel (`stack`, `single`, `daily`) |
| `LOG_LEVEL` | `debug` | Minimum severity to record |

### Vite / Frontend

| Variable | Default | Description |
|---|---|---|
| `VITE_APP_NAME` | `${APP_NAME}` | Exposed to JavaScript via `import.meta.env.VITE_APP_NAME` |

---


## Setup

### Step by step instruction

**1. Clone the repository**

```bash
git clone <repository-url> task-management
cd task-management
```

**2. Install PHP dependencies**

```bash
composer install
```

**3. Configure environment**

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your `DB_*` values to point at your local MySQL instance.

**4. Create the database**

```sql
CREATE DATABASE `task-management` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**5. Run migrations**

```bash
php artisan migrate
```

Creates all required tables: `users`, `tasks` (with `soft_deletes`), `sessions`, `cache`, `jobs`, and `personal_access_tokens`.

**6. (Optional) Seed demo data**

Creates one test user and 10 sample tasks:

```bash
php artisan db:seed
```

| Field | Value |
|---|---|
| Email | `test@example.com` |
| Password | `password` |

**7. Install and build frontend assets**

```bash
npm install
npm run build
```

---

## Running the Application

### Development (with hot reload)

```bash
composer run dev
```

This concurrently starts:

| Process | Description |
|---|---|
| `php artisan serve` | PHP development server at `http://localhost:8000` |
| `npm run dev` | Vite HMR server |
| `php artisan queue:listen` | Queue worker (for database-driven jobs/emails) |
| `php artisan pail` | Real-time log watcher |

Open [http://localhost:8000](http://localhost:8000) in your browser.

---

## API Reference

All endpoints are protected — requests must carry an active session cookie and the `X-CSRF-TOKEN` header (handled automatically by `window.axios` in the frontend through JavaScript).

**Base path:** `/api`

### Task endpoints

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/tasks` | List tasks (paginated, 6 per page) |
| `POST` | `/api/tasks` | Create a new task |
| `GET` | `/api/tasks/{id}` | Retrieve a single task |
| `PATCH` | `/api/tasks/{id}` | Update a task |
| `DELETE` | `/api/tasks/{id}` | Soft-delete a task |

### `GET /api/tasks` — query parameters

| Parameter | Type | Description |
|---|---|---|
| `page` | integer | Page number (default: `1`) |
| `status` | string | Filter by `pending`, `in_progress`, or `completed` |
| `deleted` | string | Pass `only` to return soft-deleted tasks instead of active ones |

### Task object

```json
{
  "id": 1,
  "title": "Design Landing Page",
  "description": "Create a modern hero section.",
  "status": "pending",
  "priority": 1,
  "due_date": "2026-03-20",
  "user_id": 1,
  "deleted_at": null,
  "created_at": "2026-03-13T10:00:00.000000Z",
  "updated_at": "2026-03-13T10:00:00.000000Z"
}
```

### Reference values

**Status**

| Value | Meaning |
|---|---|
| `pending` | Not yet started |
| `in_progress` | Actively being worked on |
| `completed` | Finished |

**Priority**

| Value | Label |
|---|---|
| `0` | Low |
| `1` | Medium |
| `2` | High |

**Status transition rules**

```
pending → in_progress → completed
```

Any other direction (skip or backward) returns `422 Unprocessable Content` with a `status` validation error. This is enforced in `UpdateTaskRequest` and mirrored in the dashboard UI.

---

## Design Decisions & Assumptions

**Session-based API auth instead of Sanctum token API**
The dashboard is a Blade SPA ( single page application) inside the same Laravel application, which shares the user's existing login session. The API routes use `middleware(['web', 'auth'])` rather than `auth:sanctum`. This removes the need for token issuance and management while still providing security for the application. *Assumption: the API is consumed only by the bundled frontend, and not by external third-party clients.*

**Priority stored as integer**
The `priority` column is an `integer` (0/1/2) rather than an `enum`. This is done for easier backend coding. The UI maps the integers to known labels (Low / Medium / High).

**Soft deletes is used for tracking, and not as a recycle bin**
The "Deleted" sidebar filter shows soft-deleted tasks as read-only records. Restore functionaltiy was not implemented in this application. *Assumption: soft deletes exist for tracking purposes.*