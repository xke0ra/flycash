<?php

    /*!
	 * POCKET v3.7
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */
	
	include_once("../admin/core/init.inc.php");
	
	//  http://yoursite.com/postbacks/kiwiwall.php
	
    $user_id = isset($_REQUEST['sub_id']) ? $_REQUEST['sub_id'] : '';
    $status = isset($_REQUEST['status']) ? $_REQUEST['status'] : 0;
    
    $amount = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : 0;
    $amount = intval($amount); // pre-rounding the amount for safety
    
    $timeCurrent = time();
    
    $configs = new functions($dbo);
    
    $type = "KiwiWall offerwall Credit";
    
    // Checking Remote Ip
    if($configs->isWhitelisted($_SERVER["REMOTE_ADDR"])){
        
        if($status == 1){
            
            $account = new account($dbo, 0);
            $userdata = $account->getuserdata($user_id);
            $userdata = array_merge(['username' => '', 'points' => 0, 'gcm' => ''], is_array($userdata) ? $userdata : []);
                
            if($userdata['username'] != $user_id){ api::printError(ERROR_UNKNOWN, "Account Mismatch"); }else{
                    
                $configs->creditUserPoints($user_id, $amount, $type, 'You earned '.$amount.' points from '.$type);
                
                echo "1";
                
            }
            
        }else{ echo "1"; }
        
	// Unknown Ip
	}else{ api::printError(ERROR_UNKNOWN, "Unknown Error"); }

?>