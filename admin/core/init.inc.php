<?php

    /*!
	 * FLY CASH v4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2022 AYM ( http://www.aym.com )
	 */

// --- Security Headers (6.5) ---
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
header("X-Permitted-Cross-Domain-Policies: none");

// --- HTTPS Enforcement (6.2) ---
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    if (!empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REQUEST_URI'])) {
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header("Location: " . $redirect);
        exit;
    }
}

header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");

// --- Secure Session Config (6.2) ---
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// --- IP Ban Check (6.1) ---
$clientIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';

// Load configs (database credentials etc.)
require 'config.php';

// Please Do Not Edit Below

foreach ($B as $name => $val) {

    define($name, $val);
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

    define($name, $val);
}

// The auto-loader which loads classes automatically
require 'autoload.inc.php';

$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
$dbo = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

$helper = new helper($dbo);
$auth = new auth($dbo);

// --- IP Ban Check (6.1) ---
$banCheck = new functions($dbo);
if ($banCheck->isIpBanned($clientIp)) {
    header("HTTP/1.0 403 Forbidden");
    echo json_encode(array("error" => true, "error_code" => 403, "error_description" => "Your IP has been banned due to suspicious activity."));
    exit;
}
