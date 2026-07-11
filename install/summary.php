<?php
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>FLY CASH - Admin Panel Installation</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="AYM.com">
		<link rel="stylesheet" href="fonts/material-design-iconic-font/css/material-design-iconic-font.css">
		
        <link rel="stylesheet" href="../admin/assets/css/bootstrap.min.css" />
        <link rel="stylesheet" href="css/custom.css" />
        
        <script type="text/javascript" src="../admin/assets/js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="../admin/assets/js/bootstrap.min.js"></script>
    
		<!-- STYLE CSS -->
		<link rel="stylesheet" href="css/style.css">
	</head>
	<body style="background: #fff;">
	
		<p style="font-size: 30px; font-weight: 200; text-align: center; margin-top: 2%;">FLY CASH</p>
		<p style="font-size: 16px; font-weight: 200; text-align: center;">Admin Panel Installation</p>
		<div class="custom">
		    
			<?php 
			
			if(isset($_GET['error'])) { ?>
			    
			    <div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex; margin-top: 8%;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>
			<?php
				echo '<p style="font-size: 30px; font-weight: 200; text-align: center; color: #f44336; margin-top: 2%;">INSTALLATION FAILED</p>';
				if($_GET['error'] == 1 ) { echo '<p style="font-size: 16px; font-weight: 200; text-align: center; color: #f44336">Could not connect to the database. Please check the connection details and try again.</p>'; }
				if($_GET['error'] == 2 ) { echo '<p style="font-size: 16px; font-weight: 200; text-align: center; color: #f44336">Cannot edit config.php file. Please check the location, permissions or chmod permissions on your webserver.</p>'; }
				if($_GET['error'] == 3 ) { echo '<p style="font-size: 16px; font-weight: 200; text-align: center; color: #f44336">Cannot process or access the pocket_db.sql file.</p>'; }
			} else if (isset($_GET['success'])) { ?>
			    <div class="swal2-icon swal2-success swal2-animate-success-icon" style="display: flex; margin-top: 8%;">
			        <div class="swal2-success-circular-line-left" style="background-color: rgb(255, 255, 255);"></div>
			        <span class="swal2-success-line-tip"></span>
			        <span class="swal2-success-line-long"></span>
			        <div class="swal2-success-ring"></div>
			        <div class="swal2-success-fix" style="background-color: rgb(255, 255, 255);"></div>
			        <div class="swal2-success-circular-line-right" style="background-color: rgb(255, 255, 255);"></div>
			        </div>
			<?php
				echo '<p style="font-size: 30px; font-weight: 200; text-align: center; color: #5fca37; margin-top: 2%;">INSTALLATION SUCCESS</p>';
				echo '<p style="font-size: 16px; font-weight: 200; text-align: center; margin-top: 1%;">Congratulations, you\'ve successfully installed the WebPanel. <br><br> Delete the install folder and Click <a href="../admin/login.php">here</a> to Login to your admin panel.</p>';
			}else{?>
			    <div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex; margin-top: 8%;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>
			<?php
				echo '<p style="font-size: 30px; font-weight: 200; text-align: center; color: #f44336; margin-top: 2%;">INSTALLATION FAILED</p>';
				echo '<p style="font-size: 16px; font-weight: 200; text-align: center; color: #f44336">Please check the all the details and try again.</p>';
				header("Location: index.php");
				exit;
			}
			?>
		
			<p style="text-align: center; margin-top: 10%;">
			Need help ? please Open a Ticket in our <a href="https://www.aym.com/support" target="_blank">Support forum</a> by providing your Purchase Details.<br><br>
			<a href="https://www.aym.com/support" target="_blank">www.aym.com/support</a>
			</p>
			
		</div>

		<script src="js/jquery-3.3.1.min.js"></script>
		
</body>
</html>
