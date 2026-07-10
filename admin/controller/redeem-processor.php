<?php
include_once("../core/init.inc.php");

    if (!account::isSession()) {

        // User Not Logged in
        $result = array('error' => true, 'error_code' => 101, 'error_description' => "Invalid Client Id");
        echo json_encode($result);
        exit;
        
    }else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $csrfToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!empty($csrfToken) && !helper::verifyCsrfToken($csrfToken)) {
            $result = array('error' => true, 'error_code' => 403, 'error_description' => "Invalid security token. Please refresh and try again.");
            echo json_encode($result);
            exit;
        }
        
	$configs = new functions($dbo);
	
	// All User Data
	$req_user_info = $configs->getUserInfo(account::getUserID());

    require_once ("Mobile_Detect.php");
    require_once ("detect.php");
    
    $id = isset($_REQUEST['orderId']) ? $_REQUEST['orderId'] : '0';
    $payoutTo = isset($_REQUEST['pos']) ? $_REQUEST['pos'] : 'Data Not Posted due to server issue - contact Developer';
	
	$dev_name = Detect::deviceType() .' - '.Detect::os();
	$dev_man = Detect::browser();
	
    $GetPdata = new redeem($dbo);
	$payoutData = $GetPdata->getSinglePayout($id);
	
	$payoutPoints = $payoutData['payout_pointsRequired'];
	
	$userId = account::getUserID();
	$userPoints = $req_user_info['points'];
	$username = $req_user_info['login'];
	
	if(isset($payoutData['error_code'])){
	    
	    // Invalid Payout Id - Hence, Malicious Activity
	    $result = array('error' => true, 'error_code' => 420, 'error_description' => "Malicious Activity");
	    
	// User Has the Required Points ?	
	}else if(isset($payoutData['payout_status']) && $payoutData['payout_status'] !== 'Active'){
	    
	    $result = array('error' => true, 'error_code' => 420, 'error_description' => "This reward is currently disabled");
	    
	}else if($userPoints < $payoutPoints){
	    
	    $result = array('error' => true, 'error_code' => 210, 'error_description' => "No Enough Points");
	
	}else if($userPoints >= $payoutPoints){
		
		$payout_title = $payoutData['payout_title'];
		$payout_amount = $payoutData['payout_amount'];
		
		try {
		    $dbo->beginTransaction();
		    
		    $stmt = $dbo->prepare("SELECT points FROM users WHERE id = :id LIMIT 1 FOR UPDATE");
		    $stmt->execute([':id' => $userId]);
		    $userRow = $stmt->fetch();
		    
		    if (!$userRow || (int)$userRow['points'] < $payoutPoints) {
		        $dbo->rollBack();
		        $result = array('error' => true, 'error_code' => 210, 'error_description' => "No Enough Points");
		    } else {
		        $st = $dbo->prepare("UPDATE users SET points = points - :delta WHERE id = :id AND points - :delta >= 0");
		        $st->execute([':delta' => $payoutPoints, ':id' => $userId]);
		        
		        if ($st->rowCount() === 0) {
		            $dbo->rollBack();
		            $result = array('error' => true, 'error_code' => 210, 'error_description' => "No Enough Points");
		        } else {
		            $timeCurrent = time();
		            $trackerSt = $dbo->prepare("INSERT INTO tracker (user_id, username, points, type, date) VALUES (:uid, :user, :points, :type, :time)");
		            $trackerSt->execute([':uid' => $userId, ':user' => $username, ':points' => -$payoutPoints, ':type' => 'Redeem', ':time' => $timeCurrent]);
		            
		            $time = date("Y-m-d", $timeCurrent);
                    $reqSt = $dbo->prepare("INSERT INTO redemptions(request_from, dev_name, dev_man, gift_name, req_amount, points_used, date, status, user_id, username) VALUES (:payoutTo, :dev_name, :dev_man, :payout_title, :payout_amount, :payoutPoints, :time, 'pending', :uid, :username)");
		            $reqSt->execute([':payoutTo' => $payoutTo, ':dev_name' => $dev_name, ':dev_man' => $dev_man, ':payout_title' => $payout_title, ':payout_amount' => $payout_amount, ':payoutPoints' => $payoutPoints, ':time' => $time, ':uid' => $userId, ':username' => $username]);
		            
		            $dbo->commit();
		            
		            $result = array('error' => true, 'error_code' => 100, 'error_description' => "Redeem Request Receievd");
		        }
		    }
		    
		} catch (\Exception $e) {
		    $dbo->rollBack();
		    \FlyCash\Logger::error("Redeem transaction failed (controller)", ['user' => $username, 'payout' => $id, 'error' => $e->getMessage()]);
		    $result = array('error' => true, 'error_code' => 911, 'error_description' => "Server Error - Please try again");
		}
		
	}else{
	    
	    $result = array('error' => true, 'error_code' => 108, 'error_description' => "Contact Developer - This should error not be shown");
	    
	}
	
	echo json_encode($result);
	
}else{
    
    // File Access Directly
    $result = array('error' => true, 'error_code' => 101, 'error_description' => "Invalid Client Id");
    
    echo json_encode($result);
}

?>