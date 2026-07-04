<?php
	 /*
	 *  AddOn Name      :   AdGem Offerwall
	 *  AddOn URL       :   https://www.aym.com/item/adgem-offerwall/
	 *  AddOn License   :   https://www.aym.com/licenses/
	 *
	 *  This Code is a part of Premium AddOn. Do not Share this code.
	 * 
	 *  ALL RIGHTS RESERVED
	 *
	 *  http://www.aym.com
	 *  support@aym.com
	 *
	 *  Copyright 2020 AYM ( http://www.aym.com )
	 *
	 */
    
    // URL : https://your-domain.com/postbacks/adgem.php?user_id={player_id}&amount={amount}
    
    include_once("../admin/core/init.inc.php");
	 
	$network_name = "AdGem";
    $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : 'none';
    $points = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : 0;
 
    
    $account = new account($dbo);
    $userdata = $account->getuserdata($user_id);
    $user_name_from_database = isset($userdata['username']) ? $userdata['username'] : "none";
    
    if($user_name_from_database === $user_id){
        
        // Valid User - Reward the user here
        $points = intval($points);
        $type = $network_name." offerwall Credit";
        
        $configs = new functions($dbo);
        $configs->creditUserPoints($user_name_from_database, $points, $type, 'You earned '.$points.' points from '.$type, true, false);
        
        // All Good - Successfully Rewarded
        echo "OK - Postback Success";
        exit();
        
    }
    
    echo "NOT OK - Postback Failed";
    
?>
