<?php

if ($method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

requireParams(array('email'), $data);
validateClient($data);

$email = helper::escapeText(helper::clearText($data['email']));
$result = $api->sendPasswordResetEmail($email, CLIENT_ID);

if ($result['error'] === false) {
    jsonResponse($result, 200);
}

jsonResponse($result, 400);
