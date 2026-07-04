<?php

if ($method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

validateClient($data);
$authData = authorizeRequest($data, $dbo);
$username = isset($data['user']) ? helper::clearText($data['user']) : '';

$amount = isset($data['name']) ? intval($data['name']) : 0;
$type = isset($data['value']) ? $data['value'] : '0';

if ($amount > 5) {
    jsonError(ERROR_ACCESS_TOKEN, "Invalid amount.", 400);
}

$account = new account($dbo, $authData['accountId']);
$userdata = $account->get();

if (!empty($username) && $userdata['username'] !== $username) {
    jsonError(ERROR_UNKNOWN, "Account Mismatch", 403);
}

$spinRewardTitle = $api->getConfig('SPIN_REWARD_TITLE');

$stmt = $dbo->prepare("SELECT * FROM tracker WHERE username = :user AND type = :title ORDER BY id DESC LIMIT 1");
$stmt->execute(array(':user' => $userdata['username'], ':title' => $spinRewardTitle));

$rewardUser = true;
if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch();
    $timeCalculated = intval($row['date']) + 86400;
    if ($timeCalculated > time()) {
        jsonError(410, $timeCalculated - time(), 429);
    }
}

if ($api->creditUserPoints($userdata['username'], $amount, $spinRewardTitle)) {
    jsonResponse(array(
        "error" => false,
        "error_code" => ERROR_SUCCESS,
        "response_title" => "Spin Success",
        "response_message" => "Spin Points Credited"
    ), 200);
}

jsonError(ERROR_UNKNOWN, "Server Error", 500);
