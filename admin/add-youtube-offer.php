<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */

	$pagename = 'add-youtube-offer';
	$container = 'youtube-offers';
	
	include_once("inc/admin.inc.php");
	$valid = false;
    
include_once 'inc/admin_header.php';
?>
<div class="admin-content">
        <!--Main Content-->
        <div class="content sm-gutter">
            <div class="container-fluid padding-25 sm-padding-10">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title">
                            <h4>Youtube offers Details</h4>
                        </div>
                    </div>
					<?php if(APP_DEMO) { include_once 'inc/demo-notice.php'; } ?>
					
					<!-- START MAIN CONTENT HERE -->
					
                    <div class="col-md-12">
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5>Add youtube offer</h5>
                            </div>

                            <form action="process/add-youtube-offer.php" method="post" enctype="multipart/form-data" class="horizontal-form">
							<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Title</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="title" type="text" placeholder=" Youtube Offer  Title" value="" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Sub Title</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="sub" type="text" placeholder="Youtube Offer  Subtitle" value="" required=""/>
                                        </div>
                                    </div>
                                </div>
	                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Duration In Minutes</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="duration" min="1" step="1" max="10000000" type="number" placeholder="Watch duration" required=""/>
                                        </div>
                                    </div>
                                </div>
							
	                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Points</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="points" min="1" step="1" max="10000000" type="number" placeholder="User Earn Points" required=""/>
                                        </div>
                                    </div>
                                </div>
							
						
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Image</label>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <input id="image_name" class="form-control" type="text" name="image_name" value="" placeholder="Choose Image" style="background: #e9ecef; " autocomplete="off" disabled/>
												<span class="input-group-addon text-dark"><label for="file-upload" class="custom-file-upload"><i class="ion-ios-folder"></i><span>Change Image</span></label>
													<input id="file-upload" onchange="readURL(this);" name="offer_image" accept="image/png, image/jpeg, image/jpg" type="file"/>
												</span>
											</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Youtubeb URL</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="url" placeholder="https://youtube.com/?v=123456" type="text" value="" required=""/>
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
		$('#image_name').val(input.files[0].name);
		$('#image_name').prop('disabled', false);
	}
}

</script>
