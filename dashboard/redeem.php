<?php
$pagename = 'redeem';
	$container = '';

    include_once("includes/user.inc.php");

    // Redeem List
    $payouts = new redeem($dbo);
    $payoutList = $payouts->getPayouts();
    $payoutList = $payoutList['payouts'];

    $hasActivePayouts = false;
    if (isset($payoutList) && is_array($payoutList)) {
        foreach ($payoutList as $payout) {
            if ($payout['payout_status'] === 'Active') {
                $hasActivePayouts = true;
                break;
            }
        }
    }

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
                <button type="button" class="btn btn-primary" onclick="document.getElementById('rewards-grid').scrollIntoView({behavior:'smooth'})">Redeem</button>
            </div>
        </div>
    </div>

    <main class="page-content">
        <div class="container">

            <?php include_once("../admin/controller/notices.php"); ?>

            <div class="redeem-grid" id="rewards-grid">
                <?php if ($hasActivePayouts) {
                    foreach($payoutList as $payout){
                        if($payout['payout_status'] == "Active"){
                ?>
                <div class="redeem-card">
                    <img class="redeem-card-img" src="../admin/images/<?php echo esc_attr($payout['payout_thumbnail']); ?>" alt="<?php echo esc_attr($payout['payout_title']); ?>" loading="lazy">
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
                <?php } } ?>
                <?php } else { ?>
                <div class="notif-empty" style="grid-column:1/-1;padding:48px 16px;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--gray-300);margin-bottom:12px;"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    <div style="font-size:15px;color:var(--gray-500);margin-bottom:4px;">No rewards available</div>
                    <div style="font-size:13px;color:var(--gray-400);">Check back soon for new rewards.</div>
                </div>
                <?php } ?>
            </div>

        </div>
    </main>

    <?php include_once 'includes/dashboard_footer.php'; ?>
    <?php include_once 'includes/dashboard_scroll_to_top.php'; ?>
    <?php include_once 'includes/global_footer_scripts.php'; ?>

</body>
</html>
