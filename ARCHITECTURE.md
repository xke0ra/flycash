# FlyCash v4 — Architecture

## 1. Overview

PHP 8.3 application with two architectural layers coexisting:

- **Legacy layer** (`admin/`, `dashboard/`, `postbacks/`): procedural-style PHP with classes extending `db_connect` (e.g. `functions`, `requests` → now `redemptions`). Autoloaded via `admin/core/autoload.inc.php` with `spl_autoload_register`. Several legacy classes now act as facades delegating to the modern layer:
  - `functions` → delegates to `PointsService`, `SecurityService`, `UserService`, `ConfigService`, `OfferwallService`, `AnalyticsService`, `NotificationService`
  - `offerwalls` → delegates to `OfferwallService`
- **Modern layer** (`src/`): PSR-4 namespaced code under `FlyCash\` namespace. Service classes (`AuthService`, `PointsService`, `SecurityService`, etc.), PSR-7-style HTTP handling, and the postback processing pipeline.
- **Bridge**: `bootstrap.php` loads Composer autoloader, Dotenv, and Monolog. `admin/core/init.inc.php` calls `bootstrap.php` and then sets up legacy constants, session, database connection (`$dbo` PDO instance), and instantiates legacy singletons.

The legacy admin controllers (`admin/controller/`), process handlers (`admin/process/`), and API endpoints (`admin/api/`) delegate core logic to `src/Services` where feasible. Static analysis (PHPStan level 6) covers `src/`, `admin/core/`, `admin/controller/`, `admin/process/`, `admin/api/`, `dashboard/`, `postbacks/`, and `refer/`.

## 2. Database

Single MySQL database, managed via Phinx migrations (`db/migrations/`).

- **`users`**: core user accounts. Indexes on `login`, `email`, `refer`, `referer`.
- **`redemptions`**: unified payout requests (merged from legacy `requests` + `completed`). Status enum: `pending|processing|completed|rejected|cancelled`. Indexes on `user_id`, `status`, and `(user_id, status)`.
- **`postback_log`**: idempotency guard — `UNIQUE(provider, transaction_id)` prevents double-crediting. Indexed on `transaction_id` and `(provider, created_at)`.
- **`tracker`**: points transaction log. Indexes on `username`, `type`, `date`, `user_id`, and compound `(user_id, date)` for OgAds daily cap queries.
- **`configuration`**: key-value settings. Indexed on `config_name`, with instance-level caching in `ConfigService`.
- **`offerwalls`**: offerwall definitions queried by `position` (indexed). Custom URLs use `{user_id}` placeholder.
- **`analytics`**: daily aggregated stats.
- **`rate_limits`**: per-minute signin attempt tracking.
- **`audit_log`**: admin action trail.

No foreign key constraints are enforced at the MySQL level; referential integrity is maintained by application logic (except `redemptions.user_id → users.id` which has a FK).

## 3. Security

- **Postback idempotency**: `Handler::handle()` first performs an atomic INSERT claim into `postback_log` (with `UNIQUE(provider, transaction_id)`) **before** crediting the user. On exception after claim, the row is updated to `failed`. Duplicates are rejected indefinitely.
- **HMAC signatures**: every offerwall handler in `src/Postback/Handlers/` verifies a `hash_hmac('sha256', ...)` signature. Falls back to IP whitelist if no secret is configured. `Handler::verifySignature()` is `abstract` — new handlers must implement it at compile time.
- **Security headers**: emitted from PHP in `init.inc.php`: `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `Permissions-Policy`, `Cross-Origin-Resource-Policy`, `Cross-Origin-Opener-Policy`, `Strict-Transport-Security` (production only).
- **Session**: `HttpOnly` + `SameSite=Lax`; `Secure` in production.
- **CSRF**: token validated on state-changing POST requests via `helper::verifyCsrfToken()`.
- **Rate limiting**: API v3 and v4 signin endpoints limit to 5 attempts/minute per IP.
- **No `die()` in push**: `sendPush()` failures no longer block point transactions (Phase 0.2 fix).

## 4. Frontend

- **Design system**: `dashboard/assets/css/modern.css` (1253 lines) — CSS custom properties and component classes. Loaded on every dashboard page.
- **Legacy bundle**: `plugins.bundle.css` (418 KB, ~2.6% used) and `style.bundle.css` (1.3 MB, ~3.3% used) coexist. Trimmed variants exist for future deployment (`*.trimmed.css` in `tools/`).
- **JS**: jQuery-based with vanilla ES6 additions. PWA manifest + service worker scaffolding.
- **CSS audit tools**: `tools/css-audit.php`, `tools/css-trim.php`, `tools/css-audit-test.php` — unused class detection and bundle trimming.

## 5. Tests

Three suites under `tests/`:

- **`tests/Unit/`** (23 tests): service-layer tests mocking PDO. Fast, no database required.
- **`tests/Integration/`** (23 tests): postback handler (9 handlers × 2-6 tests each), API v4, and redemptions queries against a real test database.
- **`tests/Feature/`** (32 tests): full-stack flow tests — auth, redeem, postback, admin actions, password reset, v3 deprecation, and Phase 0 regression.

**Total: 137 tests, 273 assertions.** Run with `composer test`.

E2E manual scenarios in `tests/E2E/README.md` covering full user journeys, duplicate rejection, and rate limiting.

Policy: every critical financial or security bugfix must be accompanied by a permanent regression test (see `tests/Unit/Phase0RegressionTest.php` for examples).
