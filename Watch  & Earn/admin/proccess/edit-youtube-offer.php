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
		
		$ID = $_POST['id'];
		$name = $_POST['title'];
		$subtitle = $_POST['sub'];
		$duration = $_POST['duration'];

		$image_name = isset($_POST['image_name']) ? $_POST['image_name'] : $ID."_offerwall_image.png";
		$points = isset($_POST['points']) ? $_POST['points'] : 0;
		$val1 = isset($_POST['val1']) ? $_POST['val1'] : "0000";

		$result = false;
		$offerwall_url = '';
		    $offerwall_url = $val1;
		
		$configs = new functions($dbo);
		

		require_once  "../core/class.imagehelper.php";
		
		$image = new Imagehelper\Image($_FILES);
			
		if($image["offer_image"]){
				
			$image->setSize(0, 5000000);
			$image->setMime(array('png', 'jpg','jpeg'));
			$image->setLocation("../images");
			
				$imageName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $image_name);
				$image->setName($imageName);
			
			$upload = $image->upload();
			
			if($upload){
				
				// update with image
				
				$urlfinal = $image->getName().'.'.$image->getMime();
    		    
    		    $sql = "UPDATE youtube SET title = '$name', subtitle = '$subtitle', url='$offerwall_url', duration='$duration', points = '$points', image = '$urlfinal' WHERE id = '$ID' ";
    			$stmt = $dbo->prepare($sql);
    			if($stmt->execute()){ $result = true; }
				
			}else{
				
				 //echo $image->getError();
				 
				 // update without image
		    
    		    $sql = "UPDATE youtube SET title = '$name', subtitle = '$subtitle', url='$offerwall_url', duration='$duration', points = '$points' WHERE id = '$ID' ";
    			$stmt = $dbo->prepare($sql);
    			
    			if($stmt->execute()){ $result = true; }
				
			}
		}
		
		if($result){
			
			header("Location: ../youtube-offers.php");
			
		}else{
			
			header("Location: ../youtube-offers.php");
		}
		
		
	}else{
		
		header("Location: ../youtube-offers.php");
		
	}
	
	
	

?>