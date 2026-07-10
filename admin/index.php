<?php
include_once("core/init.inc.php");

	if (admin::isSession()) {

		header("Location: admin.php");
		
	}else{
	    
		header("Location: login.php");
	}
	
	
	?>