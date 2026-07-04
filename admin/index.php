<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */

    include_once("core/init.inc.php");

	if (admin::isSession()) {

		header("Location: admin.php");
		
	}else{
	    
		header("Location: login.php");
	}
	
	
	?>