<?php
include_once("../api.inc.php");

if (!empty($_POST)) {
    
    $data = $_POST['data'];
    
    $json = json_decode($data, true);

    $clientId = isset($json['clientId']) ? $json['clientId'] : 0;

    $accountId = isset($json['accountId']) ? $json['accountId'] : '';
    $accessToken = isset($json['accessToken']) ? $json['accessToken'] : '';
    
    $user = isset($json['user']) ? $json['user'] : '11';
    
    $clientId = helper::clearInt($clientId);
    $accountId = helper::clearInt($accountId);
    
    $accessToken = helper::clearText($accessToken);
    $accessToken = helper::escapeText($accessToken);
    
    $result = array("error" => true);
    $auth = new auth($dbo);
    
    if(!isset($json['name'])){
        
        api::printError(ERROR_UNKNOWN, "Error on Redeem DATA");
        
    }else if(!isset($json['value'])) {

        api::printError(ERROR_UNKNOWN, "Error on Redeem Request DATA");
        
    }else if($clientId != CLIENT_ID) {

        api::printError(ERROR_UNKNOWN, "Error client Id.");
        
    }else if(!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization");
    }
    
    $payoutId = $json['name'];
    $payoutTo = $json['value'];
    
    $dev_name = $json['dev_name'];
    $dev_man = $json['dev_man'];
    
    $redeem = new redeem($dbo);
    $payoutdata = $redeem->getSinglePayout($payoutId);

    $account = new account($dbo, $accountId);
    $userdata = $account->get();
    $notify = new functions($dbo);
    
    if($payoutdata['payout_id'] != $payoutId){

        api::printError(ERROR_ACCESS_TOKEN, "Invalid Redeem Request DATA");
        
    }else if($userdata['username'] != $user){
        
        api::printError(ERROR_UNKNOWN, "Account Mismatch");
    
    }else if($payoutdata['payout_status'] !== 'Active'){
        
        api::printError(ERROR_UNKNOWN, "This reward is currently disabled");
        
    }else if($userdata['points'] < $payoutdata['payout_pointsRequired']){
        
        api::printError(420, "No Enough Balance");
        
    }else{
        
        // THE START
        
        $payout_title = $payoutdata['payout_title'];
        $payout_amount = $payoutdata['payout_amount'];
        $payout_pointsRequired = $payoutdata['payout_pointsRequired'];
        
        try {
            $dbo->beginTransaction();
            
            $stmt = $dbo->prepare("SELECT points FROM users WHERE id = :id LIMIT 1 FOR UPDATE");
            $stmt->execute([':id' => $accountId]);
            $userRow = $stmt->fetch();
            
            if (!$userRow || (int)$userRow['points'] < $payout_pointsRequired) {
                $dbo->rollBack();
                api::printError(420, "No Enough Balance");
            }
            
            $st = $dbo->prepare("UPDATE users SET points = points - :delta WHERE id = :id AND points - :delta >= 0");
            $st->execute([':delta' => $payout_pointsRequired, ':id' => $accountId]);
            
            if ($st->rowCount() === 0) {
                $dbo->rollBack();
                api::printError(420, "No Enough Balance");
            }
            
            $timeCurrent = time();
            $trackerSt = $dbo->prepare("INSERT INTO tracker (user_id, username, points, type, date) VALUES (:uid, :user, :points, :type, :time)");
            $trackerSt->execute([':uid' => $accountId, ':user' => $user, ':points' => -$payout_pointsRequired, ':type' => 'Redeem', ':time' => $timeCurrent]);
            
            $time = date("Y-m-d", $timeCurrent);
            $reqSt = $dbo->prepare("INSERT INTO redemptions(request_from, dev_name, dev_man, gift_name, req_amount, points_used, date, status, user_id, username) VALUES (:payoutTo, :dev_name, :dev_man, :payout_title, :payout_amount, :payout_pointsRequired, :time, 'pending', :uid, :user)");
            $reqSt->execute([':payoutTo' => $payoutTo, ':dev_name' => $dev_name, ':dev_man' => $dev_man, ':payout_title' => $payout_title, ':payout_amount' => $payout_amount, ':payout_pointsRequired' => $payout_pointsRequired, ':time' => $time, ':uid' => $accountId, ':user' => $user]);
            
            $dbo->commit();
            
            $result = array("error" => false, "error_code" => ERROR_SUCCESS, "response_title" => "Redeem Success", "response_message" => "Redem All Good");
            
        } catch (\Exception $e) {
            $dbo->rollBack();
            \FlyCash\Logger::error("Redeem transaction failed (v3)", ['user' => $user, 'payout' => $payoutId, 'error' => $e->getMessage()]);
            api::printError(ERROR_UNKNOWN, "Server Error");
        }
        
        // THE END
    }

    echo json_encode($result);
    exit;
}
