<?php
$pagename = 'add-payout';
	$container = 'payouts';
	
	include_once("inc/admin.inc.php");

include_once 'inc/admin_header.php';
?>
<div class="admin-content">
        <!--Main Content-->
        <div class="content sm-gutter">
            <div class="container-fluid padding-25 sm-padding-10">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title">
                            <h4>Add New Redeem option</h4>
                        </div>
                    </div>
                    
					<?php if(APP_DEMO) { include_once 'inc/demo-notice.php'; } ?>
					
					<!-- START MAIN CONTENT HERE -->
					
                    <div class="col-12 col-sm-6 col-md-6 col-lg-8 mb-4 mb-lg-0">
                        
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5>New Redeem Details</h5>
                            </div>

                            <form action="process/add-redeem.php" method="post" enctype="multipart/form-data" class="horizontal-form">
							<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Redeem Name</label>
                                        <div class="col-md-9">
                                            <input class="form-control" onchange="changeName(this);" name="payout_name" id="payout_name" placeholder="Paypal" value="" type="text" autocomplete="off" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Redeem Subtitle</label>
                                        <div class="col-md-9">
                                            <input class="form-control" onchange="changeName(this);" id="payout_sub" name="payout_sub" placeholder="1000 Points = $1 USD" value="" type="text" autocomplete="off" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Redeem Image</label>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <input id="payout_image_name" class="form-control" type="text" name="payout_image_name" value="" placeholder="Choose Image" style="background: #e9ecef; cursor: pointer;" autocomplete="off" required="" />
												<span class="input-group-addon text-dark"><label for="file-upload" class="custom-file-upload"><i class="ion-ios-folder"></i><span>Choose Image</span></label>
													<input id="file-upload" onchange="readURL(this);" name="payout_image" accept="image/png, image/jpeg, image/jpg" type="file" required/>
												</span>
											</div>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Redeem Amount</label>
                                        <div class="col-md-9">
                                            <input class="form-control" id="payout_amount" onchange="changeName(this);"  name="payout_amount" placeholder="$1 USD" value="" type="text" autocomplete="off" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Points Require to Redeem</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="payout_points" placeholder="1000" value="" type="number" autocomplete="off" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Message to user </label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="payout_msg" placeholder="Enter your Email/Mobile :" value="Enter your Email Address :" type="text" autocomplete="off" required=""/>
                                        </div>
                                    </div>
                                </div>

                                <hr />
                                <button class="btn btn-primary mr-0 pull-right" type="submit" value="upload">Add Redeem</button>
								<br><br>
                            </form>
                        </div>
                    </div>
                        
                    <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-4 mb-lg-0">
						<div class="block task-block">
							<div class="section-title">
								<h5>New Redeem Preview</h5>
							</div>

							<ul id="inprogress">
							    
								<!-- New Redeem -->
								<li>
									<div class="task align-items-center" style="cursor: auto;">
										<div class="members single">
											<div class="member rounded-circle float-left" style=" border-radius: 0%; width: 60px; height: 60px;">
												<img id="newImage" class="img-fluid" src="assets/images/person-placeholder.png" />
											</div>
										</div>
										<div class="task-desc">
											<p id="newtitle" class="task-title text-truncate"> ------- </p>
											<span class="end-time text-truncate"><p id="newsub"> ---- ---- </p></span>
										</div>
										<div class="members single">
											<div class="float-right">
												<a href="#"><p id="newAmount" style="color: #1880c9; font-weight: 700;"> </p><a>
											</div>
										</div>
									</div>
								</li>
							    
							</ul>

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

function changeName(input) {
    var newtitle = document.getElementById('newtitle');
    var newsub = document.getElementById('newsub');
    var newAmount = document.getElementById('newAmount');
    var title = document.getElementById('payout_name');
    var sub = document.getElementById('payout_sub');
    var amount = document.getElementById('payout_amount');
    
    newtitle.textContent = title.value;
    newsub.textContent = sub.value;
    
}

function readURL(input) {
	
	if (input.files && input.files[0]) {
		
		var reader = new FileReader();
		
		reader.onload = function (e) {
			$('#newImage')
				.attr('src', e.target.result)
				.width(60)
				.height(60);
			};
		
		reader.readAsDataURL(input.files[0]);
		$('#payout_image_name').val(input.files[0].name);
		$('#payout_image_name').prop('disabled', false);
	}
}

</script>
