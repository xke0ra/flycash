<?php

    /*!
	 * POCKET v3.7
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */

	$pagename = 'redeem';
	$container = '';

    include_once("includes/user.inc.php");

    // Redeem List
    $payouts = new redeem($dbo);
    $payoutList = $payouts->getPayouts();
    $payoutList = $payoutList['payouts'];

?><!DOCTYPE html>
<?php include_once 'includes/vendor_comments.php'; ?>
<html lang="en">
<head>
    <?php include_once 'includes/dashboard_title.php'; ?>
    <?php include_once 'includes/global_header_scripts.php'; ?>
</head>
<body>

    <?php include_once 'includes/dashboard_header_mobile.php'; ?>
    <?php include_once 'includes/dashboard_header.php'; ?>

    <div class="page-header">
        <div class="container">
            <div>
                <h1 class="page-title">Redeem</h1>
                <div class="page-breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="sep">/</span>
                    <span>Redeem</span>
                </div>
            </div>
            <div class="page-actions">
                <a href="redeem.php" class="btn btn-primary">Redeem</a>
            </div>
        </div>
    </div>

    <main class="page-content">
        <div class="container">

            <?php include_once("../admin/controller/notices.php"); ?>

            <div class="redeem-grid">
                <?php if (isset($payoutList) && is_array($payoutList)) {
                    foreach($payoutList as $payout){
                        if($payout['payout_status'] == "Active"){
                ?>
                <div class="redeem-card">
                    <img class="redeem-card-img" src="../admin/images/<?php echo esc_attr($payout['payout_thumbnail']); ?>" alt="<?php echo esc_attr($payout['payout_title']); ?>">
                    <h4><?php echo esc_attr($payout['payout_title']); ?></h4>
                    <div class="subtitle"><?php echo esc_attr($payout['payout_subtitle']); ?></div>
                    <div class="detail-row">
                        <span class="detail-label">Name</span>
                        <span class="detail-value"><?php echo esc_attr($payout['payout_title']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Amount</span>
                        <span class="detail-value"><?php echo esc_attr($payout['payout_amount']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Points Required</span>
                        <span class="detail-value"><?php echo esc_attr($payout['payout_pointsRequired']); ?></span>
                    </div>
                    <button type="button" class="btn-redeem" onclick="<?php if($req_user_info['points'] >= $payout['payout_pointsRequired']){ echo "showRedeemAlert('".$payout['payout_id']."', '".$payout['payout_title']."', '".$payout['payout_subtitle']."', '".$payout['payout_message']."')"; }else{ echo 'showNoEnoughPointsAlert()'; } ?>">Redeem</button>
                </div>
                <?php } } } ?>
            </div>

        </div>
    </main>

    <?php include_once 'includes/dashboard_footer.php'; ?>
    <?php include_once 'includes/dashboard_scroll_to_top.php'; ?>
    <?php include_once 'includes/global_footer_scripts.php'; ?>

</body>
</html>
