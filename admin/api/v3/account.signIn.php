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
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $clientId = helper::clearInt($clientId);

    $username = helper::clearText($username);
    $username = helper::escapeText($username);

    $password = helper::clearText($password);
    $password = helper::escapeText($password);

    if ($clientId != CLIENT_ID) {

        api::printError(ERROR_UNKNOWN, "Error client Id.");
    }

    $access_data = array();

    $account = new account($dbo);
    $access_data = $account->signin($username, $password);

    unset($account);

    if ($access_data["error"] === false) {

        $auth = new auth($dbo);
        $access_data = $auth->create($access_data['accountId'], $clientId);

        if ($access_data['error'] === false) {

            $account = new account($dbo, $access_data['accountId']);
            $access_data['account'] = array();
            $access_data['config'] = array();

            array_push($access_data['account'], $account->get());
            array_push($access_data['config'], $account->getConfigs());
        }
    }

    echo json_encode($access_data);
    exit;
}
