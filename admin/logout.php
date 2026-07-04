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

    if (!admin::isSession()) {

        header('Location: ../');
    }

    if (isset($_GET['access_token'])) {

        $accessToken = (isset($_GET['access_token'])) ? ($_GET['access_token']) : '';

        if (admin::getAccessToken() === $accessToken) {

            admin::unsetSession();

            header('Location: ../');
            exit;
        }
    }

    header('Location: ../');