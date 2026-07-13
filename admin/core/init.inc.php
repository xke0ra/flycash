<?php
// --- Security Headers (set in PHP for server-independence: works on Apache, Nginx, etc.) ---
header_remove('X-Powered-By');
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
header("X-Permitted-Cross-Domain-Policies: none");
header("Cross-Origin-Resource-Policy: same-origin");
header("Cross-Origin-Opener-Policy: same-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://www.youtube.com https://www.youtube.com/iframe_api https://s.ytimg.com 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data: https://api.qrserver.com; font-src 'self' https://fonts.gstatic.com; frame-src 'self' https:; connect-src 'self'; form-action 'self'");

// --- Secure Session Config (6.2) ---
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// --- Load Bootstrap (Composer, .env, Monolog) ---
require __DIR__ . '/../../bootstrap.php';

// --- HTTPS Enforcement (6.2) - يعتمد على APP_ENV من .env ---
$isDev = (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development');

if (!$isDev) {
    if ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') && (!isset($_SERVER['HTTP_X_FORWARDED_PROTO']) || $_SERVER['HTTP_X_FORWARDED_PROTO'] !== 'https')) {
        if (!empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REQUEST_URI'])) {
            $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header("Location: " . $redirect);
            exit;
        }
    }
}

if (!$isDev) {
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
    ini_set('session.cookie_secure', 1);
}

// --- Global Exception Handler (Phase 5) ---
set_exception_handler(function (Throwable $e) {
    $logger = $GLOBALS['logger'] ?? null;
    if ($logger) {
        $logger->error('Uncaught exception', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ]);
    }
    $isApi = (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false);
    if ($isApi) {
        header('Content-Type: application/json', true, 500);
        echo json_encode(['error' => true, 'error_code' => 500, 'error_description' => 'Internal server error.']);
    } else {
        header('HTTP/1.0 500 Internal Server Error', true, 500);
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>500 Internal Server Error</title>';
        echo '<style>body{font-family:sans-serif;background:#f8fafc;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}';
        echo '.card{background:#fff;border-radius:16px;padding:40px;text-align:center;box-shadow:0 20px 50px -12px rgba(0,0,0,.15)}';
        echo 'h1{font-size:48px;color:#6366f1;margin:0 0 8px}p{color:#64748b;font-size:14px;margin:0}</style>';
        echo '</head><body><div class="card"><h1>500</h1><p>Something went wrong. Please try again later.</p></div></body></html>';
    }
    exit;
});

// --- IP Ban Check (6.1) ---
$clientIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';

// Load DB config from .env or fall back to config.php
if (isset($_ENV['DB_HOST'])) {
    $B['DB_HOST'] = $_ENV['DB_HOST'];
    $B['DB_NAME'] = $_ENV['DB_NAME'];
    $B['DB_USER'] = $_ENV['DB_USER'];
    $B['DB_PASS'] = $_ENV['DB_PASS'];
    $INSTALL_STATUS = "SUCCESS";
} else {
    require 'config.php';
}

// Please Do Not Edit Below

foreach ($B as $name => $val) {
    if (!defined($name)) {
        define($name, $val);
    }
}

$C = array();

$C['APP_DEMO'] = false;                          
$C['APP_PATH'] = "app";
$C['CLIENT_ID'] = 1;  //Android App Client ID (only for android application)
$C['ERROR_SUCCESS'] = 0;

$C['ERROR_UNKNOWN'] = 100;
$C['ERROR_ACCESS_TOKEN'] = 101;

$C['ERROR_LOGIN_TAKEN'] = 300;
$C['ERROR_EMAIL_TAKEN'] = 301;
$C['ERROR_IP_TAKEN'] = 302;

$C['ERROR_ACCOUNT_ID'] = 400;

$C['GCM_NOTIFY_CONFIG'] = 0;
$C['GCM_NOTIFY_SYSTEM'] = 1;
$C['GCM_NOTIFY_CUSTOM'] = 2;
$C['GCM_NOTIFY_LIKE'] = 3;
$C['GCM_NOTIFY_ANSWER'] = 4;
$C['GCM_NOTIFY_QUESTION'] = 5;
$C['GCM_NOTIFY_COMMENT'] = 6;
$C['GCM_NOTIFY_FOLLOWER'] = 7;

$C['ACCOUNT_STATE_ENABLED'] = 0;
$C['ACCOUNT_STATE_DISABLED'] = 1;
$C['ACCOUNT_STATE_BLOCKED'] = 2;
$C['ACCOUNT_STATE_DEACTIVATED'] = 3;

$time = time();

foreach ($C as $name => $val) {
    if (!defined($name)) {
        define($name, $val);
    }
}

// The auto-loader which loads classes automatically
require 'autoload.inc.php';

$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
$dbo = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

// --- Database Wrapper (Phase 3) ---
$GLOBALS['flycash_db'] = new FlyCash\Database($dbo);

$helper = new helper($dbo);
$auth = new auth($dbo);

// Global esc_attr for backward compatibility (replaces 4 local definitions)
if (!function_exists('esc_attr')) {
    function esc_attr($attr) { return \helper::esc_attr($attr); }
}

// --- IP Ban Check (6.1) ---
$banCheck = new functions($dbo);
if ($banCheck->isIpBanned($clientIp)) {
    $GLOBALS['logger']->warning('Blocked banned IP', ['ip' => $clientIp]);
    header("HTTP/1.0 403 Forbidden");
    echo json_encode(array("error" => true, "error_code" => 403, "error_description" => "Your IP has been banned due to suspicious activity."));
    exit;
}
