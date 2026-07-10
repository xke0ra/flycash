<?php
$pagename = 'youtube-offers';
	$container = 'youtube-offers';
	
	include_once("inc/admin.inc.php");
    
    $offerwalls = new offerwalls($dbo);

include_once 'inc/admin_header.php';
?>
<div class="admin-content">
        <!--Main Content-->
        <div class="content sm-gutter">
            <div class="container-fluid padding-25 sm-padding-10">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title">
                            <h4>Youtube Offers Options</h4>
                        </div>
                    </div>
					<?php if(APP_DEMO) { include_once 'inc/demo-notice.php'; } ?>
					
					<!-- START MAIN CONTENT HERE -->
					
					<div class="col-12 col-sm-6 col-md-6 col-lg-6 mb-4 mb-lg-0">
						<div class="block task-block">
							<div class="section-title">
								<h5>Active Youtube Offers</h5>
							</div>
							
							<ul id="todo">
								<?php
								
								    $result = $offerwalls->getYoutubeOffers(0);
								    $offerwalls_loaded = count($result['youtubeoffers']);
								    if ($offerwalls_loaded != 0) {
								        
								        foreach ($result['youtubeoffers'] as $key => $value) {
								            draw($value);
								            
								        }
								        
								    }
								    
								?>
							</ul>
						</div>
					</div>

					<div class="col-12 col-sm-6 col-md-6 col-lg-6 mb-4 mb-lg-0">
						<div class="block task-block">
							<div class="section-title">
								<h5>Deactived</h5>
							</div>
							
							<ul id="inprogress">
							    
							    <?php
								
								    if ($offerwalls_loaded != 0) {
								        
								        foreach ($result['youtubeoffers'] as $key => $value) {
								            drawDeactives($value);
								            
								        }
								        
								    }
								    
								?>
							</ul>
						</div>
					</div>
					
					<div class="block bg-trans mb-2">&nbsp;</div>
					
					<!-- END MAIN CONTENT HERE -->
					<?php include_once 'inc/support.php'; ?>
					
                </div>
            </div>
        </div>
</div><!-- /admin-content -->
<?php include_once 'inc/admin_footer.php'; ?>
<?php

    function draw($offerwall)
    {
        
        if($offerwall['offer_status'] == 'Active'){
        
	?>
	
								<!--Task-->
								<li>
									<div class="task align-items-center" style="cursor: auto;">
										<div class="members single">
											<div class="member rounded-circle float-left" style=" border-radius: 0%;">
												<img class="img-fluid" src="images/<?php echo htmlspecialchars($offerwall['offer_thumbnail'], ENT_QUOTES, 'UTF-8'); ?>" />
											</div>
										</div>
										<div class="task-desc">
											<p class="task-title text-truncate"><?php echo htmlspecialchars($offerwall['offer_title'], ENT_QUOTES, 'UTF-8'); ?></p>
											<span class="end-time text-truncate"><?php echo htmlspecialchars($offerwall['offer_subtitle'], ENT_QUOTES, 'UTF-8'); ?></span>
										</div>
										<div class="members single">
											<div class="float-right">
											<a href="process/youtube-offer.php?action=0&id=<?php echo (int)$offerwall['offer_id']; ?>" class="btn btn-danger btn-small"><i class="fa fa-eye-slash"></i>Deactivate</a>
											<a href="edit-youtube-offer.php?id=<?php echo (int)$offerwall['offer_id']; ?>" class="btn btn-warning btn-small"><i class="fa fa-edit"></i>Edit</i></a>
											</div>
										</div>
									</div>
								</li>
								
	<?php
        }
    }
    
    function drawDeactives($offerwall)
    {
        
        if($offerwall['offer_status'] != 'Active'){
        
	?>
	
								<!--Task-->
								<li>
									<div class="task align-items-center" style="cursor: auto;">
										<div class="members single">
											<div class="member rounded-circle float-left" style=" border-radius: 0%;">
												<img class="img-fluid" src="images/<?php echo htmlspecialchars($offerwall['offer_thumbnail'], ENT_QUOTES, 'UTF-8'); ?>" />
											</div>
										</div>
										<div class="task-desc">
											<p class="task-title text-truncate"><?php echo htmlspecialchars($offerwall['offer_title'], ENT_QUOTES, 'UTF-8'); ?></p>
											<span class="end-time text-truncate"><?php echo htmlspecialchars($offerwall['offer_subtitle'], ENT_QUOTES, 'UTF-8'); ?></span>
										</div>
										<div class="members single">
											<div class="float-right">
											<a href="process/youtube-offer.php?action=1&id=<?php echo (int)$offerwall['offer_id']; ?>" class="btn btn-primary btn-small"><i class="fa fa-eye"></i>Activate</a>
											<a href="edit-youtbe-offer.php?id=<?php echo (int)$offerwall['offer_id']; ?>" class="btn btn-warning btn-small"><i class="fa fa-edit"></i>Edit</i></a>
											</div>
										</div>
									</div>
								</li>
								
	<?php
        }
    }
?>
