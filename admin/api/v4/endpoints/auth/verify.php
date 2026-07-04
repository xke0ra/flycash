<?php

if ($method !== 'GET' && $method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

if ($method === 'GET') {
    $hash = isset($_GET['hash']) ? $_GET['hash'] : '';
    if (empty($hash)) {
        jsonError(ERROR_UNKNOWN, "Missing verification hash.", 400);
    }
    $account = new account($dbo);
    $result = $account->verifyEmail($hash);
    if ($result['error'] === false) {
        $webRoot = $api->getConfig('WEB_ROOT');
        header("Location: " . $webRoot . "?verified=1");
        exit;
    }
    echo "<html><body><h3>" . htmlspecialchars($result['error_description']) . "</h3></body></html>";
    exit;
}

validateClient($data);
requireParams(array('accountId', 'accessToken'), $data);
$authData = authorizeRequest($data, $dbo);

$account = new account($dbo, $authData['accountId']);
$userdata = $account->get();

if (intval($userdata['email_verified']) === 1) {
    jsonError(ERROR_UNKNOWN, "Email already verified.", 400);
}

$result = $account->sendVerificationEmail($authData['accountId']);

if ($result['error'] === false) {
    jsonResponse(array(
        "error" => false,
        "error_code" => ERROR_SUCCESS,
        "error_description" => "Verification email sent."
    ), 200);
}

jsonResponse($result, 400);
