# FlyCash v4

Money Making Script — earn rewards and gift cards through offerwalls, daily check-ins, spins, referrals, and more.

## Requirements

- PHP 8.3+
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- PDO MySQL extension
- cURL extension
- BCMath or GMP (for random int)

## Quick Start

```bash
# 1. Install dependencies
composer install

# 2. Configure environment
cp .env.example .env
# Edit .env with your database credentials

# 3. Create database and run migrations
phinx migrate -e development

# 4. Seed default configuration (optional)
mysql -u root pocket_db < docs/default_data.sql

# 5. Start development server
php -S localhost:8000 -t .
```

Point your browser to `http://localhost:8000/admin/` and follow the installer.

## Common Commands

```bash
composer test              # Run PHPUnit (137 tests)
composer analyse           # Run PHPStan (level 6)
composer testdox           # PHPUnit with --testdox output
phinx migrate              # Apply database migrations
```

## Environment Variables (`.env`)

| Variable    | Description                          | Default       |
|-------------|--------------------------------------|---------------|
| `DB_HOST`   | MySQL host                          | `localhost`   |
| `DB_NAME`   | Database name                       | `pocket_db`   |
| `DB_USER`   | Database user                       | `root`        |
| `DB_PASS`   | Database password                   | *(empty)*     |
| `APP_ENV`   | Application environment             | `development` |
| `APP_DEBUG` | Enable debug mode                   | `true`        |
| `LOG_LEVEL` | Monolog minimum level               | `debug`       |
| `LOG_FILE`  | Log file path (relative to project) | `logs/app.log`|

## Project Structure

```
├── admin/                  # Admin panel (legacy)
│   ├── api/                #   API v3 and v4 endpoints
│   ├── controller/         #   Page controllers
│   ├── core/               #   Legacy classes (autoloaded)
│   ├── process/            #   Form handlers
│   └── ...
├── dashboard/              # User dashboard
├── postbacks/              # Legacy postback entry points
├── src/                    # Modern PSR-4 layer
│   ├── Postback/           #   Postback pipeline (Handler + 9 handlers)
│   ├── Services/           #   Business logic services
│   └── ...
├── tests/                  # PHPUnit tests (137)
│   ├── Unit/               #   Service-layer tests
│   ├── Integration/        #   Handler + API tests
│   ├── Feature/            #   Full flow tests
│   └── E2E/                #   Manual E2E scenarios
├── db/migrations/          # Phinx migrations
├── docs/                   # Documentation
└── tools/                  # Utility scripts (CSS audit)
```

## Architecture

See [ARCHITECTURE.md](ARCHITECTURE.md) for detailed layer documentation.

## Adding Offerwalls

1. Register as a publisher with an offerwall network (AdGem, OGAds, AdGateMedia, etc.).
2. Get your offerwall iframe URL containing a `{user_id}` placeholder.
3. In admin panel: **Offerwalls → Add Offerwall**.
4. Fill name, paste URL (with `{user_id}`), set position, save.

The custom URL mechanism works with **any** network — no hardcoded integrations.

## Testing

```bash
# Run all tests
composer test

# Run specific suite
vendor/bin/phpunit tests/Unit
vendor/bin/phpunit tests/Integration
vendor/bin/phpunit tests/Feature

# Run with testdox output
composer testdox
```

See `tests/E2E/README.md` for manual E2E scenarios covering user registration, earning, and redemption.

## License

Proprietary — all rights reserved.
