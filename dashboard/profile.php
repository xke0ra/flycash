<?php
$pagename = 'profile';
	$container = '';

    include_once("includes/user.inc.php");
    include_once("../admin/controller/controller-profile.php");

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
                <h1 class="page-title">Account Settings</h1>
                <div class="page-breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="sep">/</span>
                    <span>Account Settings</span>
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
                <!-- Sidebar -->
                <div class="sidebar-card">
                    <div class="sidebar-card-header">
                        <div class="avatar"><?php echo esc_attr(!empty($req_user_info['fullname']) ? strtoupper($req_user_info['fullname'][0]) : '?'); ?></div>
                        <h4><?php echo esc_attr($req_user_info['fullname']); ?></h4>
                        <p>Member Since <?php echo esc_attr(date('d M, Y', $req_user_info['regtime'])); ?></p>
                    </div>
                    <div class="sidebar-nav">
                        <a href="profile.php" class="active">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Account Information
                        </a>
                        <a href="change-password.php">
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

                <!-- Main Content -->
                <div class="card-modern">
                    <div class="card-modern-header">
                        <h3>Account Information <small>update your personal information</small></h3>
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
                                <?php echo esc_attr($error_message); ?>
                            </div>
                            <?php } ?>

                            <div class="form-section-title">Account Information</div>

                            <div class="form-group">
                                <label>Username</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        </span>
                                    </div>
                                    <input class="form-control" type="text" value="<?php echo esc_attr($req_user_info['login']); ?>" disabled>
                                </div>
                                <div class="form-text">Usernames cannot be changed. <a href="<?php echo esc_attr($configs->getConfig('APP_CONTACT_US_URL')); ?>" target="_blank">Contact us</a> to change it.</div>
                            </div>

                            <div class="form-group">
                                <label>Referral Code</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                        </span>
                                    </div>
                                    <input class="form-control" type="text" value="<?php echo esc_attr($req_user_info['refer']); ?>" disabled>
                                </div>
                                <div class="form-text">Referral code cannot be changed. <a href="<?php echo esc_attr($configs->getConfig('APP_CONTACT_US_URL')); ?>" target="_blank">Contact us</a> to change it.</div>
                            </div>

                            <div class="form-section-title">Contact Info</div>

                            <div class="form-group">
                                <label>Full Name</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" name="full_name" value="<?php echo esc_attr($req_user_info['fullname']); ?>" placeholder="Full Name" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email Address</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">@</span>
                                    </div>
                                    <input type="email" class="form-control" name="email" value="<?php echo esc_attr($req_user_info['email']); ?>" placeholder="Email" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Contact Phone</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        </span>
                                    </div>
                                    <input type="number" name="mobile" class="form-control" value="<?php echo esc_attr($req_user_info['mobile']); ?>" placeholder="Phone" required>
                                </div>
                                <div class="form-text">We'll never share your phone number with anyone.</div>
                            </div>
                        </div>
                        <div class="card-modern-footer">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
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
