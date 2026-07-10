<?php

/**
 * Test bootstrap — loads Composer autoloader and sets up test environment.
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load .env.testing if it exists, otherwise .env
$dotenvFile = __DIR__ . '/../.env.testing';
if (!file_exists($dotenvFile)) {
    $dotenvFile = __DIR__ . '/../.env';
}

if (file_exists($dotenvFile)) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..', basename($dotenvFile));
    $dotenv->safeLoad();
}

// Set constants that init.inc.php would normally define
if (!defined('DB_HOST') && isset($_ENV['DB_HOST'])) {
    define('DB_HOST', $_ENV['DB_HOST']);
    define('DB_NAME', $_ENV['DB_NAME']);
    define('DB_USER', $_ENV['DB_USER']);
    define('DB_PASS', $_ENV['DB_PASS']);
}

// Define app constants (subset of init.inc.php)
$constants = [
    'CLIENT_ID' => 1,
    'ERROR_SUCCESS' => 0,
    'ERROR_UNKNOWN' => 100,
    'ERROR_ACCESS_TOKEN' => 101,
    'ERROR_LOGIN_TAKEN' => 300,
    'ERROR_EMAIL_TAKEN' => 301,
    'ERROR_IP_TAKEN' => 302,
    'ERROR_ACCOUNT_ID' => 400,
    'APP_DEMO' => false,
    'APP_PATH' => 'app',
    'GCM_NOTIFY_CONFIG' => 0,
    'GCM_NOTIFY_SYSTEM' => 1,
    'GCM_NOTIFY_CUSTOM' => 2,
    'GCM_NOTIFY_LIKE' => 3,
    'GCM_NOTIFY_ANSWER' => 4,
    'GCM_NOTIFY_QUESTION' => 5,
    'GCM_NOTIFY_COMMENT' => 6,
    'GCM_NOTIFY_FOLLOWER' => 7,
    'ACCOUNT_STATE_ENABLED' => 0,
    'ACCOUNT_STATE_DISABLED' => 1,
    'ACCOUNT_STATE_BLOCKED' => 2,
    'ACCOUNT_STATE_DEACTIVATED' => 3,
];

foreach ($constants as $name => $val) {
    if (!defined($name)) {
        define($name, $val);
    }
}

// Load legacy autoloader for classes like auth, helper, functions, account, etc.
$legacyAutoload = __DIR__ . '/../admin/core/autoload.inc.php';
if (file_exists($legacyAutoload)) {
    require_once $legacyAutoload;
}

// Set up Monolog for testing if logger not already set
if (!isset($GLOBALS['logger'])) {
    $logger = new Monolog\Logger('test');
    $logger->pushHandler(new Monolog\Handler\NullHandler());
    $GLOBALS['logger'] = $logger;
}
