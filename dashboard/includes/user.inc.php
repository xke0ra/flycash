<?php
include_once("../admin/core/init.inc.php");

    if (!account::isSession()) {

        header("Location: login.php");
        exit;
    }
    
	$configs = new functions($dbo);
	
	// All User Data
	$req_user_info = $configs->getUserInfo(account::getUserID());
	$user_username = $req_user_info['login'];
	
	$configs->updateUserAccess(account::getUserID());
	
	$APP_NAME = $configs->getConfig('APP_NAME');
	$APP_DESC = $configs->getConfig('APP_DESC');
	
?>