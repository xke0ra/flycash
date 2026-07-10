<?php
include_once("../core/init.inc.php");
	
	// Default Result
	$result = array('error' => true, 'error_code' => 101, 'error_description' => "Invalid Client Id");
    
    if (!account::isSession()) {

        // User Not Logged in
        echo json_encode($result);
        exit;
        
    }else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $accountId = account::getUserID();
        
        $account = new account($dbo, $accountId);
        $notify = new functions($dbo);
        $userdata = $account->get();
        $timeCurrent = time();
        $checkinReward = $notify->getConfig('DAILY_REWARD');
        $checkinBonusTitle = $notify->getConfig('CHECKIN_BONUS_TITLE');
        
        $rewardUser = false;
        
        $sql = "SELECT * FROM tracker WHERE user_id = :uid AND type = :checkinBonusTitle ORDER BY id DESC LIMIT 1";
        $stmt = $dbo->prepare($sql);
        $stmt->execute(array(':uid' => $accountId, ':checkinBonusTitle' => $checkinBonusTitle));
        
        if ($stmt->rowCount() > 0) {
            
            // old User
            
            $row = $stmt->fetch();
            
            // "1539068949"
            $timeData = $row['date'];
            
            $timeCalculated = $timeData + 24 * 3600;
            
            $diff = $timeCalculated - $timeCurrent;
            
            if($timeCalculated > $timeCurrent){
                
                $rewardUser = false;
                //api::printError(410, $diff);
                $result = array('error' => false, 'error_code' => 420, 'error_description' => "Daily Checkin Reward Taken Already");
                
                echo json_encode($result);
                exit;
                
            }else{
                
                $rewardUser = true;
            }
            
        }else{
            
            // New User
            $rewardUser = true;
            
        }
        
        if ($notify->creditUserPoints($userdata['username'], $checkinReward, $checkinBonusTitle, 'You earned '.$checkinReward.' points from daily check-in')) {
            
            $result = array('error' => false, 'error_code' => 100, 'error_description' => "Daily Checkin Reward Credited Successfully.");
            
            echo json_encode($result);
            exit;
            
        } else {
            
            api::printError(404, "Server Error");
            
        }
        
    }else{
        
        // File Access Directly
        echo json_encode($result);
        exit();
    }
    
?>