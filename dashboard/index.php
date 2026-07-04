<?php

    /*!
	 * POCKET v3.7
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */

     // Install Handler
	if (!file_exists('../admin')) {
	    include_once("includes/notice-admin-not-installed.php");
		exit;
	}

	$pagename = 'dashboard';
	$container = '';

    include_once("includes/user.inc.php");

	$configs->updateAnalyticsSessions();

    // User Points
	$userCurrentPoints = $req_user_info['points'];
	$userRedeemedPoints = $configs->getUserRedeemedPoints($req_user_info['login']);
	$userTotalPoints = $userCurrentPoints+$userRedeemedPoints;

	// User's Referred Members
	$userreferredMembers = $configs->getUserReferredMembers($req_user_info['refer']);

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
                <h1 class="page-title">Dashboard</h1>
                <div class="page-breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="sep">/</span>
                    <span>Dashboard</span>
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

            <div class="stats-grid">
                <div class="stat-card">
                    <h6>Current Points</h6>
                    <h2><?php echo esc_attr($userCurrentPoints); ?></h2>
                </div>
                <div class="stat-card">
                    <h6>Total Earned</h6>
                    <h2><?php echo esc_attr($userTotalPoints); ?></h2>
                </div>
                <div class="stat-card">
                    <h6>Redeemed Points</h6>
                    <h2><?php echo esc_attr($userRedeemedPoints); ?></h2>
                </div>
                <div class="stat-card">
                    <h6>Members Referred</h6>
                    <h2><?php echo esc_attr($userreferredMembers); ?></h2>
                </div>
            </div>

            <div class="dashboard-layout">
                <div class="dashboard-main">
                    <?php
                    include_once 'includes/dashboard_offerwalls.php';
                    ?>
                </div>
                <div class="dashboard-sidebar">
                    <?php if($configs->getConfig('DAILY_ACTIVE')){ include_once 'includes/dashboard_daily_checkin.php'; } ?>
                    <?php if($configs->getConfig('WEB_SHOW_NEW_FEATURE_NOTICE')){ include_once 'includes/dashboard_new_feature.php'; } ?>
                    <?php if($configs->getConfig('WEB_SHOW_ANNOUNCEMENT')){ include_once 'includes/dashboard_announcement.php'; } ?>
                    <?php if($configs->getConfig('WEB_SHOW_RECENT_PAYOUTS')){ include_once 'includes/dashboard_recent_payouts.php'; } ?>
                </div>
            </div>

        </div>
    </main>

    <?php include_once 'includes/dashboard_footer.php'; ?>
    <?php include_once 'includes/dashboard_scroll_to_top.php'; ?>
    <?php include_once 'includes/global_footer_scripts.php'; ?>

    <script src="assets/js/pages/dashboard.js" type="text/javascript"></script>
    <script src="assets/js/daily-checkin.js" type="text/javascript"></script>

</body>
</html>
