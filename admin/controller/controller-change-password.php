<?php

    $error = false;
	$success = false;
	if(!empty($_POST)){
		
		$new_pass = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $cnf_pass = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        $new_pass = helper::clearText($new_pass);
        $cnf_pass = helper::clearText($cnf_pass);
        
        $new_pass = helper::escapeText($new_pass);
        $cnf_pass = helper::escapeText($cnf_pass);
        
		if($cnf_pass != $new_pass){
		    
		    $error = true;
			$error_message = "New Password & Confirm Password do not Match";
			
		}

        if (!$error) {

            $result = array();
            
            $userId = account::getUserID();
            
            $user = new account($dbo, $userId);
            
            $result = $user->changePassword($new_pass);

            if ($result['error'] === false){
                
                $success = true;
                $error_message = $result['error_description'];

            } else {

                $error = true;
                $error_message = $result['error_description'];
            }
        }
		
	}
	
?>