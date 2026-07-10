<?php
$pagename = 'admin-profile';
	$container = 'settings';
	
	include_once("inc/admin.inc.php");
	
	$data = false;
	
	if(!empty($_POST) && !APP_DEMO){
		
		$old_pass = $_POST['old_pass'];
		$new_pass = $_POST['new_pass'];
		$cnf_pass = $_POST['cnf_pass'];
		
		$data = true;
		
		$settings = new settings($dbo);
		$acid = admin::getAdminID();
		
		$result = $settings->changepass($acid, $old_pass, $new_pass, $cnf_pass);
		
		if($result == 420){
			
			$error = true;
			$error_message = "Admin Not Found";
			
		}elseif($result == 422){
			
			$error = true;
			$error_message = "New Password & Confirm Password do not Match";
			
		}elseif($result == 425){
			
			$error = true;
			$error_message = "Incorrect Old Password";
			
		}elseif($result == 424){
			
			$error = true;
			$error_message = "There was some issue changing the password";
			
		}elseif($result == 1){
			
			$error = false;
			$error_message = "Password Changed Successfully";
			
		}
		
	}
	
	$acid = admin::getAdminID();
	$configs->updateConfigs(time(),'LAST_ADMIN_ACCESS');

include_once 'inc/admin_header.php';
?>
<div class="admin-content">
        <!--Main Content-->
        <div class="content sm-gutter">
            <div class="container-fluid padding-25 sm-padding-10">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title">
                            <h4>Admin Profile</h4>
                        </div>
                    </div>
					<?php if(APP_DEMO) { include_once 'inc/demo-notice.php'; } ?>
					
					<!-- START MAIN CONTENT HERE -->
					
					<div class="col-md-4">
                        <div class="block mb-4" style="box-shadow: 0 7px 15px var(--primary-alpha-Dot25); transition: all 0.3s;">
							<div class="user-profile-menu bg-white">
								<div class="avatar-info">
									<img class="profile-img rounded-circle" id="adminImage" align="middle" src="images/<?php echo htmlspecialchars($configs->getConfig('ADMIN_IMAGE'), ENT_QUOTES, 'UTF-8'); ?>" alt="profile image" style="width: 168px; height: 168px;" />
									<h4 class="name"><?php echo htmlspecialchars($helper->getAdminFullName($acid), ENT_QUOTES, 'UTF-8'); ?></h4>
									<p class="designation">Admin</p>
								</div>
							</div>
							
						</div>
					</div>
					
                    <div class="col-md-8">
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5>Admin Details</h5>
                            </div>

                            <form action="process/profile.php" method="post" enctype="multipart/form-data" class="horizontal-form"/>
							<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Admin Name</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="admin_name" placeholder="Full name" value="<?php echo htmlspecialchars($helper->getAdminFullName($acid), ENT_QUOTES, 'UTF-8'); ?>" type="text" autocomplete="off" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Admin Image</label>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <input id="admin_image_name" class="form-control" type="text" name="admin_image_name" value="<?php echo htmlspecialchars($configs->getConfig('ADMIN_IMAGE'), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Choose Image" style="background: #e9ecef; " autocomplete="off" disabled/>
												<span class="input-group-addon text-dark"><label for="file-upload" class="custom-file-upload"><i class="ion-ios-folder"></i><span>Change Image</span></label>
													<input id="file-upload" onchange="readURL(this);" name="admin_image" accept="image/png, image/jpeg, image/jpg" type="file"/>
												</span>
											</div>
                                        </div>
                                    </div>
                                </div>

                                <hr />
                                <button class="btn btn-primary mr-0 pull-right" type="submit" value="upload">Update Details</button>
								<br><br>
                            </form>
                        </div>
						
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5>Change Password</h5>
                            </div>
							
							<?php if ($data){ ?>
						
								<div class="alert <?php if($error){ echo "alert-danger"; }else{ echo "alert-success"; } ?>">
									<?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
								</div>
							
							<?php } ?>

                            <form action="" method="post" />
                                
                                <div class="form-group">
                                    <label>Old Password</label>
                                    <input class="form-control" placeholder="Old Password" type="password" name="old_pass" required=""/>
                                </div>
								
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>New Password</label>
                                        <input class="form-control" placeholder="New Password" type="password" name="new_pass" required=""/>
                                    </div>
									
                                    <div class="form-group col-md-6">
                                        <label>Confirm New Password</label>
                                        <input class="form-control" placeholder="Confirm New password" type="password" name="cnf_pass" required=""/>
                                    </div>
									
                                </div>

                                <hr />
                                <button class="btn btn-primary mr-0 pull-right" type="submit">Change Password</button>
								<br><br>
                            </form>
                        </div>
                    </div>
					
					
					<!-- END MAIN CONTENT HERE -->
					<?php include_once 'inc/support.php'; ?>
					
                </div>
            </div>
        </div>
</div><!-- /admin-content -->
<?php include_once 'inc/admin_footer.php'; ?>
<script type="text/javascript">

function readURL(input) {
	
	if (input.files && input.files[0]) {
		
		var reader = new FileReader();
		
		reader.onload = function (e) {
			$('#adminImage')
				.attr('src', e.target.result)
				.width(168)
				.height(168);
			};
		reader.readAsDataURL(input.files[0]);
		$('#admin_image_name').val(input.files[0].name);
		$('#admin_image_name').prop('disabled', false);
	}
}

</script>
