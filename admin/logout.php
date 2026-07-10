<?php
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