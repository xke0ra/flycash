<?php
$pagename = 'refer';
	$container = '';

    include_once("includes/user.inc.php");

    // User Refer Code
	$userReferCode = $req_user_info['refer'];

	// User Refer URL
	$webRoot = $configs->getConfig('WEB_ROOT');
	$userReferURL = $webRoot.'/refer/?refer='.$userReferCode;
	if(substr($webRoot, -1) == "/"){ $userReferURL = $webRoot.'refer/?refer='.$userReferCode; }

	// User's Referred Members
	$userreferredMembers = $configs->getUserReferredMembers($req_user_info['refer']);

	// User's Income from Referred Members
	$userIncomeFromReferredMembers = $configs->getUserReferIncome($req_user_info['id']);

	// defined refer reward
	$referReward = $configs->getConfig('REFER_REWARD');

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
                <h1 class="page-title">Refer & Earn</h1>
                <div class="page-breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="sep">/</span>
                    <span>Refer & Earn</span>
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

            <div class="refer-layout">
                <div>
                    <?php include_once 'includes/dashboard_stats_refer.php'; ?>
                </div>
                <div>
                    <div class="card-modern">
                        <div class="card-modern-header">
                            <h3>Refer & Earn</h3>
                        </div>
                        <div class="card-modern-body text-center">
                            <p class="refer-text">Earn unlimited rewards by referring your friends, family and followers. You'll earn <?php echo esc_attr($referReward); ?> points for every user you refer and your referal gets <?php echo esc_attr($referReward); ?> Points too.</p>

                            <div class="input-group mx-auto" style="max-width:500px;">
                                <input type="text" class="form-control refer-url-input" value="<?php echo esc_attr($userReferURL); ?>" disabled>
                                <div class="input-group-append">
                                    <a href="#" class="btn btn-secondary" onclick="copyReferURLToClipboard('<?php echo esc_attr($userReferURL); ?>')">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </a>
                                </div>
                            </div>

                            <br>
                            <p class="refer-text">Share above Referral Link or unique Referral Code <code class="refer-code"><a href="#" onclick="copyReferCodeToClipboard('<?php echo esc_attr($userReferCode); ?>')"><?php echo esc_attr($userReferCode); ?></a></code> to your Friends & Family.</p>
                            <br>

                            <div class="share-buttons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr($userReferURL); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;" target="_blank" class="btn btn-facebook">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                                    Share on Facebook
                                </a>
                                <a href="http://twitter.com/share?text=Earn free rewards and gift cards with @<?php echo esc_attr($configs->getConfig('APP_NAME')); ?>. Join me today : &amp;url=<?php echo esc_attr($userReferURL); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;" target="_blank" class="btn btn-twitter">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                    Share on Twitter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include_once 'includes/dashboard_how_to_refer.php'; ?>

        </div>
    </main>

    <?php include_once 'includes/dashboard_footer.php'; ?>
    <?php include_once 'includes/dashboard_scroll_to_top.php'; ?>
    <?php include_once 'includes/global_footer_scripts.php'; ?>

</body>
</html>
