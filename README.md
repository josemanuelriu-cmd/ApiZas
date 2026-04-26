# ApiZas

<p align="center">
  <a href="https://apizas-production.up.railway.app/docs"><strong>📄 Scribe Documentation</strong></a>
  &nbsp;·&nbsp;
  <a href="https://apizas-production.up.railway.app/api/docs-scalar"><strong>🌙 Scalar Documentation</strong></a>
</p>

---

## 📚 Table of Contents

- [About](#about)
- [Tech Stack](#-tech-stack)
- [Features & Endpoints](#features--endpoints)
- [Roles & Permissions](#-roles--permissions)
- [Setup & Installation](#-setup--installation)
- [Environment Variables](#-environment-variables)
- [API Documentation](#-api-documentation)
- [Testing](#-testing)
- [Postman Collection](#-postman-collection)
- [Upcoming Improvements](#-upcoming-improvements)

---

## 🚀 Production

The API is live at:

**`https://apizas-production.up.railway.app/api/v1/`**

| Interface | URL |
|-----------|-----|
| API | `https://apizas-production.up.railway.app/api/v1/` |
| Scribe docs | `https://apizas-production.up.railway.app/docs` |
| Scalar UI | `https://apizas-production.up.railway.app/api/docs-scalar` |

---

## About

**ApiZas** is a RESTful API built with Laravel 12 designed to manage board games, sessions, and matches between users.

It includes authentication via Laravel Passport, role-based access control, session participation, game tracking, and statistics.

All endpoints follow REST conventions and are versioned under:

```
/api/v1/
```

---

## 💻 Tech Stack

- **Runtime:** PHP 8.2
- **Framework:** Laravel 12
- **Authentication:** Laravel Passport (OAuth2 — Bearer Token)
- **Database:** MySQL
- **Architecture:** RESTful API
- **Testing:** PHPUnit (TDD)
- **Documentation:** Scribe + Scalar

---

## Features & Endpoints

All endpoints are prefixed with `/api/v1/`

---

### 🔐 Authentication

- `POST /login` → Authenticate user
- `POST /register` → Register new user
- `POST /logout` → Logout *(requires auth)*

---

### 👤 Users

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | `/users` | List all users | admin, junta |
| GET | `/users/{id}` | Get user detail | admin, junta |
| POST | `/users` | Create user | admin |
| PUT | `/users/{id}` | Update user | owner / admin |
| DELETE | `/users/{id}` | Delete user | admin |

---

### 🧩 Types (Game Categories)

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | `/types` | List types | admin, junta, partner |
| GET | `/types/{id}` | Type detail | admin, junta, partner |
| POST | `/types` | Create type | admin, junta |
| PUT | `/types/{id}` | Update type | admin, junta |
| DELETE | `/types/{id}` | Delete type | admin |

---

### 🎲 Boardgames

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | `/boardgames` | List all board games | authenticated |
| GET | `/boardgames/{id}` | Game details | authenticated |
| POST | `/boardgames` | Create game | admin, junta |
| PUT | `/boardgames/{id}` | Update game | admin, junta |
| DELETE | `/boardgames/{id}` | Delete game | admin, junta |

---

### 🧑‍🤝‍🧑 Zassessions (Game Sessions)

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | `/zassessions` | List sessions | authenticated |
| GET | `/zassessions/{id}` | Session detail | authenticated |
| POST | `/zassessions` | Create session | admin, junta |
| PUT | `/zassessions/{id}` | Update session | admin, junta |
| DELETE | `/zassessions/{id}` | Delete session | admin, junta |
| POST | `/zassessions/{id}/join` | Join session | authenticated |
| DELETE | `/zassessions/{id}/leave` | Leave session | authenticated |
| GET | `/zassessions/{id}/users` | Session users | authenticated |

#### 📊 Session Stats
- `GET /zassessions/stats` → Global stats *(admin, junta, partner)*
- `GET /zassessions/{id}/stats` → Session stats *(admin, junta, partner)*

#### 🎮 Session Games
- `GET /zassessions/{id}/games` → Games in session *(all roles)*

---

### 🎮 Games (Matches)

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | `/games` | List all matches | admin, junta, partner |
| GET | `/games/{id}` | Match detail | authenticated |
| POST | `/games` | Create match | admin, junta, partner |
| PUT | `/games/{id}` | Update match | admin, junta |
| DELETE | `/games/{id}` | Delete match | admin, junta |
| POST | `/games/{id}/join` | Join match | authenticated |
| DELETE | `/games/{id}/leave` | Leave match | authenticated |
| GET | `/games/{id}/users` | Match players | authenticated |

#### 📊 Game Stats
- `GET /games/{id}/stats` → Match stats *(admin, junta, partner)*

---

## 🔐 Roles & Permissions

The API implements **role-based access control**:

| Role | Description |
|------|-------------|
| **admin** | Full access to all endpoints |
| **junta** | Full access except editing other users |
| **partner** | Limited read + create games + view stats |
| **guest** | Join/leave sessions and games only |

---

## 🔧 Setup & Installation

### Prerequisites

- PHP >= 8.2
- Composer
- MySQL
- XAMPP (or equivalent)

---

### Clone repository

```bash
git clone https://github.com/josemanuelriu-cmd/ApiZas.git
cd ApiZas
```

### Enable Sodium extension (XAMPP)

Open `C:\xampp\php\php.ini`, find `;extension=sodium`, remove the semicolon, save and restart XAMPP.

### Install dependencies

```bash
composer install
```

### Configure environment

```bash
cp .env.example .env
```

### Generate keys

```bash
php artisan key:generate
php artisan passport:keys
```

### Run migrations and seed

```bash
php artisan migrate --seed
```

> The seeder automatically creates the Passport personal access client.

### Start server

```bash
php artisan serve
```

API available at: `http://localhost:8000/api/v1/`

---

## 🔑 Environment Variables

```env
APP_NAME=ApiZas
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=apizas
DB_USERNAME=root
DB_PASSWORD=
```

---

## 📖 API Documentation

Generate documentation:

```bash
php artisan scribe:generate
```

Then visit:

| Interface | URL |
|-----------|-----|
| Scribe HTML | `http://localhost:8000/docs` |
| Scalar UI | `http://localhost:8000/api/docs-scalar` |
| OpenAPI spec | `http://localhost:8000/docs/openapi.yaml` |
| Postman collection | `http://localhost:8000/docs/collection.json` |

---

## 🧪 Testing

This project was developed using **TDD (Test-Driven Development)**. Tests are written before implementation.

Run all tests:

```bash
php artisan test
```

Run a specific test file:

```bash
php artisan test --filter UserManagementTest
```

| Test file | Coverage |
|-----------|----------|
| UserTest | Authentication |
| UserManagementTest | Users CRUD |
| TypesTest | Types CRUD |
| BoardgameTest | Boardgames CRUD |
| ZassessionTest | Sessions CRUD + join/leave/stats |
| GameTest | Games CRUD + join/leave/stats |

---

## 📮 Postman Collection

Import the auto-generated collection and test the API directly:

1. Import `http://localhost:8000/docs/collection.json` into Postman
2. Set base URL: `http://localhost:8000/api/v1`
3. Authenticate via `POST /login` or `POST /register`
4. Use the returned Bearer token for protected routes

---

## 🚧 Upcoming Improvements

- Pagination for listings
- Advanced filters (by players, type, difficulty)
- Improved stats system
- Notifications for sessions
- Image uploads for board games
