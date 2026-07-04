<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */
	 
	include_once("../../core/init.inc.php");
	$configs = new functions($dbo);

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $configs->getConfig('APP_NAME'); ?> - <?php echo $configs->getConfig('APP_DESC'); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="AYM.com">
		
        <link rel="stylesheet" href="../../assets/css/bootstrap.min.css" />
        <link rel="stylesheet" href="css/custom.css" />
        
        <script type="text/javascript" src="../../assets/js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="../../assets/js/bootstrap.min.js"></script>
    
		<!-- STYLE CSS -->
		<link rel="stylesheet" href="css/style.css">
	</head>
	<body style="background: #fff;">
	
		<p style="font-size: 30px; font-weight: 200; text-align: center; margin-top: 2%;"><?php echo $configs->getConfig('APP_NAME'); ?></p>
		<p style="font-size: 16px; font-weight: 200; text-align: center;"><?php echo $configs->getConfig('APP_DESC'); ?></p>
		
		<div class="custom">
			<div class="swal2-icon swal2-success swal2-animate-success-icon" style="display: flex; margin-top: 8%;">
				<div class="swal2-success-circular-line-left" style="background-color: rgb(255, 255, 255);"></div>
				<span class="swal2-success-line-tip"></span>
				<span class="swal2-success-line-long"></span>
				<div class="swal2-success-ring"></div>
				<div class="swal2-success-fix" style="background-color: rgb(255, 255, 255);"></div>
				<div class="swal2-success-circular-line-right" style="background-color: rgb(255, 255, 255);"></div>
			</div>
			
			<p style="font-size: 30px; font-weight: 200; text-align: center; color: #5fca37; margin-top: 2%;">Password Changed</p>
			<p style="font-size: 16px; font-weight: 200; text-align: center; margin-top: 1%;">Congratulations, you've successfully changed your password.</p>
		</div>

		<script src="js/jquery-3.3.1.min.js"></script>
		
</body>
</html>