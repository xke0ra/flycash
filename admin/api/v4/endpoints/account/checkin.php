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

$checkinReward = $api->getConfig('DAILY_REWARD');
$checkinBonusTitle = $api->getConfig('CHECKIN_BONUS_TITLE');

$stmt = $dbo->prepare("SELECT * FROM tracker WHERE username = :user AND type = :title ORDER BY id DESC LIMIT 1");
$stmt->execute(array(':user' => $userdata['username'], ':title' => $checkinBonusTitle));

$rewardUser = true;
if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch();
    $timeCalculated = intval($row['date']) + 86400;
    if ($timeCalculated > time()) {
        jsonError(410, $timeCalculated - time(), 429);
    }
}

if ($api->creditUserPoints($userdata['username'], $checkinReward, $checkinBonusTitle)) {
    jsonResponse(array(
        "error" => false,
        "error_code" => ERROR_SUCCESS,
        "response_title" => "Daily Checkin Success",
        "response_message" => "Daily Checkin Points Credited"
    ), 200);
}

jsonError(ERROR_UNKNOWN, "Server Error", 500);
