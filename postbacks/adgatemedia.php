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
	
	// http://yoururl.com/postbacks/adgatemedia.php?tx_id={transaction_id}&user_id={s2}&point_value={points}&usd_value={payout}&offer_title={vc_title}
	
    $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';
    $amount = isset($_REQUEST['point_value']) ? $_REQUEST['point_value'] : 0;
    
    $timeCurrent = time();
    
    $configs = new functions($dbo);
    
    $type = "AdGateMedia offerwall Credit";
    
    // Checking Remote Ip
    if($configs->isWhitelisted($_SERVER["REMOTE_ADDR"])){
        
        $account = new account($dbo, 1);
        $userdata = $account->getuserdata($user_id);
        $userdata = array_merge(['username' => '', 'points' => 0, 'gcm' => ''], is_array($userdata) ? $userdata : []);
            
        if($userdata['username'] != $user_id){ api::printError(ERROR_UNKNOWN, "Account Mismatch"); }else{
                
            $configs->creditUserPoints($user_id, $amount, $type, 'You earned '.$amount.' points from '.$type);
                
            echo "1";
            
        }
        
		
	// Unknown Ip
	}else{ api::printError(ERROR_UNKNOWN, "Unknown Error"); }

?>