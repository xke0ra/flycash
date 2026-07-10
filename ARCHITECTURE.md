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

- **`redemptions`** (unified table, merged from the legacy `requests` + conceptual `completed` split): stores all redeem requests with a `status` column (`pending`, `processing`, `completed`, `rejected`). The `redemptions` class in `admin/core/class.redemptions.inc.php` provides both filtered (by status) and unfiltered queries.
- **`users`**: core user accounts. `user_id` in `redemptions`, `tracker`, and `analytics` acts as a foreign key.
- **`postback_log`**: idempotency guard — every offerwall postback inserts a row keyed by `transaction_id`. Duplicates are silently accepted (no error returned to the offerwall) but no points are credited twice.
- **`tracker`**: earning history per user per offerwall.
- **`analytics`**: aggregated session/activity data.

No foreign key constraints are enforced at the MySQL level; referential integrity is maintained by application logic.

## 3. Security

- **Postback idempotency**: `Handler::handle()` first performs an atomic INSERT claim into `postback_log` (with `UNIQUE(provider, transaction_id)`) **before** crediting the user. This ensures that even if `creditUser()` or a side-effect (e.g. push notification) fails, the transaction ID is already recorded and any retry will be rejected as a duplicate. No time window — duplicates are blocked indefinitely.
- **HMAC signatures**: every offerwall handler in `src/Postback/Handlers/` verifies a `hash_hmac('sha256', ...)` signature using the provider's shared secret. Falls back to IP whitelist if no secret is configured. The base `Handler::verifySignature()` is declared `abstract` — any new handler MUST implement signature verification (compile-time guarantee).
- **Security headers**: sent from PHP in `admin/core/init.inc.php`: `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `Permissions-Policy`, `Cross-Origin-Resource-Policy`, `Cross-Origin-Opener-Policy`, `Strict-Transport-Security` (production only). This ensures headers work regardless of web server (Apache, Nginx, etc.).
- **Session**: `HttpOnly` and `SameSite=Lax` set in PHP; `Secure` flag enabled in production.
- **CSRF**: token validated on state-changing POST requests via `helper::verifyCsrfToken()`.
- **IP ban**: checked on every admin/dashboard request via `functions::isIpBanned()`.

No `.htaccess`-only security — all critical headers are emitted from PHP for server-independence.

## 4. Frontend

- **Design system**: `dashboard/assets/css/modern.css` (1253 lines) — CSS custom properties (tokens `--primary`, `--gray-*`, `--shadow-*`, etc.) and component classes. Loaded on every dashboard page via `dashboard/includes/global_header_scripts.php`.
- **Coexisting legacy bundle**: `plugins.bundle.css` (418 KB, 3632 classes, ~2.6% used in PHP) and `style.bundle.css` (1.3 MB, 6112 classes, ~3.3% used) are also loaded. The legacy bundle is a pre-built Metronic/theme stylesheet; the modern CSS overrides and extends it.
- **JS**: jQuery-based with some vanilla ES6. PWA manifest with service worker scaffolding.
- **Loading order**: fonts → legacy bundle CSS → app.css → modern.css → notifications.bundle.css → JS bundles.

See Item 6 of the cleanup session for a detailed CSS usage report.

## 5. Tests

Three suites under `tests/`:

- **`tests/Unit/`**: service-layer tests (`Services/*`) mocking PDO. Fast, no database required.
- **`tests/Integration/`**: API and postback handler tests against a real test database.
- **`tests/Feature/`**: full-stack flow tests (auth, redeem, admin actions, postback flow).

Run with `composer test` (PHPUnit). Static analysis: `composer analyse` (PHPStan level 6).

Policy: every critical financial or security bugfix must be accompanied by a permanent regression test (see `tests/Unit/Phase0RegressionTest.php` for examples covering gcm_regid crash, signature verification, and duplicate transaction rejection).
