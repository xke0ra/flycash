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

    $offerwalls = new offerwalls($dbo);
    $result = $offerwalls->getOfferwalls();
    
    $offerwalls_loaded = count($result['offerwalls']);
    
    if ($offerwalls_loaded < 1) {
        
        api::printError(ERROR_UNKNOWN, "Server Not Responding");
        
    }

    echo json_encode($result);
    exit;