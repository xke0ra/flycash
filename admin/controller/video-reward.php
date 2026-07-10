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
        $ID = $_POST['id'];
        
		$configs = new functions($dbo);
        $offerwalls = new offerwalls($dbo);
        $result = $offerwalls->getSingleYoutubeOffer($ID);
        
        $account = new account($dbo, $accountId);
        $notify = new functions($dbo);
        $userdata = $account->get();
        $timeCurrent = time();
        $checkinReward = $result['offer_points'];
        $checkinBonusTitle = "Youtube Video Reward";

        
        $rewardUser = false;
        
        $user = $userdata['username'];
        
        $sql = "SELECT * FROM watched_video WHERE user_id = :accountId AND video_id = :ID ORDER BY id DESC LIMIT 1";
        $stmt = $dbo->prepare($sql);
        $stmt->execute(array(':accountId' => $accountId, ':ID' => $ID));
        
        if ($stmt->rowCount() > 0) {
            
            // old User
            

                $rewardUser = false;
                //api::printError(410, $diff);
                $result = array('error' => false, 'error_code' => 420, 'error_description' => "This Youtube Reward has been Taken Already");
                
                echo json_encode($result);
                exit;
                

        }else{
            
            // New User
            $rewardUser = true;
            
        }
        
        $newBalance = $userdata['points'] + $checkinReward;
        
        // Updating user Points
        $sql = "UPDATE users SET points = :newBalance WHERE id = :id";
        $stmt = $dbo->prepare($sql);
        $stmt->execute(array(':newBalance' => $newBalance, ':id' => $accountId));
        
        // Updating user Tracker
        $sql = "INSERT INTO tracker(user_id, username, points, type, date) values (:uid, :user, :checkinReward, :checkinBonusTitle, :timeCurrent)";
        $stmt = $dbo->prepare($sql);
        
        if($stmt->execute(array(':uid' => $accountId, ':user' => $user, ':checkinReward' => $checkinReward, ':checkinBonusTitle' => $checkinBonusTitle, ':timeCurrent' => $timeCurrent))){
            
            $notif2 = new notifications($dbo);
            $notif2->add($accountId, 'Video Reward', 'You earned '.$checkinReward.' points from watching a video', $checkinReward);
            $notify->sendPush($userdata['gcm'], "credit", $checkinReward, "none", "none");
        
        $sql = "INSERT INTO watched_video(user_id, video_id) values (:accountId, :ID)";
        $stmt = $dbo->prepare($sql);
        $stmt->execute(array(':accountId' => $accountId, ':ID' => $ID));

        $result = array('error' => false, 'error_code' => 100, 'error_description' => "Youtube Video Reward Credited Successfully.");
        
        echo json_encode($result);
        exit;
        
        }else{ api::printError(404, "Server Error"); }
        
    }else{
        
        // File Access Directly
        echo json_encode($result);
        exit();
    }
    
?>