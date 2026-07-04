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
        <div class="logo">
            <a href="index.php">
                <img src="../admin/images/<?php echo esc_attr($configs->getConfig('SITE_LOGO_DARK')); ?>" alt="Logo">
            </a>
        </div>
        <h3>Reset Password</h3>

        <?php if ($error){ ?>
            <div class="alert alert-danger"><?php echo esc_attr($error_message); ?></div>
        <?php } ?>

        <?php if ($success){ ?>
            <div class="alert alert-success"><?php echo esc_attr($error_message); ?></div>
            <div style="text-align:center;margin-top:16px;">
                <a href="login.php" style="font-weight:600;color:var(--primary);">Back to Login</a>
            </div>
        <?php }else{ ?>
            <form action="forgot-password.php" method="post">
                <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">
                <div class="form-group">
                    <input class="form-control" placeholder="Enter your email address" id="email" name="email" type="email" value="<?php echo esc_attr($email); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Reset Password</button>
            </form>
        <?php } ?>

        <div class="form-footer">
            <span>Remember your password?</span>&nbsp;
            <a href="login.php">Login Here</a>
        </div>
    </div>

    <?php include_once 'includes/global_footer_scripts.php'; ?>

</body>
</html>
