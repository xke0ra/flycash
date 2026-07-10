<?php
include_once("../admin/core/init.inc.php");
	 
	 $_SESSION["refererCode"] = isset($_REQUEST['refer']) ? $_REQUEST['refer']: '';
	 
	 
	 header("Location: ../dashboard/register.php");
	 exit;

?>