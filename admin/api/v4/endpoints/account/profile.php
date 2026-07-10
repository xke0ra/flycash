<?php

if ($method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

validateClient($data);
$authData = authorizeRequest($data, $dbo);
$username = isset($data['user']) ? helper::clearText($data['user']) : '';

$profileService = \FlyCash\Container::get(\FlyCash\Services\ProfileService::class);
$userdata = $profileService->get($authData['accountId']);

if ($userdata === null) {
    jsonError(ERROR_UNKNOWN, "Account not found", 404);
}

if (!empty($username) && $userdata['username'] !== $username) {
    jsonError(ERROR_UNKNOWN, "Account Mismatch", 403);
}

$fcm = isset($data['fcm']) ? $data['fcm'] : 0;
$pckg = isset($data['pckg']) ? $data['pckg'] : "none";

jsonResponse(array(
    "error" => false,
    "error_code" => ERROR_SUCCESS,
    "accessToken" => $authData['accessToken'],
    "accountId" => $authData['accountId'],
    "account" => array($userdata),
    "config" => array($profileService->getConfigs($authData['accountId'], $fcm))
), 200);
