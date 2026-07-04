<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */

include_once("../api.inc.php");

if (!empty($_POST)) {

    $clientId = isset($_POST['clientId']) ? $_POST['clientId'] : 0;
    $email = isset($_POST['email']) ? $_POST['email'] : '';

    $clientId = helper::clearInt($clientId);
    $email = helper::escapeText($email);

    if ($clientId != CLIENT_ID) {

        api::printError(ERROR_UNKNOWN, "Error client Id.");
    }

    $result = array("error" => true, "error_code" => ERROR_UNKNOWN, "error_message" => "Incorrect email address");
    
    $configs = new functions($dbo);
    $email_status = $configs->sendPasswordResetEmail($email, $clientId);
    
    $result['error'] = $email_status['error'];
    $result['error_code'] = $email_status['error_code'];
    $result['error_message'] = $email_status['error_message'];

    echo json_encode($result);
    exit;
}
