<?php

/**
 * FLY CASH v4 - Bootstrap
 *
 * Loads Composer autoloader, environment variables, and initializes logging.
 */

// 1. Load Composer autoloader
$autoloadPath = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

if (!file_exists($autoloadPath)) {
    die('Composer autoloader not found. Please run: composer install');
}

require $autoloadPath;

// 2. Load .env (if exists - fall back to config.php for DB credentials)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . '.env')) {
    $dotenv->load();
}

// 3. Set timezone
$timezone = $_ENV['APP_TIMEZONE'] ?? 'UTC';
date_default_timezone_set($timezone);

// 4. Initialize Monolog
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;

$logLevel = strtoupper($_ENV['LOG_LEVEL'] ?? 'ERROR');
$logFile = __DIR__ . DIRECTORY_SEPARATOR . ($_ENV['LOG_FILE'] ?? 'logs/app.log');

$logDir = dirname($logFile);

if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

if (!is_writable($logDir)) {
    chmod($logDir, 0755);
}

$logger = new Logger('flycash');
$handler = new RotatingFileHandler($logFile, 30, Level::fromName($logLevel));
$logger->pushHandler($handler);

$GLOBALS['logger'] = $logger;
