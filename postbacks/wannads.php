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
	
	// http://yoururl.com/postbacks/wannads.php

    $user_id = isset($_GET['subId']) ? $_GET['subId'] : "null";
    $amount = isset($_GET['reward']) ? $_GET['reward'] : 0;
    $amount = intval($amount); // just pre-rounding the amount for safety
    
    // IMP Parameter
    $status = isset($_GET['status']) ? $_GET['status'] : 0;
    
    if($status != 1){
        
        // No Need to credit user Here
        echo "OK";
        exit;
    }
    
    $timeCurrent = time();
    
    $configs = new functions($dbo);
    
    $type = "Wannads offerwall Credit";
        
        $account = new account($dbo, 1);
        $userdata = $account->getuserdata($user_id);
            
        if($userdata['username'] != $user_id){ api::printError(ERROR_UNKNOWN, "Account Mismatch"); }else{
                
            $configs->creditUserPoints($user_id, $amount, $type, 'You earned '.$amount.' points from '.$type);
            
            echo "OK";
            
        }
        

?>