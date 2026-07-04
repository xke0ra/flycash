<?php

if ($method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

requireParams(array('username', 'password', 'email'), $data);
validateClient($data);

$username = helper::clearText($data['username']);
$username = helper::escapeText($username);

$fullname = isset($data['fullname']) ? helper::clearText($data['fullname']) : $username;
$fullname = helper::escapeText($fullname);

$password = helper::clearText($data['password']);
$password = helper::escapeText($password);

$email = helper::clearText($data['email']);
$email = helper::escapeText($email);

$profile_pic = isset($data['image']) ? $data['image'] : '';
$refererCode = isset($data['referer']) ? $data['referer'] : 0;
$reg_type = isset($data['reg']) ? $data['reg'] : 'Manual';

$account = new account($dbo);
$access_data = $account->signup($username, $fullname, $password, $email, $refererCode, $profile_pic, $reg_type);

if ($access_data["error"] === false) {
    $account->sendVerificationEmail($access_data['accountId']);

    $auth = new auth($dbo);
    $access_data = $auth->create($access_data['accountId'], CLIENT_ID);

    if ($access_data['error'] === false) {
        $account = new account($dbo, $access_data['accountId']);
        $access_data['account'] = array($account->get());
        $access_data['config'] = array($account->getConfigs());
        $access_data['email_verified'] = false;
        $access_data['verification'] = 'Verification email sent. Please check your inbox.';
        jsonResponse($access_data, 201);
    }
}

jsonResponse($access_data, 400);
