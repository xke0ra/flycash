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
        
        $user = new account($dbo, $userId);
        
        if($req_user_info['email'] === $email){
            
            $result = $user->updateAccount($name, $email, $mobile, false);
            
        }else{
            
            $result = $user->updateAccount($name, $email, $mobile, true);
            
        }
        
        if ($result['error'] === false){
            
            $success = true;
            $error_message = $result['error_description'];
            
        	// Get User's latest Data
        	$req_user_info = $configs->getUserInfo(account::getUserID());
            
        } else {
            
            $error = true;
            $error_message = $result['error_description'];
            
        }
        
		
	}