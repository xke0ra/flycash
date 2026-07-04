<?php

    /*!
	 * POCKET v3.5
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */
	
	include_once("../admin/core/init.inc.php");
	
	// http://yoursite.com/postbacks/adscendmedia.php?offerid=[OID]&name=[ONM]&rate=[CUR]&sub1=[SB1]
	
	$user_id = isset($_REQUEST['sub1']) ? $_REQUEST['sub1'] : '';
	$amount = isset($_REQUEST['rate']) ? $_REQUEST['rate'] : 0;
    $timeCurrent = time();
    
    $configs = new functions($dbo);
    
    $type = "AdScendMedia offerwall Credit";
    
    // Checking Remote Ip
    if($configs->isWhitelisted($_SERVER["REMOTE_ADDR"])){
		
        if($amount < 1000){
            
            $account = new account($dbo, 1);
            $userdata = $account->getuserdata($user_id);
            $userdata = array_merge(['username' => '', 'points' => 0, 'gcm' => ''], is_array($userdata) ? $userdata : []);
            
            if($userdata['username'] != $user_id){ api::printError(ERROR_UNKNOWN, "Account Mismatch"); }else{
                
                $configs->creditUserPoints($user_id, $amount, $type, 'You earned '.$amount.' points from '.$type);
                
                echo "success";
            
            }
            
        }else{ api::printError(ERROR_UNKNOWN, "Unknown Data Error"); }
        
	// Unknown Ip
	}else{ api::printError(ERROR_UNKNOWN, "Unknown Error"); }
	
?>