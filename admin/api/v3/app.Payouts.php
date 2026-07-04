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

    $payouts = new redeem($dbo);
    $result = $payouts->getPayouts();

    echo json_encode($result);
    exit;