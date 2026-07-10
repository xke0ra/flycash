<?php

if ($method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

requireParams(array('username', 'password'), $data);
validateClient($data);

$username = helper::clearText($data['username']);
$username = helper::escapeText($username);

$password = isset($data['password']) ? trim($data['password']) : '';

// Rate limiting
$ip = helper::ip_addr();
if (!$api->checkRateLimit($ip, 'api_login', 5, 60)) {
    jsonError(ERROR_UNKNOWN, "Too many attempts. Please try again later.", 429);
}

$account = new account($dbo);
$access_data = $account->signin($username, $password);

if ($access_data["error"] === false) {
    $auth = new auth($dbo);
    $access_data = $auth->create($access_data['accountId'], CLIENT_ID);

    if ($access_data['error'] === false) {
        $account = new account($dbo, $access_data['accountId']);
        $access_data['account'] = array($account->get());
        $access_data['config'] = array($account->getConfigs());
        jsonResponse($access_data, 200);
    }
} else {
    $api->logFailedAttempt($username);
}

jsonResponse($access_data, 401);
