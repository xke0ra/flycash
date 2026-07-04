<?php

    /*!
	 * POCKET v3.7
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */

include_once("../api.inc.php");

if (!empty($_POST)) {
    
    $country = isset($_POST['country']) ? $_POST['country'] : "us";
    $uid = isset($_POST['uid']) ? $_POST['uid'] : "none";
    $device = isset($_POST['device']) ? $_POST['device'] : "none";
    $ip = $_SERVER['REMOTE_ADDR'];
    $apiConfig = new functions($dbo);
    
    $pubId = $apiConfig->getConfig('AdMantum_PubId');
    $appId = $apiConfig->getConfig('AdMantum_AppId');
    $secretkey = $apiConfig->getConfig('AdMantum_SecretKey');
    
    $URL = "https://admantum.com/api/v1/offers/?appid=".$appId."&uid=".$uid."&ip=".$ip."&country=".$country."&device=".$device;
    
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_NOBODY, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	$result = curl_exec($ch);
	$curl_error = curl_errno($ch);
	curl_close($ch);
    
    echo $result;
    exit;
}

?>