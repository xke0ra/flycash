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
    $cid = isset($json['cid']) ? $json['cid'] : '00';
    $of_id = isset($json['of_id']) ? $json['of_id'] : '0';
    
    $of_title = isset($json['of_title']) ? $json['of_title'] : '00';
    $of_amount = isset($json['of_amount']) ? $json['of_amount'] : '00';
    $of_url = isset($json['of_url']) ? $json['of_url'] : '00';
    $partner = isset($json['partner']) ? $json['partner'] : '00';
    $dev_name = isset($json['dev_name']) ? $json['dev_name'] : '00';
    $dev_man = isset($json['dev_man']) ? $json['dev_man'] : '00';
    
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    
    $clientId = helper::clearInt($clientId);
    $accountId = helper::clearInt($accountId);
    
    $accessToken = helper::clearText($accessToken);
    $accessToken = helper::escapeText($accessToken);
    
    $of_title = helper::clearText($of_title);
    $of_title = helper::escapeText($of_title);
    
    $result = array("error" => true);
    $auth = new auth($dbo);
    
    if($clientId != CLIENT_ID) {

        api::printError(ERROR_UNKNOWN, "Error client Id.");
        
    }else if(!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $account = new account($dbo, $accountId);
    $userdata = $account->get();
    $offerdata = new offers($dbo);
    $offerdata = $offerdata->getStatus($cid,$of_id,$user);
    $date = time();
    
    $offerStatus = isset($offerdata['status']) ? $offerdata['status'] : '404';
    
    if($userdata['username'] != $user){
        
        api::printError(ERROR_UNKNOWN, "Account Mismatch");
    
    }else if($offerStatus == 0){
        
        api::printError(400, "Offer Pending");
        
    }else if($offerStatus == 1){
        
        api::printError(420, "Offer Completed");
        
    }else if($offerStatus == 2){
        
        api::printError(422, "Offer Processing");
        
    }else if($offerStatus == 3){
        
        api::printError(423, "Offer Rejected");
        
    }else if($offerStatus == 404){
        
        // Saving Offer Details
        $sql = "INSERT INTO offer_status(cid, user, of_id, of_title, of_amount, of_url, partner, ip_addr, dev_name, dev_man, date, status) values (:cid, :user, :of_id, :of_title, :of_amount, :of_url, :partner, :ip_addr, :dev_name, :dev_man, :date, '0')";
        $stmt = $dbo->prepare($sql);
        
        if($stmt->execute(array(':cid' => $cid, ':user' => $user, ':of_id' => $of_id, ':of_title' => $of_title, ':of_amount' => $of_amount, ':of_url' => $of_url, ':partner' => $partner, ':ip_addr' => $ip_addr, ':dev_name' => $dev_name, ':dev_man' => $dev_man, ':date' => $date))){
            
            $result = array("error" => false, "error_code" => ERROR_SUCCESS, "error_description" => "Offer Details Saved");
            
        }else{
            
            api::printError(ERROR_UNKNOWN, "Server Error");
            
        }
        
    }else{
        
        api::printError(ERROR_UNKNOWN, "UNKNOWN Status Error");
    }
    
    
    echo json_encode($result);
    exit;
}
