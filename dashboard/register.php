<?php
include_once("../admin/core/init.inc.php");
    include_once("../admin/controller/controller-signup.php");

?><!DOCTYPE html>
<?php include_once 'includes/vendor_comments.php'; ?>
<html lang="en">

	<!-- begin::Head -->
	<head>
	    <?php include_once 'includes/dashboard_title.php'; ?>
	    <?php include_once 'includes/global_header_scripts.php'; ?>

	</head>
	<!-- end::Head -->

	<!-- begin::Body -->
	<body class="login-page">

    <div class="login-card">
        <div class="card-modern-header" style="text-align:center;border:none;justify-content:center;padding-bottom:0;">
            <div>
                <div class="logo">
                    <a href="index.php">
                        <img src="../admin/images/<?php echo esc_attr($configs->getConfig('SITE_LOGO_DARK')); ?>" alt="Logo" loading="lazy">
                    </a>
                </div>
                <h3 style="font-size:24px;margin-top:16px;">Create Account</h3>
            </div>
        </div>

        <?php if ($error){ ?>
            <div class="alert alert-danger" style="margin:0 24px 16px;"><?php echo esc_attr($error_message); ?></div>
        <?php } ?>

        <?php if ($success){ ?>
            <div class="alert alert-success" style="margin:0 24px 16px;"><?php echo esc_attr($success_message); ?></div>
            <div style="text-align:center;margin:0 24px 24px;">
                <span>You can now Sign In here:</span>&nbsp;
                <a href="login.php" style="font-weight:600;color:var(--primary);">Sign In!</a>
            </div>
        <?php }else{ ?>
            <form name="signin_form" action="register.php" method="post">
                <input autocomplete="off" type="hidden" id="authenticity_token" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">
                <div class="card-modern-body">
                    <div class="form-group">
                        <input class="form-control" placeholder="Full Name" autocomplete="off" maxlength="30" id="fullname" name="fullname" type="text" value="<?php echo esc_attr($fullname); ?>" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="Username" autocomplete="off" maxlength="10" id="username" name="username" type="text" value="<?php echo esc_attr($username); ?>" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="Email" autocomplete="off" maxlength="50" id="email" name="email" type="email" value="<?php echo esc_attr($email); ?>" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="Confirm Email" autocomplete="off" maxlength="50" id="confirm_email" name="confirm_email" type="email" value="<?php echo esc_attr($confirm_email); ?>" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="Password" autocomplete="off" type="password" id="password" maxlength="20" name="password" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="Referer Code (optional)" autocomplete="off" maxlength="10" id="referer" name="referer" type="text" <?php if(isset($_SESSION["refererCode"]) && $_SESSION["refererCode"] !== ''){ echo 'readonly'; } ?> value="<?php if(isset($_SESSION["refererCode"]) && $_SESSION["refererCode"] !== ''){ echo esc_attr($_SESSION["refererCode"]); }else{echo esc_attr($referer); } ?>">
                    </div>
                </div>
                <div class="card-modern-footer" style="flex-direction:column;">
                    <button type="submit" class="btn btn-primary" style="width:100%;">Create Account</button>
                </div>
            </form>

            <?php if($configs->getConfig('FACEBOOK_LOGIN_WEB') || $configs->getConfig('GOOGLE_LOGIN_WEB')){ ?>
            <div class="divider" style="margin:0 24px;"><span>OR</span></div>
            <div class="social-login" style="padding:0 24px 16px;">
                <?php if($configs->getConfig('FACEBOOK_LOGIN_WEB')){ ?>
                <a href="../admin/controller/oauth.php?provider=Facebook" class="btn btn-facebook">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    Facebook
                </a>
                <?php } if($configs->getConfig('GOOGLE_LOGIN_WEB')){ ?>
                <a href="../admin/controller/oauth.php?provider=Google" class="btn btn-danger">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Google
                </a>
                <?php } ?>
            </div>
            <?php } ?>
        <?php } ?>

        <div class="form-footer" style="margin:0 24px 24px;">
            <span>Already have an account?</span>&nbsp;
            <a href="login.php">Sign In!</a>
        </div>
    </div>
		<!-- end:: Page -->
		
	    <?php include_once 'includes/global_footer_scripts.php'; ?>
	    
	</body>

	<!-- end::Body -->
</html>
