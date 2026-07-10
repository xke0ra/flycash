<?php
include_once("../core/init.inc.php");

    if (!admin::isSession()) {

        header("Location: ../login.php");
		exit;
    }else if(!empty($_POST) && !APP_DEMO){
		
		if (!helper::verifyCsrfToken($_POST['csrf_token'] ?? '')) { header("Location: ../index.php"); exit; }
		
		$admin_name = $_POST['admin_name'];
		$admin_image_name = isset($_POST['admin_image_name']) ? $_POST['admin_image_name'] : "admin_image";
		
		$settings = new settings($dbo);
		$configs = new functions($dbo);
		
		$result = $configs->updateConfigs($admin_name,'ADMIN_NAME');
		$result = $settings->changeAdminName($admin_name, admin::getAdminID());
		
		require_once  "../core/class.imagehelper.php";
			
		$image = new Imagehelper\Image($_FILES);
			
		if($image["admin_image"]){
				
				
			$image->setSize(0, 5000000);
			$image->setMime(array('png', 'jpg','jpeg'));
			$image->setLocation("../images");
			
			if(!empty($admin_image_name)){
				$imageName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $admin_image_name);
				$image->setName($imageName);
			}else{
				$image->setName("admin_image");
			}
			
			$upload = $image->upload();
			
			if($upload){
				
				$result = $configs->updateConfigs($image->getName().'.'.$image->getMime(),'ADMIN_IMAGE');
				
			}else{
				
				// echo $image->getError();
				
			}
			
		}
		
		
		if($result){
			
			header("Location: ../profile.php");
		exit;
		}else{
			
			header("Location: ../profile.php");
		exit;
		}
		
	}else{
		
		header("Location: ../profile.php");
		exit;
	}
	
	
	

?>