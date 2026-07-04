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
    $amount = isset($json['name']) ? $json['name'] : '0';
    $type = isset($json['value']) ? $json['value'] : '0';
    
    $clientId = helper::clearInt($clientId);
    $accountId = helper::clearInt($accountId);
    
    $accessToken = helper::clearText($accessToken);
    $accessToken = helper::escapeText($accessToken);
    
    $result = array("error" => true);
    $auth = new auth($dbo);
    
    if($clientId != CLIENT_ID) {

        api::printError(ERROR_UNKNOWN, "Error client Id.");
        
    }else if(!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
        
    }else if($amount > 5) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $account = new account($dbo, $accountId);
    $notify = new functions($dbo);
    $userdata = $account->get();
    $timeCurrent = time();
    $spinReward = $amount;
    $spinRewardTitle = $notify->getConfig('SPIN_REWARD_TITLE');
    
    $rewardUser = false;
    
    if($userdata['username'] != $user){
        
        api::printError(ERROR_UNKNOWN, "Account Mismatch");
    
    }
    
    $sql = "SELECT * FROM tracker WHERE username = :user AND type = :spinRewardTitle ORDER BY id DESC LIMIT 1";
    $stmt = $dbo->prepare($sql);
    $stmt->execute(array(':user' => $user, ':spinRewardTitle' => $spinRewardTitle));
    
    if ($stmt->rowCount() > 0) {
        
        // old User
        
        $row = $stmt->fetch();
        
        // "1539068949"
        $timeData = $row['date'];
        
        $timeCalculated = $timeData + 24 * 3600;
        
        $diff = $timeCalculated - $timeCurrent;
        
        if($timeCalculated > $timeCurrent){
            
            $rewardUser = false;
            api::printError(410, $diff);
            
        }else{
            
            $rewardUser = true;
        }
        
    }else{
        
        // New User
        $rewardUser = true;
        
    }
    
    if($rewardUser){
        
        if ($notify->creditUserPoints($user, $spinReward, $spinRewardTitle)) {
        
        $result = array("error" => false, "error_code" => ERROR_SUCCESS, "response_title" => "Daily Checkin Success", "response_message" => "Daily Checkin Points Credited");
        
        }else{ api::printError(ERROR_UNKNOWN, "Server Error"); }
        
    }else{
        
        api::printError(ERROR_UNKNOWN, "Server Error");
        
    }

    echo json_encode($result);
    exit;
}
