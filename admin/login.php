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
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --radius: 16px;
            --radius-sm: 10px;
            --shadow-xl: 0 20px 50px -12px rgba(0,0,0,.15);
            --font: 'Inter', sans-serif;
        }
        body {
            font-family: var(--font);
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .login-card {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            box-shadow: var(--shadow-xl);
            padding: 40px 32px;
            max-width: 420px;
            width: 100%;
        }
        .login-card .logo {
            font-size: 24px;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 4px;
            text-align: center;
        }
        .login-card .subtitle {
            color: var(--gray-500);
            font-size: 14px;
            text-align: center;
            margin-bottom: 32px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid var(--gray-200);
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-family: var(--font);
            transition: all .2s;
            background: var(--gray-50);
            outline: none;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
            background: #fff;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px 24px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 15px;
            font-weight: 600;
            font-family: var(--font);
            cursor: pointer;
            transition: all .2s;
            margin-top: 8px;
        }
        .btn:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 14px rgba(99,102,241,.35);
            transform: translateY(-1px);
        }
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: #fef2f2;
            color: #b91c1c;
            border-left: 4px solid #ef4444;
        }
        .invalid-feedback {
            display: none;
            font-size: 12px;
            color: #ef4444;
            margin-top: 4px;
        }
        .form-control:invalid ~ .invalid-feedback { display: block; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">Admin Panel</div>
        <div class="subtitle">Sign in to your <?php echo htmlspecialchars($configs->getConfig('APP_NAME'), ENT_QUOTES, 'UTF-8'); ?> Dashboard</div>
        
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
            <div class="form-group" style="margin-bottom:12px">
                <label>2FA Code <span style="font-weight:400;color:var(--gray-500)">(if enabled)</span></label>
                <input class="form-control" autocomplete="off" placeholder="6-digit code" type="text" id="twofa_code" name="twofa_code" maxlength="6" pattern="[0-9]{6}" aria-label="Two-factor authentication code">
            </div>
            <button type="submit" class="btn">Sign In</button>
        </form>
    </div>
</body>
</html>
