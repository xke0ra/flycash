<?php

    /*!
     * FLY CASH API v4
     *
     * http://www.aym.com
     * support@aym.com
     *
     * Copyright 2022 AYM ( http://www.aym.com )
     */

require_once __DIR__ . '/../../core/init.inc.php';

header("Content-type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Accept");
header("X-API-Version: 4.0");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$result = array("error" => false, "error_code" => ERROR_SUCCESS);

function jsonResponse($data, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonError($errorCode, $message = "Unknown error", $httpCode = 400) {
    http_response_code($httpCode);
    echo json_encode(array(
        "error" => true,
        "error_code" => $errorCode,
        "error_description" => $message
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

function getJsonBody() {
    $raw = file_get_contents("php://input");
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : '';

    if (stripos($contentType, 'application/json') !== false) {
        $data = json_decode($raw, true);
        if (is_array($data)) return $data;
    }

    if (!empty($_POST['data'])) {
        $decoded = json_decode($_POST['data'], true);
        if (is_array($decoded)) return $decoded;
    }

    if (!empty($_POST)) return $_POST;

    if (!empty($raw)) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) return $decoded;
    }

    return array();
}

function requireParams($params, $data) {
    foreach ($params as $p) {
        if (!isset($data[$p]) || (is_string($data[$p]) && trim($data[$p]) === '')) {
            jsonError(ERROR_UNKNOWN, "Missing required parameter: " . $p, 400);
        }
    }
}

function validateClient($data) {
    $clientId = isset($data['clientId']) ? intval($data['clientId']) : 0;
    if ($clientId != CLIENT_ID) {
        jsonError(ERROR_UNKNOWN, "Invalid client Id.", 401);
    }
}

function authorizeRequest($data, $dbo) {
    $accountId = isset($data['accountId']) ? intval($data['accountId']) : 0;
    $accessToken = isset($data['accessToken']) ? $data['accessToken'] : '';

    if (empty($accountId) || empty($accessToken)) {
        jsonError(ERROR_ACCESS_TOKEN, "Missing authorization credentials.", 401);
    }

    $auth = new auth($dbo);
    if (!$auth->authorize($accountId, $accessToken)) {
        jsonError(ERROR_ACCESS_TOKEN, "Invalid or expired token.", 401);
    }

    return array('accountId' => $accountId, 'accessToken' => $accessToken);
}

$api = new functions($dbo);
if (!$api->getConfig('ADMIN')) {
    jsonError(999, "Server not configured", 503);
}
