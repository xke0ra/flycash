<?php

    /*!
	 * POCKET v3.7
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */
    
    include_once("../admin/core/init.inc.php");
        $offerwalls = new offerwalls($dbo);

?><!DOCTYPE html>
<?php include_once 'includes/vendor_comments.php'; ?>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">


    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css">
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css">

    <link href="../admin/custom/videos/assets/AYM-custom.css" rel="stylesheet" type="text/css">
</head>


<body class="kt-page--loading-enabled kt-page--loading kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header--minimize-menu kt-header-mobile--fixed kt-subheader--enabled kt-subheader--transparent kt-page--loading">
    <div class="col-xl-12 col-lg-12" style="padding: 0px;">
        <div class="kt-portlet kt-portlet--tabs">
            <div class="kt-portlet__body" style="background: #f8f8fb;">
                <div class="tab-content">
                    <div class="tab-pane active" id="custom" role="tabpanel">
                        <div class="kt-widget4">
								<?php
								
								    $result = $offerwalls->getMyYoutubeOffers(0);
								    $offerwalls_loaded = count($result['youtubeoffers']);
								    if ($offerwalls_loaded != 0) {
								        
								        foreach ($result['youtubeoffers'] as $key => $value) {
								           
								            ?>
								            
                            <div class="kt-portlet kt-portlet--height-fluid"><a href="watch-video.php?id=<?php echo $value['offer_id']; ?> " target="_blank">
                                    <div class="kt-portlet__head kt-portlet__head--noborder">
                                    </div>
                                </a>
                                <div class="kt-portlet__body"><a href="watch-video.php?id=<?php echo $value['offer_id']; ?> " target="_blank">
                                    </a>
                                    <div class="kt-widget kt-widget--user-profile-2"><a href="watch-video.php?id=<?php echo $value['offer_id']; ?>" target="_blank">
                                        </a>
                                        <div class="kt-widget__head"><a href="watch-video.php?id=<?php echo $value['offer_id']; ?>" target="_blank">
                                                <span class="kt-media kt-media--lg kt-media--brand kt-margin-r-5 kt-margin-t-5">
                                                    <img class="kt-widget__img" src="../admin/images/<?php echo $value['offer_thumbnail']; ?>" alt="">
                                                </span>
                                            </a>
                                            <div class="kt-widget__info"><a href="watch-video.php?id=<?php echo $value['offer_id']; ?>" target="_blank">
                                                </a><a href="watch-video.php?id=<?php echo $value['offer_id']; ?>" class="kt-widget__username"><?php echo $value['offer_title']; ?></a>
                                                <span class="kt-widget__desc"><?php echo $value['offer_subtitle']; ?>  </span>
                                            </div>
                                        </div>
                                        <a href="watch-video.php?id=<?php echo $value['offer_id']; ?>" target="_blank" class="kt-widget__footer">
                                            <p class="btn btn-label-brand btn-lg btn-upper" style="cursor: pointer !important;"> <span class="offer-points" style=" font-size: 24px; "><?php echo $value['offer_points']; ?> </span><br><span class="offer-points-title" style=" font-size: 12px; ">Points</span></p>
                                        </a>
                                    </div>
                                </div>
                            </div>

								            
								            <?php 
								        }
								        
								    }else
								    {
								        ?>
								         <div class="kt-portlet kt-portlet--height-fluid">  <div class="kt-widget kt-widget--user-profile-2">
								             <h1 style="padding: 14px;text-align: center;">No Video were Found</h1>
								             </div>
								             </div>
								        <?php
								        
								    }
								    
								?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



</body>

</html>
