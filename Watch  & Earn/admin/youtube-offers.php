<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */

	$pagename = 'youtube-offers';
	$container = 'youtube-offers';
	
	include_once("core/init.inc.php");

    if (!admin::isSession()) {

        header("Location: index.php");
    }
    
    $offerwalls = new offerwalls($dbo);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta content="ie=edge" http-equiv="x-ua-compatible" />
	<?php include_once 'inc/title.php'; ?>

    <!--Preloader-CSS-->
    <link rel="stylesheet" href="./assets/plugins/preloader/preloader.css" />

    <!--bootstrap-4-->
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css" />

    <!--Custom Scroll-->
    <link rel="stylesheet" href="./assets/plugins/customScroll/jquery.mCustomScrollbar.min.css" />
    <!--Font Icons-->
    <link rel="stylesheet" href="./assets/icons/simple-line/css/simple-line-icons.css" />
    <link rel="stylesheet" href="./assets/icons/dripicons/dripicons.css" />
    <link rel="stylesheet" href="./assets/icons/ionicons/css/ionicons.min.css" />
    <link rel="stylesheet" href="./assets/icons/eightyshades/eightyshades.css" />
    <link rel="stylesheet" href="./assets/icons/fontawesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="./assets/icons/foundation/foundation-icons.css" />
    <link rel="stylesheet" href="./assets/icons/metrize/metrize.css" />
    <link rel="stylesheet" href="./assets/icons/typicons/typicons.min.css" />
    <link rel="stylesheet" href="./assets/icons/weathericons/css/weather-icons.min.css" />

    <!--Date-range-->
    <link rel="stylesheet" href="./assets/plugins/date-range/daterangepicker.css" />
    <!--Drop-Zone-->
    <link rel="stylesheet" href="./assets/plugins/dropzone/dropzone.css" />
    <!--Full Calendar-->
    <link rel="stylesheet" href="./assets/plugins/full-calendar/fullcalendar.min.css" />
    <!--Normalize Css-->
    <link rel="stylesheet" href="./assets/css/normalize.css" />
    <!--Main Css-->
    <link rel="stylesheet" href="./assets/css/main.css" />
    <!--Custom Css-->
    <link rel="stylesheet" href="./assets/css/custom.css" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<?php include_once 'inc/preloader.php'; ?>

<?php include_once 'inc/navigation.php'; ?>

<!--Page Container-->
<section class="page-container">
    <div class="page-content-wrapper">
        <!--Header Fixed-->
		<?php include_once 'inc/header-fixed.php'; ?>

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
    </div>
	
	<?php include_once 'inc/footer-fixed.php'; ?>

</section>

<!--Jquery-->
<script type="text/javascript" src="./assets/js/jquery-3.2.1.min.js"></script>
<!--Bootstrap Js-->
<script type="text/javascript" src="./assets/js/popper.min.js"></script>
<script type="text/javascript" src="./assets/js/bootstrap.min.js"></script>
<!--Modernizr Js-->
<script type="text/javascript" src="./assets/js/modernizr.custom.js"></script>

<!--Morphin Search JS-->
<script type="text/javascript" src="./assets/plugins/morphin-search/classie.js"></script>
<script type="text/javascript" src="./assets/plugins/morphin-search/morphin-search.js"></script>
<!--Morphin Search JS-->
<script type="text/javascript" src="./assets/plugins/preloader/pathLoader.js"></script>
<script type="text/javascript" src="./assets/plugins/preloader/preloader-main.js"></script>

<!--Chart js-->
<script type="text/javascript" src="./assets/plugins/charts/Chart.min.js"></script>

<!--Sparkline Chart Js-->
<script type="text/javascript" src="./assets/plugins/sparkline/jquery.sparkline.min.js"></script>
<script type="text/javascript" src="./assets/plugins/sparkline/jquery.charts-sparkline.js"></script>

<!--Custom Scroll-->
<script type="text/javascript" src="./assets/plugins/customScroll/jquery.mCustomScrollbar.min.js"></script>
<!--Sortable Js-->
<script type="text/javascript" src="./assets/plugins/sortable2/sortable.min.js"></script>
<!--DropZone Js-->
<script type="text/javascript" src="./assets/plugins/dropzone/dropzone.js"></script>
<!--Date Range JS-->
<script type="text/javascript" src="./assets/plugins/date-range/moment.min.js"></script>
<script type="text/javascript" src="./assets/plugins/date-range/daterangepicker.js"></script>
<!--CK Editor JS-->
<script type="text/javascript" src="./assets/plugins/ckEditor/ckeditor.js"></script>
<!--Data-Table JS-->
<script type="text/javascript" src="./assets/plugins/data-tables/datatables.min.js"></script>
<!--Editable JS-->
<script type="text/javascript" src="./assets/plugins/editable/editable.js"></script>
<!--Full Calendar JS-->
<script type="text/javascript" src="./assets/plugins/full-calendar/fullcalendar.min.js"></script>

<!--- Main JS -->
<script src="./assets/js/main.js"></script>

</body>
</html>
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
												<img class="img-fluid" src="images/<?php echo $offerwall['offer_thumbnail']; ?>" />
											</div>
										</div>
										<div class="task-desc">
											<p class="task-title text-truncate"><?php echo $offerwall['offer_title']; ?></p>
											<span class="end-time text-truncate"><?php echo $offerwall['offer_subtitle']; ?></span>
										</div>
										<div class="members single">
											<div class="float-right">
											<a href="process/youtube-offer.php?action=0&id=<?php echo $offerwall['offer_id']; ?>" class="btn btn-danger btn-small"><i class="fa fa-eye-slash"></i>Deactivate</a>
											<a href="edit-youtube-offer.php?id=<?php echo $offerwall['offer_id']; ?>" class="btn btn-warning btn-small"><i class="fa fa-edit"></i>Edit</i></a>
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
												<img class="img-fluid" src="images/<?php echo $offerwall['offer_thumbnail']; ?>" />
											</div>
										</div>
										<div class="task-desc">
											<p class="task-title text-truncate"><?php echo $offerwall['offer_title']; ?></p>
											<span class="end-time text-truncate"><?php echo $offerwall['offer_subtitle']; ?></span>
										</div>
										<div class="members single">
											<div class="float-right">
											<a href="process/youtube-offer.php?action=1&id=<?php echo $offerwall['offer_id']; ?>" class="btn btn-primary btn-small"><i class="fa fa-eye"></i>Activate</a>
											<a href="edit-youtbe-offer.php?id=<?php echo $offerwall['offer_id']; ?>" class="btn btn-warning btn-small"><i class="fa fa-edit"></i>Edit</i></a>
											</div>
										</div>
									</div>
								</li>
								
	<?php
        }
    }
?>