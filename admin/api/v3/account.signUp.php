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

    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    
    $profile_pic = isset($_POST['image']) ? $_POST['image'] : '';
    $refererCode = isset($_POST['referer']) ? $_POST['referer'] : 0;
    $reg_type = isset($_POST['reg']) ? $_POST['reg'] : 'Manual';

    $clientId = helper::clearInt($clientId);

    $username = helper::clearText($username);
    $username = helper::escapeText($username);

    $fullname = helper::clearText($fullname);
    $fullname = helper::escapeText($fullname);

    $password = helper::clearText($password);
    $password = helper::escapeText($password);

    $email = helper::clearText($email);
    $email = helper::escapeText($email);

    if ($clientId != CLIENT_ID) {

        api::printError(ERROR_UNKNOWN, "Error client Id.");
    }

    $result = array("error" => true);

    $account = new account($dbo);
    
    $result = $account->signup($username, $fullname, $password, $email, $refererCode, $profile_pic, $reg_type);
    unset($account);

    if ($result['error'] === false) {

        $account = new account($dbo);
        $result = $account->signin($username, $password);
        unset($account);

        if ($result['error'] === false) {

            $auth = new auth($dbo);
            $result = $auth->create($result['accountId'], $clientId);

            if ($result['error'] === false) {

                $account = new account($dbo, $result['accountId']);
                $result['account'] = array();
                $result['config'] = array();

                array_push($result['account'], $account->get());
                array_push($result['config'], $account->getConfigs());
            }
        }
    }

    echo json_encode($result);
    exit;
}
