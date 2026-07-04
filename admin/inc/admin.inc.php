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

        header("Location: index.php");
    }
	
	$configs = new functions($dbo);
	$analytics = new analytics($dbo);
    $stats = new stats($dbo);
    $requests = new requests($dbo);
	
	$calcPercent = $configs->getConfig('ADMIN_RATIO');
	$configs->updateConfigs(time(),'LAST_ADMIN_ACCESS');
	
	$totalUsers = $configs->getTotalUsers();
	$newUsers = $configs->getNewUsers();
	$oldUsers = $configs->getOldUsers();
	$todayActiveusers = $configs->getTodayActiveusers();
	
	$pendingRequests = $configs->getPendingRequests();
	$processingRequests = $configs->getProcessingRequests();
	$rejectedRequests = $configs->getRejectedRequests();
	$completedRequests = $configs->getCompletedRequests();
	$totalRequests = $pendingRequests + $processingRequests + $rejectedRequests + $completedRequests;
	
	$newPoints = $configs->getTotalTodayPoints();
	$oldPoints = $configs->getTotalYesterdayPoints();
	
	$totalProfit = $configs->getTotalAllTimePoints();
	$monthProfit = $configs->getTotalMonthPoints();
	$weekProfit = $configs->getTotalWeekPoints();
	
	$totalProfitFinal = round($totalProfit / $calcPercent, 2);
	$monthProfitFinal = round($monthProfit / $calcPercent, 2);
	$weekProfitFinal = round($weekProfit / $calcPercent, 2);
	
	$todayProfit = round($newPoints / $calcPercent , 2) ;
	
	$profitIncreased = false;
	$usersIncreased = false;
	$totalusersPercent = 0;
	$newusersPercent = 0;
	
	if($totalUsers >= 1){
		$totalusersPercent = round(($newUsers / $totalUsers) * 100 , 2);
	}
	
	if($newPoints >= $oldPoints){
		$profitIncreased = true;
	}
	
	$todayProfitPercent = round(($newPoints / $oldPoints) * 100 , 2);
	if($todayProfitPercent >= 100){
		$todayProfitPercent = $todayProfitPercent - 100;
	}else{
		$todayProfitPercent = 100 - $todayProfitPercent;
	}
	
	if($newUsers >= $oldUsers){
		$usersIncreased = true;
	}
		
	$newusersPercent = round(($newUsers / $oldUsers) * 100 , 2);
	if($newusersPercent >= 100){
		$newusersPercent = $newusersPercent - 100;
	}else{
		$newusersPercent = 100 - $newusersPercent;
	}
	
	$todaySessions = $analytics->getTodaySessions();
	$sessionsdata = $analytics->getAnalytics(0);
	
?>