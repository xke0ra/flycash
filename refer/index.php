<?php

    /*!
	 * POCKET v3.7
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */
	 
	 include_once("../admin/core/init.inc.php");
	 
	 $_SESSION["refererCode"] = isset($_REQUEST['refer']) ? $_REQUEST['refer']: '';
	 
	 
	 header("Location: ../dashboard/register.php");
	 exit;

?>