<?php

if ($method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

validateClient($data);
$authData = authorizeRequest($data, $dbo);
$username = isset($data['user']) ? helper::clearText($data['user']) : '';

$account = new account($dbo, $authData['accountId']);
$userdata = $account->get();

if (!empty($username) && $userdata['username'] !== $username) {
    jsonError(ERROR_UNKNOWN, "Account Mismatch", 403);
}

$limit = isset($data['limit']) ? intval($data['limit']) : 20;
$offset = isset($data['offset']) ? intval($data['offset']) : 0;
$accountId = $authData['accountId'];

$stmt = $dbo->prepare("SELECT * FROM tracker WHERE user_id = :uid AND type != '0' ORDER BY id DESC LIMIT " . intval($limit) . " OFFSET " . intval($offset));
$stmt->execute(array(':uid' => $accountId));

$transactions = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $transactions[] = $row;
}

jsonResponse(array(
    "error" => false,
    "error_code" => ERROR_SUCCESS,
    "transactions" => $transactions
), 200);
