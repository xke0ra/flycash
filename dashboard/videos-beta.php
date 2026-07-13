<?php
$rel = dirname(__FILE__);
    include_once($rel."/../admin/core/init.inc.php");
    include_once($rel."/includes/user.inc.php");
    $offerwalls = new offerwalls($dbo);

    $pagename = "videos";

?><!DOCTYPE html>
<?php include_once 'includes/vendor_comments.php'; ?>
<html lang="en">
<head>
    <?php include_once 'includes/dashboard_title.php'; ?>
    <?php include_once 'includes/global_header_scripts.php'; ?>
</head>
<body>

    <?php include_once 'includes/dashboard_header_mobile.php'; ?>
    <?php include_once $rel.'/includes/dashboard_header.php'; ?>

    <div class="page-header">
        <div class="container">
            <div>
                <h1 class="page-title">Videos</h1>
                <div class="page-breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="sep">/</span>
                    <span>Videos</span>
                </div>
            </div>
            <div class="page-actions">
                <a href="redeem.php" class="btn btn-primary">Redeem</a>
            </div>
        </div>
    </div>

    <main class="page-content">
        <div class="container">

            <div class="card-modern">
                <div class="card-modern-header">
                    <h3>Watch & Earn <small>Earn points by watching videos</small></h3>
                </div>
                <div class="card-modern-body">
                    <?php
                        $result = $offerwalls->getMyYoutubeOffers(0);
                        $offerwalls_loaded = count($result['youtubeoffers']);
                        if ($offerwalls_loaded != 0) {
                            foreach ($result['youtubeoffers'] as $key => $value) { ?>
                                <div class="video-list-item">
                                    <img class="video-list-thumb" src="../admin/images/<?php echo $value['offer_thumbnail']; ?>" alt="" loading="lazy">
                                    <div class="video-list-info">
                                        <h4><?php echo $value['offer_title']; ?></h4>
                                        <p><?php echo $value['offer_subtitle']; ?></p>
                                    </div>
                                    <a href="watch-video.php?id=<?php echo $value['offer_id']; ?>" class="btn btn-primary"><?php echo $value['offer_points']; ?> Points</a>
                                </div>
                            <?php }
                        } else { ?>
                            <div class="text-center" style="padding:48px 16px;">
                                <h4 style="margin:0 0 8px;">No Videos Found</h4>
                                <p class="text-muted" style="margin:0;">Check back later for new videos.</p>
                            </div>
                        <?php } ?>
                </div>
            </div>

        </div>
    </main>

    <?php include_once $rel.'/includes/dashboard_footer.php'; ?>
    <?php include_once $rel.'/includes/dashboard_scroll_to_top.php'; ?>
    <?php include_once $rel.'/includes/global_footer_scripts.php'; ?>

</body>
</html>
