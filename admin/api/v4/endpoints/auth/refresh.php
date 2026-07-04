<?php

if ($method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

requireParams(array('refreshToken'), $data);
validateClient($data);

$refreshToken = helper::clearText($data['refreshToken']);
$refreshToken = helper::escapeText($refreshToken);

$auth = new auth($dbo);
$result = $auth->refresh($refreshToken);

if ($result['error'] === false) {
    jsonResponse($result, 200);
}

jsonResponse($result, 401);
