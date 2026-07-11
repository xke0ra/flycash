<?php
include_once("core/init.inc.php");

    if (admin::isSession()) {

        header("Location: admin.php");
        exit;
    }
	
	$user_username = '';

    $error = false;
    $error_message = '';
	$configs = new functions($dbo);

    if (!empty($_POST)) {

        $user_username = isset($_POST['user_username']) ? $_POST['user_username'] : '';
        $user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
        $twofa_code = isset($_POST['twofa_code']) ? $_POST['twofa_code'] : '';

        $user_username = helper::clearText($user_username);
        $user_password = trim($user_password);

        if (!hash_equals((string)helper::getAuthenticityToken(), (string)$token)) {

            $error = true;
            $error_message = 'Some Error, Try Again';
        }

        if (!$error) {

            $access_data = array();

            $admin = new admin($dbo);
            $access_data = $admin->signin($user_username, $user_password);

            if ($access_data['error'] === false){

                $stmt = $dbo->prepare("SELECT twofa_enabled FROM admins WHERE id = :id LIMIT 1");
                $stmt->execute(array(':id' => $access_data['accountId']));
                $admRow = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($admRow && intval($admRow['twofa_enabled']) === 1) {
                    if (empty($twofa_code)) {
                        $error = true;
                        $error_message = '2FA code required.';
                    } else {
                        $stmt2 = $dbo->prepare("SELECT twofa_secret FROM admins WHERE id = :id LIMIT 1");
                        $stmt2->execute(array(':id' => $access_data['accountId']));
                        $secRow = $stmt2->fetch(PDO::FETCH_ASSOC);
                        if (!$secRow || !totp::verify($twofa_code, $secRow['twofa_secret'])) {
                            $error = true;
                            $error_message = 'Invalid 2FA code.';
                        }
                    }
                }

                if (!$error) {
                    $roleInfo = $admin->getRole($access_data['accountId']);
                    $rolePerms = $roleInfo ? $roleInfo['permissions'] : '';
                    admin::setSession($access_data['accountId'], admin::getAccessToken(), $user_username, $rolePerms);
                    header("Location: admin.php");
                    exit;
                }

            } else {
                $configs->logFailedAttempt($user_username);
                $error = true;
                $error_message = 'Incorrect login or password.';
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
    <meta name="theme-color" content="#6366f1">
    <title>Admin Login | <?php echo $configs->getConfig('APP_NAME'); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="logo">Admin Panel</div>
        <div class="auth-subtitle">Sign in to your <?php echo htmlspecialchars($configs->getConfig('APP_NAME'), ENT_QUOTES, 'UTF-8'); ?> Dashboard</div>
        
        <?php if ($error){ ?>
            <div class="alert alert-danger" aria-live="polite" role="alert"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php } ?>
        
        <form action="login.php" method="post" novalidate onsubmit="showLoading()">
            <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">
            <div class="form-group">
                <label>Username</label>
                <input class="form-control" placeholder="Enter your username" maxlength="24" id="user_username" name="user_username" type="text" value="<?php echo htmlspecialchars($user_username, ENT_QUOTES, 'UTF-8'); ?>" required aria-label="Username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input class="form-control" autocomplete="off" placeholder="Enter your password" type="password" id="user_password" maxlength="20" name="user_password" required aria-label="Password">
            </div>
            <div class="form-group">
                <label>2FA Code <span class="optional-note">(if enabled)</span></label>
                <input class="form-control" autocomplete="off" placeholder="6-digit code" type="text" id="twofa_code" name="twofa_code" maxlength="6" pattern="[0-9]{6}" aria-label="Two-factor authentication code">
            </div>
            <button type="submit" class="btn">Sign In</button>
        </form>
    </div>
</body>
</html>
