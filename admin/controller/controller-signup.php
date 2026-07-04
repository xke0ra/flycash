<?php

    if (account::isSession()) {

        header("Location: index.php");
        exit;
    }
	
	$fullname = '';
	$username = '';
	$email = '';
	$confirm_email = '';
	$referer = '';
	
	function esc_attr($attr){ return htmlspecialchars($attr, ENT_COMPAT, 'UTF-8'); }
	
	$_SESSION["refererCode"] = isset($_SESSION["refererCode"]) ? $_SESSION["refererCode"] : '';

    $success = false;
    $success_message = '';
    
	$error = isset($_SESSION['login_error']) ? $_SESSION['login_error']: false;
	$error_message = isset($_SESSION['login_message']) ? $_SESSION['login_message']: '';
	$_SESSION['login_error'] = false;
	$_SESSION['login_message'] = '';
	
	$configs = new functions($dbo);
	
	$APP_NAME = $configs->getConfig('APP_NAME');
	$APP_DESC = $configs->getConfig('APP_DESC');

    if (!empty($_POST)) {

        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $confirm_email = isset($_POST['confirm_email']) ? $_POST['confirm_email'] : '';
        $referer = isset($_POST['referer']) ? $_POST['referer'] : '';
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $username = helper::clearText($username);
        $fullname = helper::clearText($fullname);
        $email = helper::clearText($email);
        $confirm_email = helper::clearText($confirm_email);
        $referer = helper::clearText($referer);
        $password = helper::clearText($password);

        $username = helper::escapeText($username);
        $fullname = helper::escapeText($fullname);
        $email = helper::escapeText($email);
        $confirm_email = helper::escapeText($confirm_email);
        $referer = helper::escapeText($referer);
        $password = helper::escapeText($password);

        if (helper::getAuthenticityToken() !== $token) {

            $error = true;
            $error_message = 'Some Error, Try Again';
        }
        
        if($confirm_email != $email){
            
            $error = true;
            $error_message = 'Email & Confirm Email are Different';
        }

        if (!$error) {

            $access_data = array();
            
            $user = new account($dbo);
            
            $access_data = $user->signup($username, $fullname, $password, $email, $referer, 'person-placeholder.jpeg', 'Manual');

            if ($access_data['error'] === false){
                
                $success = true;
                $success_message = $access_data['error_description'];

            } else {

                $error = true;
                $error_message = $access_data['error_description'];
            }
        }
    }

    helper::newAuthenticityToken();
    
?>