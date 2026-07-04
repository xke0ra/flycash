<?php

    /*!
	 * POCKET v3.5
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */
	
	include_once("../admin/core/init.inc.php");
	
	//  http://yoursite.com/postbacks/cpalead.php?subid={subid}&subid2={subid2}&virtual_currency={virtual_currency}
	
    $user_id = isset($_REQUEST['subid2']) ? $_REQUEST['subid2'] : '';
    $amount = isset($_REQUEST['virtual_currency']) ? $_REQUEST['virtual_currency'] : 0;
    $amount = intval($amount); // pre-rounding the amount for safety
    
    $timeCurrent = time();
    
    $configs = new functions($dbo);
    
    $type = "CpaLead offerwall Credit";
    
    // Checking Remote Ip
    if($configs->isWhitelisted($_SERVER["REMOTE_ADDR"])){
        
        $account = new account($dbo, 0);
        $userdata = $account->getuserdata($user_id);
        
        $userdata = array_merge([
            'username' => '',
            'points' => 0,
            'gcm' => '',
        ], is_array($userdata) ? $userdata : []);
            
        if($userdata['username'] != $user_id){ api::printError(ERROR_UNKNOWN, "Account Mismatch"); }else{
                
            $configs->creditUserPoints($user_id, $amount, $type, 'You earned '.$amount.' points from '.$type);
            
            echo "PostBack Success";
            
        }
        
	// Unknown Ip
	}else{ api::printError(ERROR_UNKNOWN, "Unknown Error"); }

?>