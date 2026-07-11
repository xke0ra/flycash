<?php
include_once("../admin/core/init.inc.php");
	 include_once("../admin/controller/controller-forgot-password.php");

?><!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once 'includes/dashboard_title.php'; ?>
    <?php include_once 'includes/global_header_scripts.php'; ?>
</head>
<body class="login-page">

    <?php include_once 'includes/dashboard_page_loader.php'; ?>

    <div class="login-card">
        <div class="card-modern-header" style="text-align:center;border:none;justify-content:center;padding-bottom:0;">
            <div>
                <div class="logo">
                    <a href="index.php">
                        <img src="../admin/images/<?php echo esc_attr($configs->getConfig('SITE_LOGO_DARK')); ?>" alt="Logo" loading="lazy">
                    </a>
                </div>
                <h3 style="font-size:24px;margin-top:16px;">Reset Password</h3>
            </div>
        </div>

        <?php if ($error){ ?>
            <div class="alert alert-danger" style="margin:0 24px 16px;"><?php echo esc_attr($error_message); ?></div>
        <?php } ?>

        <?php if ($success){ ?>
            <div class="alert alert-success" style="margin:0 24px 16px;"><?php echo esc_attr($error_message); ?></div>
            <div style="text-align:center;margin:0 24px 24px;">
                <a href="login.php" style="font-weight:600;color:var(--primary);">Back to Login</a>
            </div>
        <?php }else{ ?>
            <form action="forgot-password.php" method="post">
                <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">
                <div class="card-modern-body">
                    <div class="form-group">
                        <input class="form-control" placeholder="Enter your email address" id="email" name="email" type="email" value="<?php echo esc_attr($email); ?>" required>
                    </div>
                </div>
                <div class="card-modern-footer" style="flex-direction:column;">
                    <button type="submit" class="btn btn-primary" style="width:100%;">Reset Password</button>
                </div>
            </form>
        <?php } ?>

        <div class="form-footer" style="margin:0 24px 24px;">
            <span>Remember your password?</span>&nbsp;
            <a href="login.php">Login Here</a>
        </div>
    </div>

    <?php include_once 'includes/global_footer_scripts.php'; ?>

</body>
</html>
