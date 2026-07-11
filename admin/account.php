<?php
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

	$configs = new functions($dbo);
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
		$user_password = trim($user_password);
		$user_password_repeat = trim($user_password_repeat);

		$user_username = helper::escapeText($user_username);
		$user_fullname = helper::escapeText($user_fullname);

		if (!hash_equals((string)helper::getAuthenticityToken(), (string)$token)) {

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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth.css" />
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="auth-title">Create Admin Account</div>
        <div class="auth-subtitle">Set up the initial administrator account to manage the <?php echo htmlspecialchars($configs->getConfig('APP_NAME'), ENT_QUOTES, 'UTF-8'); ?> platform.</div>
        
        <?php if ($error){ ?>
            <div class="alert alert-danger">
                <?php 
                foreach ($error_message as $msg) {
                    echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . "<br />";
                } ?>
            </div>
        <?php } ?>
        
        <form action="account.php" method="post" novalidate>
            <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">
            <div class="form-group">
                <label>Full Name</label>
                <input class="form-control" placeholder="Full Name" maxlength="24" id="user_fullname" name="user_fullname" type="text" value="<?php echo htmlspecialchars($user_fullname, ENT_QUOTES, 'UTF-8'); ?>" required />
            </div>
            <div class="form-group">
                <label>Username</label>
                <input class="form-control" placeholder="Username" maxlength="24" id="user_username" name="user_username" type="text" value="<?php echo htmlspecialchars($user_username, ENT_QUOTES, 'UTF-8'); ?>" required />
            </div>
            <div class="form-group">
                <label>Password</label>
                <input class="form-control" autocomplete="off" placeholder="Password" type="password" id="user_password" maxlength="20" name="user_password" required />
            </div>
            <button type="submit" class="btn btn-primary">Create Admin Account</button>
        </form>
    </div>
</body>
</html>
