<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */
	
	include_once("../core/init.inc.php");

    if (!admin::isSession()) {

        header("Location: ../index.php");
		
    }else if(!empty($_POST) && !APP_DEMO){
		
		$name = $_POST['title'];
		$subtitle = $_POST['sub'];
		$duration = $_POST['duration'];
		$points = isset($_POST['points']) ? $_POST['points'] : 0;
		$custom_offerwall_url = isset($_POST['url']) ? $_POST['url'] : '';
		
		$type = 'custom_offerwall_'.helper::generateRandomString(6);
		
		$image_name = isset($_POST['image_name']) ? $_POST['image_name'] : $type."_image.png";
		$imageName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $image_name);
		
		$configs = new functions($dbo);
		
		require_once  "../core/class.imagehelper.php";
		
		$image = new Imagehelper\Image($_FILES);
			
		if($image["offer_image"]){
				
			$image->setSize(0, 5000000);
			$image->setMime(array('png', 'jpg','jpeg'));
			$image->setLocation("../images");
			$image->setName($imageName);
			
			$upload = $image->upload();
			
			if($upload){
				
				// image uploaded
				$imageName = $image->getName().'.'.$image->getMime();
				
			}else{
				 //echo $image->getError();
			}
		}
		
		$sql = "INSERT INTO youtube (title, subtitle, url, points, image, status,duration) VALUES ('$name', '$subtitle', '$custom_offerwall_url', '$points', '$imageName', '1','$duration')";
		$stmt = $dbo->prepare($sql);
		$stmt->execute();
		
	}
	
	header("Location: ../youtube-offers.php");
	exit();
	
	
	

?>