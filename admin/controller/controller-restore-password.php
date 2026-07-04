<?php
	
	if (admin::isSession()) {

        header("Location: ../admin.php");
        exit;
        
    }else if (account::isSession()) {

        header("Location: ../../dashboard/index.php");
        exit;
    }
    
    if (isset($_GET['hash'])) {

        $hash = isset($_GET['hash']) ? $_GET['hash'] : '';

        $hash = helper::clearText($hash);
        $hash = helper::escapeText($hash);

        $restorePointInfo = $helper->getRestorePoint($hash);

        if ($restorePointInfo['error'] !== false) {
            
            header("Location: ../../dashboard/index.php");
            exit;
        }

    } else {

        header("Location: ../../dashboard/index.php");
        exit;
    }
    
    $error = false;
    $success = false;
    $error_message = '';
    
    $user_password = '';
    $user_password_repeat = '';
    
	$configs = new functions($dbo);
	$APP_NAME = $configs->getConfig('APP_NAME');
	$APP_DESC = $configs->getConfig('APP_DESC');
	
    if (!empty($_POST)) {

        $error = false;

        $user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';
        $user_password_repeat = isset($_POST['user_password_repeat']) ? $_POST['user_password_repeat'] : '';
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $user_password = helper::clearText($user_password);
        $user_password_repeat = helper::clearText($user_password_repeat);

        $user_password = helper::escapeText($user_password);
        $user_password_repeat = helper::escapeText($user_password_repeat);

        if (helper::getAuthenticityToken() !== $token) {

            $error = true;
            $error_message = 'Some Error, Try Again';
            
        }elseif (!helper::isCorrectPassword($user_password)) {

            $error = true;
            $error_message = 'Incorrect password.';
            
        }elseif ($user_password !== $user_password_repeat) {

            $error = true;
            $error_message = 'Passwords do not match.';
        }

        if (!$error) {

            $account = new account($dbo, $restorePointInfo['accountId']);

            $account->newPassword($user_password);
            $account->restorePointRemove();

            $success = true;
            $error_message = "Your Password has been changed sucessfully.";
        }
    }

    helper::newAuthenticityToken();