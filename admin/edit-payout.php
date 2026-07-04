<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */

	$pagename = 'payouts';
	$container = 'payouts';
	
	include_once("inc/admin.inc.php");
	$valid = false;

    if(isset($_GET['id'])){
		
		$ID = (int)$_GET['id'];
		$configs = new functions($dbo);
        $payout = new redeem($dbo);
        $result = $payout->getSinglePayout($ID);
        
        if(isset($result['payout_id'])){
            
            $valid = true;
        }
        
    }
    
    if(!$valid){
        
        header("Location: payouts.php");
        
    }
    
include_once 'inc/admin_header.php';
?>
<div class="admin-content">
        <!--Main Content-->
        <div class="content sm-gutter">
            <div class="container-fluid padding-25 sm-padding-10">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title">
                            <h4>Edit Redeem option Details</h4>
                        </div>
                    </div>
					<?php if(APP_DEMO) { include_once 'inc/demo-notice.php'; } ?>
					
					<!-- START MAIN CONTENT HERE -->
					
                    <div class="col-md-12">
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5>Edit Redeem</h5>
                            </div>

                            <form action="process/edit-payout.php" method="post" enctype="multipart/form-data" class="horizontal-form">
							<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Redeem Name</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="payout_id" type="text" value="<?php echo (int)$result['payout_id']; ?>" hidden/>
                                            <input class="form-control" name="payout_name" placeholder="Paypal" type="text" value="<?php echo htmlspecialchars($result['payout_title'], ENT_QUOTES, 'UTF-8'); ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Redeem SubTitle</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="payout_sub" placeholder="1000 Points = $1 USD" type="text" value="<?php echo htmlspecialchars($result['payout_subtitle'], ENT_QUOTES, 'UTF-8'); ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Redeem Image</label>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <input id="payout_image_name" class="form-control" type="text" name="payout_image_name" value="<?php echo htmlspecialchars(basename($result['payout_thumbnail']), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Choose Image" style="background: #e9ecef; " autocomplete="off" disabled/>
												<span class="input-group-addon text-dark"><label for="file-upload" class="custom-file-upload"><i class="ion-ios-folder"></i><span>Change Image</span></label>
													<input id="file-upload" onchange="readURL(this);" name="payout_image" accept="image/png, image/jpeg, image/jpg" type="file"/>
												</span>
											</div>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Redeem Amount</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="payout_amount" type="text" value="<?php echo htmlspecialchars($result['payout_amount'], ENT_QUOTES, 'UTF-8'); ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Points Require to Redeem</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="payout_points" type="number" placeholder="1000" value="<?php echo (int)$result['payout_pointsRequired']; ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Message to user</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="payout_msg" type="text" value="<?php echo htmlspecialchars($result['payout_message'], ENT_QUOTES, 'UTF-8'); ?>" required=""/>
                                        </div>
                                    </div>
                                </div>

                                <hr />

                                <button class="btn btn-primary mr-3 pull-right" type="submit">Save Settings</button>
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
		
		reader.readAsDataURL(input.files[0]);
		$('#payout_image_name').val(input.files[0].name);
		$('#payout_image_name').prop('disabled', false);
	}
}

</script>
