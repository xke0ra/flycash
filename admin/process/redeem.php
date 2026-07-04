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

    $configs = new functions($dbo);

    if (!admin::isSession()) {

        header("Location: ../index.php");
		exit;
    }else if(isset($_GET['id']) && isset($_GET['action']) && !APP_DEMO){
		
		$ID = $_GET['id'];
		$status = $_GET['action'];
		
        $sql = "UPDATE payouts SET status = :status WHERE id = :ID";
        $stmt = $dbo->prepare($sql);
        
        if($stmt->execute(array(':status' => $status, ':ID' => $ID))){
		    $configs->logAudit(admin::getAdminID(), admin::getAdminUsername(), 'update_payout_status', 'Payout #'.$ID, 'Updated payout ID '.$ID.' status to '.$status);
			header("Location: ../payouts.php");
		exit;
		}else{
			
			header("Location: ../payouts.php");
		exit;
		}
		
	}else{
		
		header("Location: ../payouts.php");
		exit;
	}
	
	
	

?>