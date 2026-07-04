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
    }else if(isset($_GET['type']) && isset($_GET['action']) && !APP_DEMO){
		
		$type = $_GET['type'];
		$status = $_GET['action'];
		$configs = new functions($dbo);
		
        $sql = "UPDATE offerwalls SET status = :status WHERE type = :type";
        $stmt = $dbo->prepare($sql);
        
        $result = $stmt->execute(array(':status' => $status, ':type' => $type));
        
        if($type === 'checkin'){
		    $result = $configs->updateConfigs($status, 'DAILY_ACTIVE');
        }else if($type === 'admantum'){
		    $result = $configs->updateConfigs($status, 'AdMantumActive');
        }else if($type === 'adgatemedia'){
		    $result = $configs->updateConfigs($status, 'AdGateMediaActive');
        }else if($type === 'adscendmedia'){
		    $result = $configs->updateConfigs($status, 'AdScendMediaActive');
        }else if($type === 'cpalead'){
		    $result = $configs->updateConfigs($status, 'CpaLeadActive');
        }else if($type === 'wannads'){
		    $result = $configs->updateConfigs($status, 'WannadsActive');
        }else if($type === 'kiwiwall'){
		    $result = $configs->updateConfigs($status, 'KiwiWallActive');
        }else if($type === 'adgem'){
		    $result = $configs->updateConfigs($status, 'AdGemActive');
        }else if($type === 'offertoro'){
		    $result = $configs->updateConfigs($status, 'OfferToroActive');
        }
        
        
        if($result){
		    $configs->logAudit(admin::getAdminID(), admin::getAdminUsername(), 'update_offerwall_status', 'Offerwall '.$type, (($status == 1) ? 'Enabled' : 'Disabled').' offerwall type: '.$type);
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