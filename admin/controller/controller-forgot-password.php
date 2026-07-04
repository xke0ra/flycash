<?php

    if (account::isSession()) {

        header("Location: index.php");
        exit;
    }
	
	$user_username = '';

    $error = false;
    $success = false;
    $error_message = '';
	$configs = new functions($dbo);
	
	$APP_NAME = $configs->getConfig('APP_NAME');
	$APP_DESC = $configs->getConfig('APP_DESC');
	
	$email = '';
	
	function esc_attr($attr){ return htmlspecialchars($attr, ENT_COMPAT, 'UTF-8'); }

    if (!empty($_POST)) {

        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $email = helper::clearText($email);

        $email = helper::escapeText($email);

        if (helper::getAuthenticityToken() !== $token) {

            $error = true;
            $error_message = 'Some Error, Try Again';
        }

        if (!$error) {
            
            $clientId = 0; // Web Version
            $email_status = $configs->sendPasswordResetEmail($email, $clientId);
            
            if($email_status['error'] === false){
                
                $success = true;
                $error_message = $email_status['error_message'];
                
            }else{
                
                $error = $email_status['error'];
                $error_message = $email_status['error_message'];
            }
            
        }
    }

    helper::newAuthenticityToken();