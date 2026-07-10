# Changelog

## [Unreleased]

### Phase 2 — Completing Features & Performance

- **2.3**: Added E2E manual test scenarios (`tests/E2E/README.md`)
- **2.2b**: Instance-level caching in `ConfigService::get()` and `getAll()`
- **2.2a**: Migration for `offerwalls.position` index and `tracker.(user_id, date)` compound index
- **2.1c**: Removed 6 hardcoded offerwall types from `offerwalls-controller.php` (AdMantum, AdGateMedia, AdScendMedia, CpaLead, KiwiWall, Wannads) — now relies on custom URL with `{user_id}` for all networks
- **2.1b**: Reviewed all 36 `creditUserPoints()` call sites — push parameter is intentional everywhere
- **2.1a**: Added integration tests for 5 remaining postback handlers (AdMantum, AdScendMedia, CpaLead, OfferToro, OgAds)
- **2.1a fix**: OgAdsHandler USD→points conversion — was truncating decimal via `getInt()` (2.50 → 2 → 600 pts); now uses `getString()` + `float` (2.50 × 300 = 750 pts)

### Phase 1 — Cleanup & Organization

- **1.3b**: Removed stale `Watch & Earn` folder, updated `.gitattributes` export-ignore, updated ARCHITECTURE.md
- **1.3a**: Expanded `phpstan.neon` to cover `admin/process/`, `dashboard/`, `refer/` — 0 errors at level 6
- **1.2c**: `offerwalls` legacy class delegates to `OfferwallService` (facade pattern)
- **1.2b**: `verifySignature()` made `abstract` in `Handler.php` — all 9 handlers implement it
- **1.2a**: Unified `esc_attr()` — moved to `class.helper.inc.php` + global function guard in `init.inc.php`; removed 4 duplicate definitions
- **1.1**: Referral logic in `class.account.inc.php` now uses `PointsService::creditUserPoints()` (atomic) instead of read-modify-write

### Phase 0 — Critical Fixes

- **0.7**: Regression tests (`tests/Unit/Phase0RegressionTest.php`) for gcm_regid crash, signature verification, duplicate transaction
- **0.6**: Schema updated (`redemptions` table, `UNIQUE(provider, transaction_id)`); CI switched to `phinx migrate`
- **0.5**: Rate limiting added to API v3 and v4 signin endpoints (5 attempts/minute)
- **0.4**: `INSTALL_STATUS` check added inside `install/process.php`; `exit` after every `header()` redirect
- **0.3**: Fixed PHP 8+ deprecated `{var}` syntax → `[var]` in `install/process.php`
- **0.2b**: Atomic postback claim (`claimPostback()` before `creditUser()`) in `Handler::handle()`
- **0.2**: Removed `die()` from `sendPush()` in both `PointsService` and `class.functions.inc.php` — push failure no longer blocks transactions
- **0.1**: Documented offerwall setup instructions (custom URL with `{user_id}`)

### Baseline

- `e7c2775` — FlyCash v4 pre-modernization snapshot
