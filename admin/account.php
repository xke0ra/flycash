<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */
	 
	 include_once("core/init.inc.php");

	if (admin::isSession()) {

		header("Location: admin.php");
	}

	$admin = new admin($dbo);

	if ($admin->getCount() > 0) {

		header("Location: login.php");
	}
	
	$error = false;
	$error_message = array();

	$user_username = '';
	$user_fullname = '';
	$user_password = '';
	$user_password_repeat = '';

	$error_token = false;
	$error_username = false;
	$error_fullname = false;
	$error_password = false;
	$error_password_repeat = false;

	if (!empty($_POST)) {

		$error = false;

		$user_username = isset($_POST['user_username']) ? $_POST['user_username'] : '';
		$user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';
		$user_fullname = isset($_POST['user_fullname']) ? $_POST['user_fullname'] : '';
		$token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

		$user_username = helper::clearText($user_username);
		$user_fullname = helper::clearText($user_fullname);
		$user_password = helper::clearText($user_password);
		$user_password_repeat = helper::clearText($user_password_repeat);

		$user_username = helper::escapeText($user_username);
		$user_fullname = helper::escapeText($user_fullname);
		$user_password = helper::escapeText($user_password);
		$user_password_repeat = helper::escapeText($user_password_repeat);

		if (helper::getAuthenticityToken() !== $token) {

			$error = true;
			$error_token = true;
			$error_message[] = 'Error!';
		}

		if (!helper::isCorrectLogin($user_username)) {

			$error = true;
			$error_username = true;
			$error_message[] = 'Username Should be 5 Characters or more';
		}

		if (!helper::isCorrectPassword($user_password)) {

			$error = true;
			$error_password = true;
			if(strlen($user_password) < 6){
				$error_message[] = 'Password Should be 6 Characters or more';
			}else{
				$error_message[] = 'Password Should not contain any symbols like ( @ * - & . )';
			}
		}

		if (!$error) {

			$admin = new admin($dbo);

			$result = array();
			$result = $admin->signup($user_username, $user_password, $user_fullname);

			if ($result['error'] === false) {

				$access_data = $admin->signin($user_username, $user_password);

				if ($access_data['error'] === false) {

					$clientId = 0; // Desktop version

					admin::createAccessToken();

					admin::setSession($access_data['accountId'], admin::getAccessToken(), $user_username);

					header("Location: admin.php");
				}
			}
		}
	}

	helper::newAuthenticityToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta content="ie=edge" http-equiv="x-ua-compatible" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin | Create Account</title>
    <meta name="theme-color" content="#6366f1">
    <link href="./assets/images/favicon.ico" rel="shortcut icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/modern-admin.css?ver=2.0" />
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./assets/css/main.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<section style="background: url(assets/images/bg.jpg);background-size: cover">
    <div class="height-100-vh bg-primary-trans">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="login-div">
						
						<?php if ($error){ ?>
						
							<div class="alert alert-danger">
								<?php 
								foreach ($error_message as $msg) {
									echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . "<br />";
								} ?>
							</div>
							
						<?php } ?>
                        <p class="logo mb-1">Create Admin Account</p>
                        <p class="mb-4" style="color: #a5b5c5">Remember that now Creating an account for Admin Login!</p>
                        <form id="needs-validation" action="account.php" method="post" novalidate="" />
                            <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">
							<div class="form-group">
                                <label>Full Name</label>
                                <input class="form-control input-lg" placeholder="Full Name" maxlength="24" id="user_fullname" name="user_fullname" type="text" value="<?php echo htmlspecialchars($user_fullname, ENT_QUOTES, 'UTF-8'); ?>" required="" />
                                <div class="invalid-feedback">This field is required.</div>
                            </div>
							<div class="form-group">
                                <label>User Name</label>
                                <input class="form-control input-lg" placeholder="User Name" maxlength="24" id="user_username" name="user_username" type="text" value="<?php echo htmlspecialchars($user_username, ENT_QUOTES, 'UTF-8'); ?>" required="" />
                                <div class="invalid-feedback">This field is required.</div>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input class="form-control input-lg" autocomplete="off" placeholder="Password" type="password" id="user_password" maxlength="20" name="user_password" required="" />
                                <div class="invalid-feedback">This field is required.</div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Create Admin Account</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="./assets/js/jquery-3.2.1.min.js"></script>
<script src="./assets/js/popper.min.js"></script>
<script src="./assets/js/bootstrap.min.js"></script>

</body>
</html>
