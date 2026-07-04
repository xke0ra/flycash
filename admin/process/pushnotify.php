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
    }else if(!empty($_POST)){
		
		if (!helper::verifyCsrfToken($_POST['csrf_token'] ?? '')) { header("Location: ../index.php"); exit; }
		
		$fcm_id = isset($_POST['fcm']) ? $_POST['fcm'] : '0';
		$title = $_POST['title'];
		$message = $_POST['msg'];
		$image = $_POST['img'];
		$type = "none";
		
		$notify = new functions($dbo);
		
		$result = $notify->sendPush($fcm_id, $title, $message, $image, $type);
		
		if($result){
			
			header("Location: ../push.php");
		exit;
		}else{
			
			header("Location: ../push.php");
		exit;
		}
		
	}else{
		
		header("Location: ../index.php");
		exit;
	}
	
	
	

?>