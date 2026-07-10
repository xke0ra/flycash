<?php

if ($method !== 'GET' && $method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

$emailService = \FlyCash\Container::get(\FlyCash\Services\EmailVerificationService::class);

if ($method === 'GET') {
    $hash = isset($_GET['hash']) ? $_GET['hash'] : '';
    if (empty($hash)) {
        jsonError(ERROR_UNKNOWN, "Missing verification hash.", 400);
    }
    $verified = $emailService->verifyEmail($hash);
    if ($verified) {
        $webRoot = $api->getConfig('WEB_ROOT');
        header("Location: " . $webRoot . "?verified=1");
        exit;
    }
    echo "<html><body><h3>Verification failed. Link may be expired or invalid.</h3></body></html>";
    exit;
}

validateClient($data);
requireParams(array('accountId', 'accessToken'), $data);
$authData = authorizeRequest($data, $dbo);

if ($emailService->isEmailVerified($authData['accountId'])) {
    jsonError(ERROR_UNKNOWN, "Email already verified.", 400);
}

$sent = $emailService->sendVerificationEmail($authData['accountId']);

if ($sent) {
    jsonResponse(array(
        "error" => false,
        "error_code" => ERROR_SUCCESS,
        "error_description" => "Verification email sent."
    ), 200);
}

jsonError(ERROR_UNKNOWN, "Failed to send verification email.", 400);
