<?php

if ($method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

requireParams(array('name', 'value', 'user', 'dev_name', 'dev_man'), $data);
validateClient($data);
$authData = authorizeRequest($data, $dbo);

$payoutId = intval($data['name']);
$payoutTo = $data['value'];
$dev_name = $data['dev_name'];
$dev_man = $data['dev_man'];
$user = helper::clearText($data['user']);

$redeem = new redeem($dbo);
$payoutdata = $redeem->getSinglePayout($payoutId);

$account = new account($dbo, $authData['accountId']);
$userdata = $account->get();

if ($payoutdata['payout_id'] != $payoutId) {
    jsonError(ERROR_ACCESS_TOKEN, "Invalid Redeem Request DATA", 400);
}

if ($userdata['username'] != $user) {
    jsonError(ERROR_UNKNOWN, "Account Mismatch", 403);
}

if ($userdata['points'] < $payoutdata['payout_pointsRequired']) {
    jsonError(420, "No Enough Balance", 400);
}

$payout_title = $payoutdata['payout_title'];
$payout_amount = $payoutdata['payout_amount'];
$payout_pointsRequired = $payoutdata['payout_pointsRequired'];
$time = date("Y-m-d", time());

if ($api->creditUserPoints($user, -$payout_pointsRequired, 'Redeem', 'Redeemed '.$payout_amount.' '.$payout_title)) {
    $sql = "INSERT INTO Requests(request_from, dev_name, dev_man, gift_name, req_amount, points_used, date, status, username) VALUES (:payoutTo, :dev_name, :dev_man, :payout_title, :payout_amount, :payout_pointsRequired, :time, 0, :user)";
    $stmt = $dbo->prepare($sql);
    if ($stmt->execute(array(':payoutTo' => $payoutTo, ':dev_name' => $dev_name, ':dev_man' => $dev_man, ':payout_title' => $payout_title, ':payout_amount' => $payout_amount, ':payout_pointsRequired' => $payout_pointsRequired, ':time' => $time, ':user' => $user))) {
        jsonResponse(array(
            "error" => false,
            "error_code" => ERROR_SUCCESS,
            "response_title" => "Redeem Success",
            "response_message" => "Redeem All Good"
        ), 201);
    } else {
        error_log("Points Debited but Redeem request not added to DB: user=$user points=$payout_pointsRequired payout=$payout_amount $payout_title to=$payoutTo");
        jsonError(ERROR_UNKNOWN, "Server Error", 500);
    }
}

jsonError(ERROR_UNKNOWN, "Server Error", 500);
