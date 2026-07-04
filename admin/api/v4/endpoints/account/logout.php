<?php

if ($method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

validateClient($data);
$authData = authorizeRequest($data, $dbo);

$auth = new auth($dbo);
$auth->remove($authData['accountId'], $authData['accessToken']);

jsonResponse(array(
    "error" => false,
    "error_code" => ERROR_SUCCESS
), 200);
