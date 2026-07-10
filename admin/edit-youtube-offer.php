<?php
$pagename = 'edit-youtube-offer';
	$container = 'youtube-offers';
	
	include_once("inc/admin.inc.php");
	$valid = false;

    if(isset($_GET['id'])){
		
		$ID = (int)$_GET['id'];
		$configs = new functions($dbo);
        $offerwalls = new offerwalls($dbo);
        $result = $offerwalls->getSingleYoutubeOffer($ID);
        
        if(isset($result['offer_id'])){
            
            $valid = true;
        }
        
    }
    
    if(!$valid){
        
        header("Location: youtube-offers.php");
        
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
                            <h4>Edit Youtube Offer option Details</h4>
                        </div>
                    </div>
					<?php if(APP_DEMO) { include_once 'inc/demo-notice.php'; } ?>
					
					<!-- START MAIN CONTENT HERE -->
					
                    <div class="col-md-12">
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5><?php if($valid){ echo htmlspecialchars($result['offer_title'], ENT_QUOTES, 'UTF-8'); } ?> Configuration</h5>
                            </div>

                            <form action="process/edit-youtube-offer.php" method="post" enctype="multipart/form-data" class="horizontal-form">
							<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Title</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="id" type="text" value="<?php if($valid){ echo (int)$result['offer_id']; } ?>" hidden/>
                                            <input class="form-control" name="title" type="text" value="<?php if($valid){ echo htmlspecialchars($result['offer_title'], ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Sub Title</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="sub" type="text" value="<?php if($valid){ echo htmlspecialchars($result['offer_subtitle'], ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>

	                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Duration In Minutes</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="duration" min="1" step="1" max="10000000" type="number" placeholder="Watch duration"  value="<?php if($valid){ echo (int)$result['offer_duration']; } ?>"  required=""/>
                                        </div>
                                    </div>
                                </div>
							
	                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Points</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="points" min="1" step="1" max="10000000" type="number" placeholder="User Earn Points" value="<?php if($valid){ echo (int)$result['offer_points']; } ?>"  required=""/> 
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Image</label>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <input id="image_name" class="form-control" type="text" name="image_name" value="<?php if($valid){ echo htmlspecialchars(basename($result['offer_thumbnail']), ENT_QUOTES, 'UTF-8'); } ?>" placeholder="Choose Image" style="background: #e9ecef; " autocomplete="off" disabled/>
												<span class="input-group-addon text-dark"><label for="file-upload" class="custom-file-upload"><i class="ion-ios-folder"></i><span>Change Image</span></label>
													<input id="file-upload" onchange="readURL(this);" name="offer_image" accept="image/png, image/jpeg, image/jpg" type="file"/>
												</span>
											</div>
                                        </div>
                                    </div>
                                </div>
                                

                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Youtue URL</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val1" placeholder="https://yotube.com/?v=123" type="text" value="<?php if($valid){ echo htmlspecialchars($result['offer_url'], ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
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
