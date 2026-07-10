<?php
require_once __DIR__ . '/../../core/init.inc.php';

header("Content-type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Accept");
header("X-API-Version: 4.0");

$allowedOrigins = isset($_ENV['ALLOWED_ORIGINS']) && $_ENV['ALLOWED_ORIGINS'] !== ''
    ? array_map('trim', explode(',', $_ENV['ALLOWED_ORIGINS']))
    : [];
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
$allowed = \FlyCash\Api::resolveCorsOrigin($origin, $allowedOrigins);
if ($allowed !== null) {
    header("Access-Control-Allow-Origin: $allowed");
    header("Vary: Origin");
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

\FlyCash\Api::logRequest();

$result = array("error" => false, "error_code" => ERROR_SUCCESS);

function jsonResponse($data, $httpCode = 200) {
    \FlyCash\Api::jsonResponse($data, $httpCode);
}

function jsonError($errorCode, $message = "Unknown error", $httpCode = 400) {
    \FlyCash\Api::jsonError($errorCode, $message, $httpCode);
}

function getJsonBody() {
    return \FlyCash\Api::getJsonBody();
}

function requireParams($params, $data) {
    \FlyCash\Api::requireParams($params, $data);
}

function validateClient($data) {
    \FlyCash\Api::validateClient($data);
}

function authorizeRequest($data, $dbo) {
    return \FlyCash\Api::authorizeRequest($data, $dbo);
}

$api = new functions($dbo);
if (!$api->getConfig('ADMIN')) {
    jsonError(999, "Server not configured", 503);
}
