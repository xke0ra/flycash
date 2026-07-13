<?php
$pagename = 'change-password';
	$container = '';

    include_once("includes/user.inc.php");
    include_once("../admin/controller/controller-change-password.php");

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
                <h1 class="page-title">Change Password</h1>
                <div class="page-breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="sep">/</span>
                    <a href="profile.php">Account Settings</a>
                    <span class="sep">/</span>
                    <span>Change Password</span>
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

            <div class="profile-layout">
                <div class="sidebar-card">
                    <div class="sidebar-card-header">
                        <div class="avatar"><?php echo esc_attr(strtoupper($req_user_info['fullname'][0])); ?></div>
                        <h4><?php echo esc_attr($req_user_info['fullname']); ?></h4>
                        <p>Member Since <?php echo esc_attr(date('d M, Y', $req_user_info['regtime'])); ?></p>
                    </div>
                    <div class="sidebar-nav">
                        <a href="profile.php">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Account Information
                        </a>
                        <a href="change-password.php" class="active">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            Change Password
                        </a>
                        <a href="transactions.php">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            My Transactions
                        </a>
                        <a href="refer.php">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            My Referral Link
                        </a>
                    </div>
                </div>

                <div class="card-modern">
                    <div class="card-modern-header">
                        <h3>Change Password</h3>
                    </div>
                    <form method="post" action="">
                        <div class="card-modern-body">
                            <?php if($error){ ?>
                            <div class="alert alert-danger">
                                <span class="alert-icon">&#9888;</span>
                                <?php echo esc_attr($error_message); ?>
                            </div>
                            <?php } ?>
                            <?php if($success){ ?>
                            <div class="alert alert-success">
                                <span class="alert-icon">&#10003;</span>
                                <?php echo esc_attr($success_message); ?>
                            </div>
                            <?php } ?>

                            <div class="form-group">
                                <label>Current Password</label>
                                <input type="password" class="form-control" name="old_password" placeholder="Enter current password" required>
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" class="form-control" name="new_password" placeholder="Enter new password" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" class="form-control" name="confirm_new_password" placeholder="Confirm new password" required>
                            </div>
                        </div>
                        <div class="card-modern-footer">
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <?php include_once 'includes/dashboard_footer.php'; ?>
    <?php include_once 'includes/dashboard_scroll_to_top.php'; ?>
    <?php include_once 'includes/global_footer_scripts.php'; ?>

</body>
</html>
