<?php

    /*!
     * FLY CASH API v4 Router
     *
     * http://www.aym.com
     * support@aym.com
     *
     * Copyright 2022 AYM ( http://www.aym.com )
     */

require_once __DIR__ . '/api_v4.inc.php';

$endpoint = isset($_GET['endpoint']) ? preg_replace('/[^a-zA-Z0-9_\/-]/', '', $_GET['endpoint']) : '';

if (empty($endpoint)) {
    jsonError(ERROR_UNKNOWN, "No endpoint specified.");
}

$method = $_SERVER['REQUEST_METHOD'];
$handler = __DIR__ . '/endpoints/' . str_replace('/', DIRECTORY_SEPARATOR, $endpoint) . '.php';

if (!file_exists($handler)) {
    jsonError(ERROR_UNKNOWN, "Endpoint not found: " . $endpoint);
}

$data = getJsonBody();

require $handler;
