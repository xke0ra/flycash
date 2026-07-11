<?php
include_once("../admin/core/config.php");
	
	if($INSTALL_STATUS == "SUCCESS"){
		header("Location: summary.php?success=1");
		exit;
	}

	$constants_file = '../admin/core/config.php';
    $errors = 0;
    $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'].= $_SERVER['REQUEST_URI'];

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>FLY CASH - Admin Panel Installation</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="AYM.com">

		<!-- MATERIAL DESIGN ICONIC FONT -->
		<link rel="stylesheet" href="fonts/material-design-iconic-font/css/material-design-iconic-font.css">

		<!-- STYLE CSS -->
		<link rel="stylesheet" href="css/style.css">
	</head>
	<body>
		<p style="font-size: 30px; font-weight: 200; text-align: center; margin-top: 2%;">FLY CASH</p>
		<p style="font-size: 16px; font-weight: 200; text-align: center;">Admin Panel Installation</p>
		<div class="wrapper">
		
			<div class="image-holder">
				<img src="images/earn.jpg" alt="">
			</div>
			
            <form action="process.php" method="POST" id="insform">
            	<div id="wizard">
            		<!-- SECTION 1 -->
	                <h4></h4>
	                <section>
	                    <div class="form-row form-group">
	                    	<div class="form-holder">
								PHP version : 
	                    	</div>
	                    	<div class="form-holder">
								<?php
                                    if (floatval(phpversion()) < 5.1) {
                                        echo '<span class = "failure" style="color: #f44336;">' . floatval(phpversion()) . ' - The script will not work unless you update your PHP version.</span>';
                                        $errors = 1;
                                    } else {
                                        echo '<span class = "success" style="color: #5fca37;">' . floatval(phpversion()) . ' - OK</span>';
                                    }
                                    ?>
	                    	</div>
	                    </div>
						
	                    <div class="form-row form-group">
	                    	<div class="form-holder">
								PDO Enabled : 
	                    	</div>
	                    	<div class="form-holder">
								<?php
                                    if (class_exists('PDO')) {
                                        echo '<span class = "success" style="color: #5fca37;">Yes - OK </span>';
                                    } else {
                                        echo '<span class = "failure" style="color: #f44336;">No - Check the instructions or forum on how to enable this. </span>';
										$errors++;
                                    }
                                    ?>
	                    	</div>
	                    </div>
						
	                    <div class="form-row form-group">
	                    	<div class="form-holder">
								Config File Writable : 
	                    	</div>
	                    	<div class="form-holder">
								<?php
                                    if (is_writable($constants_file)) {
                                        echo '<span class = "success" style="color: #5fca37;">Yes - OK</span>';
                                    } else {
                                        echo '<span class = "failure" style="color: #f44336;">No - The file is not writable!</span>';
										$errors++;
                                    }
                                    ?>
	                    	</div>
	                    </div>
						
						<br>
	                    <div class="form-row form-group">
	                    	<div class="form-holder">
								<?php
                                    if ($errors > 0) {
                                        echo '<span class = "failure" style="color: #f44336;">Please Fix the Above issues to continue installation</span>';
                                    } else {
                                        echo 'All Good ! you can Continue to install the Script';
										$errors++;
                                    }
                                    ?>
	                    	</div>
	                    </div>
						
	                </section>
	                
					<!-- SECTION 2 -->
	                <h4></h4>
	                <section>
					
	                	<div class="form-row">
	                    	<label for="">
	                    		Database Host *
	                    	</label>
	                    	<input type="text" name="host" placeholder="localhost " class="form-control required" required>
	                    </div>
						
	                	<div class="form-row">
	                    	<label for="">
	                    		Database Name *
	                    	</label>
	                    	<input type="text" name="dbname" placeholder="Database Name " class="form-control">
	                    </div>
						
	                	<div class="form-row">
	                    	<label for="">
	                    		Database Username *
	                    	</label>
	                    	<input type="text" name="dbuser" placeholder="Database Username " class="form-control">
	                    </div>
						
	                	<div class="form-row">
	                    	<label for="">
	                    		Database Password *
	                    	</label>
	                    	<input type="text" name="dbpass" placeholder="Database Password " class="form-control">
	                    </div>
						
	                </section>

	                <!-- SECTION 3 -->
	                <h4></h4>
	                <section>
	                    <div class="form-row form-group">
	                    	<div class="form-holder">
	                    		<label for="">
	                    		First Name *
		                    	</label>
	                    		<input type="text" name="fname" placeholder="First Name" class="form-control">
	                    	</div>
	                    	<div class="form-holder">
	                    		<label for="">
	                    		Last Name *
		                    	</label>
	                    		<input type="text" name="lname" placeholder="Last Name " class="form-control">
	                    	</div>
	                    </div>	
						
	                    <div class="form-row">
	                    	<label for="admin_username" >
	                    		UserName
	                    	</label>
	                    	<input type="text" name="uname" placeholder="UserName" id="admin_username" class="form-control" >
	                    </div>
						
	                    <div class="form-row">
	                    	<label for="">
	                    		Password
	                    	</label>
	                    	<input type="text" name="upass" placeholder="Password" class="form-control">
	                    </div>
	                </section>
					<input type="hidden" name="installsbt" value="summary" />  
	            </div>
            </form>
			
		</div>

		<script src="js/jquery-3.3.1.min.js"></script>
		<script src="js/jquery.steps.js"></script>

		<script src="js/main.js"></script>
		
</body>
</html>
