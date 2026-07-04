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
	
	//  http://yoursite.com/postbacks/admantum.php?user_id={uid}&amount={virtual_currency}
	
    $click_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';
    $amount = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : '';
    $timeCurrent = time();
    
    $configs = new functions($dbo);
    
    $type = "AdMantum offerwall Credit";
    
    // if it is a Web Rewards User
    $account = new account($dbo);
    $userdata = $account->getuserdata($click_id);
    
    $user_id = isset($userdata['username']) ? $userdata['username'] : "none";
    
    if($click_id === $user_id){
        
        // Web Rewards User
        $configs->creditUserPoints($user_id, $amount, $type, 'You earned '.$amount.' points from '.$type, true, false);
        
        // All Good - Successfully Rewarded
        header("HTTP/1.1 200");
        
        echo "OK - Postback Success";
        
        
    }else{
        
        // Android App User
        $offerData = $configs->getofferStatusData($click_id);
        
        $offerClickId = isset($offerData['cid']) ? $offerData['cid'] : '';
        $offerStatus = isset($offerData['status']) ? $offerData['status'] : '';
        $offerUser = isset($offerData['user']) ? $offerData['user'] : '';
        
        if($offerClickId == $click_id && $offerStatus == 0){
            
            $user_id = $offerUser;
            $userdata = $account->getuserdata($user_id);
            
            $checkusername = isset($userdata['username']) ? $userdata['username'] : "none";
            
            if($checkusername != $user_id){
                
                // No User exists
                header("HTTP/1.1 400");
                
                api::printError(ERROR_UNKNOWN, "Account Mismatch");
                
            }else{
                
                $offerCompleted = $configs->completeofferStatusData($click_id);
                $configs->creditUserPoints($user_id, $amount, $type, 'You earned '.$amount.' points from '.$type);
                
                // All Good - Successfully Rewarded
                header("HTTP/1.1 200");
                
                echo "Postback Success";
            }
            
        }else if($offerStatus == 1){
            
            // Offer Rewarded Already
            header("HTTP/1.1 200");
            
            echo "OK - Postback Success";
            
        }else{
            
            // No such Offer Exists
            header("HTTP/1.1 400");
            
            api::printError(ERROR_UNKNOWN, "Unknown Offer Error");
            
        }
        
    }
    
?>