<?php
    
    // Points Ratio
    $pointsRatio = 500; // $1 USD = 500 Points - so that admin can earn 50% commission 
    	
    // Some offers does not work but shows up in API, Here is the list of offers that do not work !
    $not_working_offers = array("29759", "28168", "28852", "26690", "26690", "29450", "29729", "29645", "29964", "23566", "empty", "empty", "empty", "empty");
    
    // https://mobverify.com/api/v1/?affiliateid=175344&ip=183.83.245.129&ctype=3&aff_sub3=yashDev
    $ogads_api_url = "https://mobverify.com/api/v1/?affiliateid=220779&ip=".$_SERVER['REMOTE_ADDR']."&ctype=3&aff_sub3=".$req_user_info['login'];
    
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $ogads_api_url);
    	curl_setopt($ch, CURLOPT_POST, false);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_NOBODY, 0);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    	$result = curl_exec($ch);
    	$curl_error = curl_errno($ch);
    	curl_close($ch);
    	
    	if($curl_error == '0'){
    		
    		$OgAds_offers = json_decode($result, true);
    		$OgAds_offers = $OgAds_offers['offers'];
    		
    	}
    

?>