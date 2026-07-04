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
    }else if(!empty($_GET) && !APP_DEMO){
		
		$ID = isset($_GET['id']) ? $_GET['id'] : "";
		$type = isset($_GET['type']) ? $_GET['type'] : "";
		
		$requests = new requests($dbo);
		
		if($type === "process"){
		    
		    $requests->ProcessingRequest($ID);
		    $configs->logAudit(admin::getAdminID(), admin::getAdminUsername(), 'process_request', 'Request #'.$ID, 'Processed request ID '.$ID);
		    
		}
		
		header("Location: ../rejected-requests.php");
		exit();
		
		
    }elseif(!empty($_POST) && !APP_DEMO){
        
		if (!helper::verifyCsrfToken($_POST['csrf_token'] ?? '')) { header("Location: ../index.php"); exit; }
        
		$ID = isset($_POST['id']) ? $_POST['id'] : "";
		$type = isset($_POST['type']) ? $_POST['type'] : "";
		$note = isset($_POST['note']) ? $_POST['note'] : "";
		
		$requests = new requests($dbo);
		$result = false;
		
		if($type === "complete"){
			
			$result = $requests->CompleteRequest($ID, $note);
		    if($result){
		        $configs->logAudit(admin::getAdminID(), admin::getAdminUsername(), 'complete_request', 'Request #'.$ID, 'Completed request ID '.$ID);
		    }
		}else if($type === "process"){
			
			$result = $requests->ProcessingRequest($ID, $note);
		    if($result){
		        $configs->logAudit(admin::getAdminID(), admin::getAdminUsername(), 'process_request', 'Request #'.$ID, 'Processed request ID '.$ID);
		    }
		}else if($type === "reject"){
			
			$result = $requests->RejectRequest($ID, $note);
		    if($result){
		        $configs->logAudit(admin::getAdminID(), admin::getAdminUsername(), 'reject_request', 'Request #'.$ID, 'Rejected request ID '.$ID);
		    }
		}
	    
	}
	
	header("Location: ../requests.php");
	exit;
?>