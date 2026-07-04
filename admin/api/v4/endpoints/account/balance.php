<?php

if ($method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

validateClient($data);
$authData = authorizeRequest($data, $dbo);

$account = new account($dbo, $authData['accountId']);
$userdata = $account->get();

$username = isset($data['user']) ? helper::clearText($data['user']) : '';

if (!empty($username) && $userdata['username'] !== $username) {
    jsonError(ERROR_UNKNOWN, "Account Mismatch", 403);
}

jsonResponse(array(
    "error" => false,
    "error_code" => ERROR_SUCCESS,
    "user_balance" => $userdata['points']
), 200);
