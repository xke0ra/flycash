<?php

if ($method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

requireParams(array('username', 'password'), $data);
validateClient($data);

$username = helper::clearText($data['username']);
$username = helper::escapeText($username);

$password = helper::clearText($data['password']);
$password = helper::escapeText($password);

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
}

jsonResponse($access_data, 401);
