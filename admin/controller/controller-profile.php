<?php
    
	$error = false;
	$success = false;
	
	if(!empty($_POST)){
		
		$name = isset($_POST['full_name']) ? $_POST['full_name'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';

        $name = helper::clearText($name);
        $email = helper::clearText($email);
        $mobile = helper::clearText($mobile);
        
        $name = helper::escapeText($name);
        $email = helper::escapeText($email);
        $mobile = helper::escapeText($mobile);
        
        $result = array();
        
        $userId = account::getUserID();
        
        $profileService = \FlyCash\Container::get(\FlyCash\Services\ProfileService::class);
        
        $needsNewEmail = ($req_user_info['email'] !== $email);
        $updated = $profileService->updateAccount($userId, $name, $email, $mobile, $needsNewEmail ? $email : '');
        
        if ($updated){
            
            $success = true;
            $error_message = "Profile updated successfully.";
            
        	// Get User's latest Data
        	$req_user_info = $profileService->get($userId);
            
        } else {
            
            $error = true;
            $error_message = "Profile update failed.";
            
        }
        
		
	}