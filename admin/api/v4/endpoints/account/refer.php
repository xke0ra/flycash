<?php

if ($method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

requireParams(array('name', 'value', 'user'), $data);
validateClient($data);
$authData = authorizeRequest($data, $dbo);

$refererCode = $data['value'];
$user = helper::clearText($data['user']);

$account = new account($dbo, $authData['accountId']);
$userdata = $account->get();
$referdata = $account->getreferer($refererCode);
$userOldReferData = $account->getOldRefersData($user);

$refererCodefromreferData = isset($referdata['refer']) ? $referdata['refer'] : '11';
$checkusername = isset($userOldReferData['username']) ? $userOldReferData['username'] : "none";

if ($userdata['username'] != $user) {
    jsonError(ERROR_UNKNOWN, "Account Mismatch", 403);
}

if ($userdata['refered'] == 1) {
    jsonError(400, "Referral Bonus Received Already", 400);
}

if ($checkusername == $user) {
    $oldrefererCode = $userOldReferData['referer'];
    $sql = "UPDATE users SET referer = :oldrefererCode, refered = '1' WHERE login = :user";
    $stmt = $dbo->prepare($sql);
    $stmt->execute(array(':oldrefererCode' => $oldrefererCode, ':user' => $user));
    jsonError(400, "Referral Bonus Received Already", 400);
}

if ($refererCodefromreferData != $refererCode) {
    jsonError(404, "Invalid Refer Code", 404);
}

if ($userdata['username'] == $referdata['username']) {
    jsonError(420, "Self Refer Not Allowed", 400);
}

$referReward = $api->getConfig('REFER_REWARD');
$referBonusTitle = $api->getConfig('REFERAL_BONUS_TITLE');
$refererBonusTitle = $api->getConfig('REFERER_BONUS_TITLE');
$rererUserName = $referdata['username'];

$api->creditUserPoints($user, $referReward, $referBonusTitle);

$sql = "UPDATE users SET referer = :refererCode, refered = '1' WHERE login = :user";
$stmt = $dbo->prepare($sql);
$stmt->execute(array(':refererCode' => $refererCode, ':user' => $user));

$api->creditUserPoints($rererUserName, $referReward, $refererBonusTitle);

jsonResponse(array(
    "error" => false,
    "error_code" => ERROR_SUCCESS,
    "response_title" => "Refer Success",
    "response_message" => "Refer All Good"
), 201);
