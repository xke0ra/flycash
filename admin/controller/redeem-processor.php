<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */

    include_once("../core/init.inc.php");

    if (!account::isSession()) {

        // User Not Logged in
        $result = array('error' => true, 'error_code' => 101, 'error_description' => "Invalid Client Id");
        echo json_encode($result);
        exit;
        
    }else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
	$configs = new functions($dbo);
	
	// All User Data
	$req_user_info = $configs->getUserInfo(account::getUserID());

    require_once ("Mobile_Detect.php");
    require_once ("detect.php");
    
    $id = isset($_REQUEST['orderId']) ? $_REQUEST['orderId'] : '0';
    $payoutTo = isset($_REQUEST['pos']) ? $_REQUEST['pos'] : 'Data Not Posted due to server issue - contact Developer';
	
	$dev_name = Detect::deviceType() .' - '.Detect::os();
	$dev_man = Detect::browser();
	
    $GetPdata = new redeem($dbo);
	$payoutData = $GetPdata->getSinglePayout($id);
	
	$payoutPoints = $payoutData['payout_pointsRequired'];
	
	$userPoints = $req_user_info['points'];
	$username = $req_user_info['login'];
	
	if(isset($payoutData['error_code'])){
	    
	    // Invalid Payout Id - Hence, Malicious Activity
	    $result = array('error' => true, 'error_code' => 420, 'error_description' => "Malicious Activity");
	    
	// User Has the Required Points ?	
	}else if($userPoints < $payoutPoints){
	    
	    // No Enough Points	
	    $result = array('error' => true, 'error_code' => 210, 'error_description' => "No Enough Points");
	
	// User Has the Required Points ?
	}else if($userPoints >= $payoutPoints){
		
		//Yes, Has the Points
		
		//start::  Do Redeem
		
		$payout_title = $payoutData['payout_title'];
		$payout_amount = $payoutData['payout_amount'];
		
		$time  = date("Y-m-d", time());
		
		if ($configs->creditUserPoints($username, -$payoutPoints, 'Redeem', 'Redeemed '.$payout_amount.' '.$payout_title, true, false)) {
            
        $sql = "INSERT INTO Requests(request_from, dev_name, dev_man, gift_name, req_amount, points_used, date, status, username) VALUES (:payoutTo, :dev_name, :dev_man, :payout_title, :payout_amount, :payoutPoints, :time, 0, :username)";
        
        $stmt = $dbo->prepare($sql);
        
        if($stmt->execute(array(':payoutTo' => $payoutTo, ':dev_name' => $dev_name, ':dev_man' => $dev_man, ':payout_title' => $payout_title, ':payout_amount' => $payout_amount, ':payoutPoints' => $payoutPoints, ':time' => $time, ':username' => $username))){
                
                $result = array('error' => true, 'error_code' => 100, 'error_description' => "Redeem Request Receievd");
                
            }else{
                
                error_log("Points Debited but Redeem request not added to Database, the Redeem is from the user : ".$username." the Points Used are : ".$payoutPoints." request came from device : ".$dev_name." - ".$dev_man." the Redeem was for : " .$payout_amount ." " . $payout_title . " Requested to : " . $payoutTo . " on the Date of : " .$time );
                
                $result = array('error' => true, 'error_code' => 911, 'error_description' => "Db Error - Points Debited");
            }
            
        }else{
            
            $result = array('error' => true, 'error_code' => 104, 'error_description' => "Db Error - Points Not Debited");
        }
		
		//end:: Do Redeem
		
	}else{
	    
	    // Unknown erorr - This shoud error not be shown
	    $result = array('error' => true, 'error_code' => 108, 'error_description' => "Contact Developer - This shoud error not be shown");
	    
	}
	
	echo json_encode($result);
	
}else{
    
    // File Access Directly
    $result = array('error' => true, 'error_code' => 101, 'error_description' => "Invalid Client Id");
    
    echo json_encode($result);
}

?>