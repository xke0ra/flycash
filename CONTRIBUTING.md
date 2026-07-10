# Contributing

## Code Standards

1. **PHP 8.3+** — typed properties, union types, match expressions preferred.
2. **PSR-4 autoloading** — new classes go in `src/` under `FlyCash\` namespace.
3. **No `die()`/`exit()` in business logic** — return `false` or throw; let the controller/endpoint handle output.
4. **No commented-out code** — delete dead code; git history preserves it.
5. **Security headers** — always emit from PHP, not `.htaccess` (for server-independence).
6. **New postback handlers** — must implement `parseRequest()`, `validateUser()`, `verifySignature()`, `creditUser()`. `verifySignature()` is `abstract` — required at compile time.

## Testing

1. Run `composer test` before every commit.
2. Run `composer analyse` — must be 0 errors at level 6.
3. Every critical financial or security fix needs a **regression test** (see `tests/Unit/Phase0RegressionTest.php`).
4. New postback handlers need **integration tests** (see `tests/Integration/Postback/` for examples).
5. Tests that require a database use the real `pocket_db` — ensure your `.env` has valid credentials.

## Workflow

1. One commit per logical change with clear message.
2. Use conventional commit prefixes: `fix:`, `feat:`, `refactor:`, `docs:`, `test:`.
3. Run `composer test && composer analyse` after each commit.
4. Update `docs/schema_reference.sql` when modifying the schema.
5. Add Phinx migration for schema changes, never raw SQL.
