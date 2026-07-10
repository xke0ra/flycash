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

if ($payoutdata['payout_status'] !== 'Active') {
    jsonError(ERROR_UNKNOWN, "This reward is currently disabled", 400);
}

$payout_title = $payoutdata['payout_title'];
$payout_amount = $payoutdata['payout_amount'];
$payout_pointsRequired = $payoutdata['payout_pointsRequired'];

try {
    $dbo->beginTransaction();

    $accountId = $authData['accountId'];

    $stmt = $dbo->prepare("SELECT points FROM users WHERE id = :id LIMIT 1 FOR UPDATE");
    $stmt->execute([':id' => $accountId]);
    $userRow = $stmt->fetch();

    if (!$userRow || (int)$userRow['points'] < $payout_pointsRequired) {
        $dbo->rollBack();
        jsonError(420, "No Enough Balance", 400);
    }

    $st = $dbo->prepare("UPDATE users SET points = points - :delta WHERE id = :id AND points - :delta >= 0");
    $st->execute([':delta' => $payout_pointsRequired, ':id' => $accountId]);

    if ($st->rowCount() === 0) {
        $dbo->rollBack();
        jsonError(420, "No Enough Balance", 400);
    }

    $timeCurrent = time();
    $trackerSt = $dbo->prepare("INSERT INTO tracker (user_id, username, points, type, date) VALUES (:uid, :user, :points, :type, :time)");
    $trackerSt->execute([':uid' => $accountId, ':user' => $user, ':points' => -$payout_pointsRequired, ':type' => 'Redeem', ':time' => $timeCurrent]);

    $time = date("Y-m-d", $timeCurrent);
    $reqSt = $dbo->prepare("INSERT INTO redemptions(request_from, dev_name, dev_man, gift_name, req_amount, points_used, date, status, user_id, username) VALUES (:payoutTo, :dev_name, :dev_man, :payout_title, :payout_amount, :payout_pointsRequired, :time, 'pending', :uid, :user)");
    $reqSt->execute([':payoutTo' => $payoutTo, ':dev_name' => $dev_name, ':dev_man' => $dev_man, ':payout_title' => $payout_title, ':payout_amount' => $payout_amount, ':payout_pointsRequired' => $payout_pointsRequired, ':time' => $time, ':uid' => $accountId, ':user' => $user]);

    $dbo->commit();

    jsonResponse(array(
        "error" => false,
        "error_code" => ERROR_SUCCESS,
        "response_title" => "Redeem Success",
        "response_message" => "Redeem All Good"
    ), 201);

} catch (\Exception $e) {
    $dbo->rollBack();
    \FlyCash\Logger::error("Redeem transaction failed", ['user' => $user, 'payout' => $payoutId, 'error' => $e->getMessage()]);
    jsonError(ERROR_UNKNOWN, "Server Error", 500);
}
