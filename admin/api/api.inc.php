<?php
include_once("../../core/init.inc.php");
include_once("../../core/api.inc.php");

// v3 Deprecation Headers (RFC 9745, RFC 8594)
header("Deprecation: @" . time());
header("Sunset: " . gmdate('D, d M Y H:i:s', strtotime('+120 days')) . " UTC");
header("Link: </docs/openapi.yaml>; rel=\"successor-version\"");

\FlyCash\Logger::info('v3 API endpoint called', [
    'endpoint' => $_SERVER['SCRIPT_NAME'] ?? '',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
]);
