<?php
include_once("../core/init.inc.php");

    if (!admin::isSession()) {

        header("Location: ../index.php");
		exit;
    }else if(!empty($_POST) && !APP_DEMO){
		
		if (!helper::verifyCsrfToken($_POST['csrf_token'] ?? '')) { header("Location: ../index.php"); exit; }
		
		$id = isset($_POST['id']) ? $_POST['id'] : "";
		$user = isset($_POST['user']) ? $_POST['user'] : "";
		$points_to_add = isset($_POST['points_to_add']) ? $_POST['points_to_add'] : "";
		$reason_for_adding_points = isset($_POST['reason_for_adding_points']) ? $_POST['reason_for_adding_points'] : "";
		
		$result = false;
		$timeCurrent = time();
		$_SESSION["points_added"] = 2;
		
		if($user != null){
		    
            $accountId = helper::clearInt($id);
    
            $account = new account($dbo, $accountId);
            $accountInfo = $account->get();
            $notify = new functions($dbo);
            
            if($accountInfo['username'] == $user){
                
                $result = $notify->creditUserPoints($user, $points_to_add, $reason_for_adding_points, 'Admin added '.$points_to_add.' points - '.$reason_for_adding_points, true, true);
                $notify->logAudit(admin::getAdminID(), admin::getAdminUsername(), 'add_points', $user, $points_to_add.' points for '.$reason_for_adding_points);
                
                if($result){
                    $_SESSION["points_added"] = 1;
                }
            }
		}
            
		if($result){
			
        header("Location: ../user-details.php?id=1");
		exit;
		}else{
			
        header("Location: ../user-details.php?id=1");
		exit;
		}
		
	}else{
		
		header("Location: ../users.php");
		exit;
	}
?>
