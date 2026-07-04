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

    if (!admin::isSession()) {

        header("Location: ../index.php");
		exit;
    }else if(!empty($_POST) && !APP_DEMO){
		
		if (!helper::verifyCsrfToken($_POST['csrf_token'] ?? '')) { header("Location: ../index.php"); exit; }
		
		$ID = $_POST['id'];
		$name = $_POST['name'];
		$subtitle = $_POST['sub'];
		$position = isset($_POST['position']) ? $_POST['position'] : 100;
		
		$image_name = isset($_POST['image_name']) ? $_POST['image_name'] : $ID."_offerwall_image.png";
		$type = $_POST['type'];
		$points = isset($_POST['points']) ? $_POST['points'] : 0;
		$val1 = isset($_POST['val1']) ? $_POST['val1'] : "0000";
		$val2 = isset($_POST['val2']) ? $_POST['val2'] : "0000";
		$val3 = isset($_POST['val3']) ? $_POST['val3'] : "0000";
		
		$result = false;
		$offerwall_url = '';
		$configs = new functions($dbo);
		
		if(strpos($type, 'custom_offerwall_') !== false){
		    
		    // CUSTOM OFFERWALL
		    $offerwall_url = $val1;
		
		}else if($type == "checkin"){
		    
		    $result = $configs->updateConfigs($points, 'DAILY_REWARD');
		    
		}else if($type == "spin"){
		    
		    $result = $configs->updateConfigs($name, 'SPIN_TITLE');
		    $result = $configs->updateConfigs($val1, 'SPIN_REWARD_TITLE');
		    
		}else if($type == "refer"){
		    
		    $result = $configs->updateConfigs($points, 'REFER_REWARD');
		    
		}else if($type == "admantum"){
		    
		    $result = $configs->updateConfigs($val1, 'AdMantum_PubId');
		    $result = $configs->updateConfigs($val2, 'AdMantum_AppId');
		    $result = $configs->updateConfigs($val3, 'AdMantum_SecretKey');
		    
		}else if($type == "adgatemedia"){
		    
		    $result = $configs->updateConfigs($val1, 'AdGate_Media_WallId');
		    
		}else if($type == "fyber"){
		    
		    $result = $configs->updateConfigs($val1, 'Fyber_AppId');
		    $result = $configs->updateConfigs($val2, 'Fyber_SecurityToken');
		    
		}else if($type == "adscendmedia"){
		    
		    $result = $configs->updateConfigs($val1, 'AdScendMedia_PubId');
		    $result = $configs->updateConfigs($val2, 'AdScendMedia_AdwallId');
		    
		}else if($type == "cpalead"){
		    
		    $result = $configs->updateConfigs($val1, 'CpaLead_DirectLink');
		    
		}else if($type == "wannads"){
		    
		    $result = $configs->updateConfigs($val1, 'WannadsApiKey');
		    
		}else if($type == "kiwiwall"){
		    
		    $result = $configs->updateConfigs($val1, 'KiwiWall_WallId');
		    $result = $configs->updateConfigs($val2, 'KiwiWall_APIKEY');
		    $result = $configs->updateConfigs($val3, 'KiwiWall_SECKEY');
		    
		}else if($type == "adgem"){
		    
		    $result = $configs->updateConfigs($val1, 'AdGem_AppId');
		    $result = $configs->updateConfigs($val2, 'AdGem_ApiKey');
		    
		    $offerwall_url = "https://api.adgem.com/v1/wall?appid=".$val1."&playerid={user_id}";
		    
		}else if($type == "offertoro"){
		    
		    $result = $configs->updateConfigs($val1, 'OfferToro_PubId');
		    $result = $configs->updateConfigs($val2, 'OfferToro_AppId');
		    $result = $configs->updateConfigs($val3, 'OfferToro_SecretKey');
		    
		    $offerwall_url = "https://www.offertoro.com/ifr/show/".$val1."/{user_id}/".$val2;
		}
		
		require_once  "../core/class.imagehelper.php";
		
		$image = new Imagehelper\Image($_FILES);
			
		if($image["offer_image"]){
				
			$image->setSize(0, 5000000);
			$image->setMime(array('png', 'jpg','jpeg'));
			$image->setLocation("../images");
			
				$imageName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $image_name);
				$image->setName($imageName);
			
			$upload = $image->upload();
			
			if($upload){
				
				// update with image
				
				$urlfinal = $image->getName().'.'.$image->getMime();
    		    
    		    $sql = "UPDATE offerwalls SET name = :name, subtitle = :subtitle, url = :offerwall_url, position = :position, points = :points, image = :urlfinal WHERE id = :ID";
    			$stmt = $dbo->prepare($sql);
    			if($stmt->execute(array(':name' => $name, ':subtitle' => $subtitle, ':offerwall_url' => $offerwall_url, ':position' => $position, ':points' => $points, ':urlfinal' => $urlfinal, ':ID' => $ID))){ $result = true; }
				
			}else{
				
				 //echo $image->getError();
				 
				 // update without image
		    
    		    $sql = "UPDATE offerwalls SET name = :name, subtitle = :subtitle, url = :offerwall_url, position = :position, points = :points WHERE id = :ID";
    			$stmt = $dbo->prepare($sql);
    			
    			if($stmt->execute(array(':name' => $name, ':subtitle' => $subtitle, ':offerwall_url' => $offerwall_url, ':position' => $position, ':points' => $points, ':ID' => $ID))){ $result = true; }
				
			}
		}
		
		if($result){
			
			header("Location: ../offerwalls.php");
		exit;
		}else{
			
			header("Location: ../offerwalls.php");
		exit;
		}
		
		
	}else{
		
		header("Location: ../offerwalls.php");
		exit;
	}
	
	
	

?>