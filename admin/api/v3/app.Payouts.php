<?php
include_once("../api.inc.php");

    $payouts = new redeem($dbo);
    $result = $payouts->getPayouts();

    echo json_encode($result);
    exit;