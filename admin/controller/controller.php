<?php
// Install Handler
	if (file_exists('install')) {
		
		echo '<html lang="en"><head><title>FLY CASH - Rewards Application</title></head>';
		echo '<body style="color: #525252; sans-serif; font-size: 16px; -webkit-font-smoothing: antialiased; margin: 0">';
		echo '<div style="text-align: center;font-size: 56px; font-weight: 200; margin: 100px 0;">';
		echo '<p style="font-size: 56px; font-weight: 200;">FLY CASH - Rewards Application</p><br>';
		echo '<a href="install/" rel="nofollow" style="padding: 20px 20px; text-decoration: none; font-size: 14px; text-transform: uppercase;color: #fff; background: #1880c9;transition: all 0.3s;">Install</a>';
		echo '<p style="font-size: 20px;margin-top: 10%; line-height: 1.5;">Click Install to proceed to the Installer<br>or<br>Remove the install folder after installation and then refresh the page to continue.</p>'; 
		echo '<p style="font-size: 14px;margin-top: 8%;">&copy; <a href="http://www.aym.com/" target="_blank" style="text-decoration: none;color: inherit;">AYM</a>. All Rights Reserved.</p>';
		echo '</div>';
		echo '</body>';
		exit; 
		
	// Update Handler
	}else if(file_exists('update')){
		
		echo '<html lang="en"><head><title>FLY CASH - Rewards Application</title></head>';
		echo '<body style="color: #525252; sans-serif; font-size: 16px; -webkit-font-smoothing: antialiased; margin: 0">';
		echo '<div style="text-align: center;font-size: 56px; font-weight: 200; margin: 100px 0;">';
		echo '<p style="font-size: 56px; font-weight: 200;">FLY CASH - Rewards Application</p><br>';
		echo '<a href="update/" rel="nofollow" style="padding: 20px 20px; text-decoration: none; font-size: 14px; text-transform: uppercase;color: #fff; background: #1880c9;transition: all 0.3s;">Update</a>';
		echo '<p style="font-size: 20px;margin-top: 10%; line-height: 1.5;">Click Update to proceed to the script updation<br>or<br>Remove the update folder after updation and then refresh the page to continue.</p>'; 
		echo '<p style="font-size: 14px;margin-top: 8%;">&copy; <a href="https://www.aym.com/" target="_blank" style="text-decoration: none;color: inherit;">AYM</a>. All Rights Reserved.</p>';
		echo '</div>';
		echo '</body>';
		exit; 
	
	// Site Handler
	}else{
		
		include_once("admin/core/init.inc.php");
		
		// REDIRECT TO DASHBOARD IF LOGGED IN
		if (account::isSession()){
		    
		    //header("Location: dashboard/index.php");
		    
		}else if(admin::isSession()){

			//header("Location: admin/admin.php");
			
		}
		
		$configs = new functions($dbo);
	}
	
?>