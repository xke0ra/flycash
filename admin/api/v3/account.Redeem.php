<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */

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
    
    }else if($userdata['points'] < $payoutdata['payout_pointsRequired']){
        
        api::printError(420, "No Enough Balance");
        
    }else{
        
        // THE START
        
        $payout_title = $payoutdata['payout_title'];
        $payout_amount = $payoutdata['payout_amount'];
        $payout_pointsRequired = $payoutdata['payout_pointsRequired'];
        
        $time  = date("Y-m-d", time());
        
        if ($notify->creditUserPoints($user, -$payout_pointsRequired, 'Redeem', 'Redeemed '.$payout_amount.' '.$payout_title)) {
            
            $sql = "INSERT INTO Requests(request_from, dev_name, dev_man, gift_name, req_amount, points_used, date, status, username) VALUES (:payoutTo, :dev_name, :dev_man, :payout_title, :payout_amount, :payout_pointsRequired, :time, 0, :user)";
            $stmt = $dbo->prepare($sql);
            
            if($stmt->execute(array(':payoutTo' => $payoutTo, ':dev_name' => $dev_name, ':dev_man' => $dev_man, ':payout_title' => $payout_title, ':payout_amount' => $payout_amount, ':payout_pointsRequired' => $payout_pointsRequired, ':time' => $time, ':user' => $user))){
                
                $result = array("error" => false, "error_code" => ERROR_SUCCESS, "response_title" => "Redeem Success", "response_message" => "Redem All Good");
                
            }else{
                
                error_log("Points Debited but Redeem request not added to Database, the Redeem is from the user : ".$user." the Points Used are : ".$payout_pointsRequired." request came from device : ".$dev_name." the Redeem was for : " .$payout_amount ." " . $payout_title . " Requested to : " . $payoutTo . " on the Date of : " .$time );
                
                api::printError(ERROR_UNKNOWN, "Sever Error");
            }
            
        }else{
            
            api::printError(ERROR_UNKNOWN, "Sever Error");
        }
        
        // THE END
    }

    echo json_encode($result);
    exit;
}
