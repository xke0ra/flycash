<?php

    /*!
	 * POCKET v3.7
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */
    
    $rel = dirname(__FILE__);
    include_once($rel."/../admin/core/init.inc.php");
    include_once($rel."/includes/user.inc.php");
    $offerwalls = new offerwalls($dbo);

    $pagename = "videos";
    include_once $rel.'/includes/dashboard_header.php';
?>


<div class="page-header">
    <div>
        <h1 class="page-title">Videos</h1>
        <p class="page-desc">Watch videos and earn points</p>
    </div>
</div>

<div class="page-content">
    <div class="card-modern">
        <div class="card-modern-body">
            <?php
                $result = $offerwalls->getMyYoutubeOffers(0);
                $offerwalls_loaded = count($result['youtubeoffers']);
                if ($offerwalls_loaded != 0) {
                    foreach ($result['youtubeoffers'] as $key => $value) { ?>
                        <div class="offers-item" style="display:flex;align-items:center;gap:16px;padding:16px 0;border-bottom:1px solid var(--border);">
                            <img src="../admin/images/<?php echo $value['offer_thumbnail']; ?>" alt="" style="width:64px;height:64px;border-radius:8px;object-fit:cover;">
                            <div style="flex:1;">
                                <h4 style="margin:0 0 4px;"><?php echo $value['offer_title']; ?></h4>
                                <p style="margin:0;color:var(--muted);font-size:14px;"><?php echo $value['offer_subtitle']; ?></p>
                            </div>
                            <a href="watch-video.php?id=<?php echo $value['offer_id']; ?>" class="btn btn-primary" style="white-space:nowrap;"><?php echo $value['offer_points']; ?> Points</a>
                        </div>
                    <?php }
                } else { ?>
                    <div style="text-align:center;padding:40px 0;">
                        <h4>No Videos Found</h4>
                        <p style="color:var(--muted);">Check back later for new videos.</p>
                    </div>
                <?php } ?>
        </div>
    </div>
</div>


<?php include_once $rel.'/includes/dashboard_footer.php'; ?>
