<?php
class functions extends db_connect
{
    private $requestFrom = 0;

    private ?\FlyCash\Services\PointsService $pointsService = null;
    private ?\FlyCash\Services\SecurityService $securityService = null;
    private ?\FlyCash\Services\UserService $userService = null;
    private ?\FlyCash\Services\ConfigService $configService = null;
    private ?\FlyCash\Services\OfferwallService $offerwallService = null;
    private ?\FlyCash\Services\AnalyticsService $analyticsService = null;
    private ?\FlyCash\Services\NotificationService $notificationService = null;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    public function getPointsService(): \FlyCash\Services\PointsService
    {
        if ($this->pointsService === null) {
            $this->pointsService = new \FlyCash\Services\PointsService($this->db);
        }
        return $this->pointsService;
    }

    public function getSecurityService(): \FlyCash\Services\SecurityService
    {
        if ($this->securityService === null) {
            $this->securityService = new \FlyCash\Services\SecurityService($this->db);
        }
        return $this->securityService;
    }

    public function getUserService(): \FlyCash\Services\UserService
    {
        if ($this->userService === null) {
            $this->userService = new \FlyCash\Services\UserService($this->db);
        }
        return $this->userService;
    }

    public function getConfigService(): \FlyCash\Services\ConfigService
    {
        if ($this->configService === null) {
            $this->configService = new \FlyCash\Services\ConfigService($this->db);
        }
        return $this->configService;
    }

    public function getOfferwallService(): \FlyCash\Services\OfferwallService
    {
        if ($this->offerwallService === null) {
            $this->offerwallService = new \FlyCash\Services\OfferwallService($this->db);
        }
        return $this->offerwallService;
    }

    public function getAnalyticsService(): \FlyCash\Services\AnalyticsService
    {
        if ($this->analyticsService === null) {
            $this->analyticsService = new \FlyCash\Services\AnalyticsService($this->db);
        }
        return $this->analyticsService;
    }

    public function getNotificationService(): \FlyCash\Services\NotificationService
    {
        if ($this->notificationService === null) {
            $this->notificationService = new \FlyCash\Services\NotificationService($this->db);
        }
        return $this->notificationService;
    }
    
    public function getUserInfo($userId) {
        return $this->getUserService()->getUserInfo($userId);
    }
    
    public function getUserInfoByValue($field, $value) {
        return $this->getUserService()->getUserInfoByValue($field, $value);
    }
    
    public function updateUserAccess($userId) {
        return $this->getUserService()->updateUserAccess($userId);
    }
    
    public function getUserReferredMembers($refererCode)
    {
        return $this->getUserService()->getUserReferredMembers($refererCode);
    }
    
    public function getUserReferIncome($userId)
    {
        return $this->getUserService()->getUserReferIncome((int)$userId);
    }

    public function getUserRedeemedPoints($userId)
    {
        return $this->getUserService()->getUserRedeemedPoints((int)$userId);
    }

    public function getRequestsCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM redemptions");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }
    
    public function mask_payoutTo($payoutTo) {
        
        $result = "du******@**il.com";
        
        if (filter_var($payoutTo, FILTER_VALIDATE_EMAIL)) {
            
            $mail_segments = explode("@", $payoutTo);
            $mail_segments[0] = substr($payoutTo, 0, 2).str_repeat("*", strlen($mail_segments[0])-2);
            
            $result =  implode("@", $mail_segments);
           
        }else{
            
            //return substr($payoutTo, 0, -4) . "****";
            $len = strlen($payoutTo);
            
            $result = substr($payoutTo, 0, 2).str_repeat('*', $len - 4).substr($payoutTo, $len - 2, 2);
            
        }
        
        return $result;
    }

    public function calcPercent($amount,$type)
    {
		$percent = 0;
		
        if($amount < 100 && $type == "week"){
			
			$percent = $amount/1;
			
		}else if($amount < 200 && $type == "week"){
			
			$percent = $amount/2;
			
		}else if($amount < 500 && $type == "week"){
			
			$percent = $amount/5;
			
		}else if($amount < 800 && $type == "week"){
			
			$percent = $amount/8;
			
		}else if($amount < 1000){
			
			$percent = $amount/10;
			
		}else if($amount < 1500){
			
			$percent = $amount/15;
			
		}else if($amount < 2000){
			
			$percent = $amount/20;
			
		}else if($amount < 3000){
			
			$percent = $amount/30;
			
		}else if($amount < 4000){
			
			$percent = $amount/40;
			
		}else if($amount < 5000){
			
			$percent = $amount/50;
			
		}else if($amount < 7500){
			
			$percent = $amount/75;
			
		}else{
			
			$percent = $amount/100;
			
		}

        return $percent;
    }

    public function getTotalUsers()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM users");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getNewUsers()
    {
        return $this->getAnalyticsService()->getNewUsers();
    }

    public function getOldUsers()
    {
		$time = time();
        $oldtime = $time - 24 * 3600;
		$today = strtotime(date("d-m-Y", $time));
        $yesterday = strtotime(date("d-m-Y", $oldtime));
		$stmt = $this->db->prepare("SELECT count(*) FROM users where regtime BETWEEN :yesterday AND :today");
        $stmt->execute(array(':yesterday' => $yesterday, ':today' => $today));
		$number_of_rows = $stmt->fetchColumn();
		
		if($number_of_rows < 1){
			$number_of_rows = 1;
		}

        return $number_of_rows;
    }

    public function getTodayActiveusers()
    {
        return $this->getAnalyticsService()->getTodayActiveUsers();
    }

    public function getTotalTodayPoints()
    {
        return $this->getPointsService()->getTotalTodayPoints();
    }

    public function getTotalYesterdayPoints()
    {
        return $this->getPointsService()->getTotalYesterdayPoints();
    }

    public function getTotalAllTimePoints()
    {
        return $this->getPointsService()->getTotalAllTimePoints();
    }

    public function getTotalMonthPoints()
    {
        return $this->getPointsService()->getTotalMonthPoints();
    }

    public function getTotalWeekPoints()
    {
        return $this->getPointsService()->getTotalWeekPoints();
    }

    public function getCurrentTotalPoints() {
        return $this->getPointsService()->getCurrentTotalPoints();
    }

    public function getCompletedRequests()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM redemptions WHERE status = 'completed'");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }
	
    public function getPendingRequests()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM redemptions WHERE status = 'pending'");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getProcessingRequests()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM redemptions WHERE status = 'processing'");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getRejectedRequests()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM redemptions WHERE status = 'rejected'");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getConfig($value) {
        return $this->getConfigService()->get($value, '');
    }

    public function getAdminUserName() {
        $sql = "SELECT username FROM admins WHERE id = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $row = $stmt->fetchColumn();
    }
    
    public function updateAnalyticsSessions() {
		$result = false;
        $today = date("Y-m-d", time());
        $sql = "SELECT * FROM analytics WHERE date = :today LIMIT 1";
        $stmt = $this->db->prepare($sql);
		$stmt->execute(array(':today' => $today));
		$number_of_rows = $stmt->fetchColumn();
		
		if ($number_of_rows > 0) {

				$sql = "UPDATE analytics SET sessions = sessions+1 WHERE date = :today";
				$stmt = $this->db->prepare($sql);
				$result = $stmt->execute(array(':today' => $today));
            
			
        }else{
			$sql = "INSERT INTO analytics (date,sessions,requests,completed) value (:today, 1,0,0)";
			$stmt = $this->db->prepare($sql);
			$result = $stmt->execute(array(':today' => $today));
		}
		
        return $result;
    }

    public function isWhitelisted($ip)
    {
        return $this->getOfferwallService()->isWhitelisted($ip);
    }
    

    function updateConfigs($value, $configname) {
        $sql = "UPDATE configuration SET config_value = :value WHERE config_name = :configname";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array(':value' => $value, ':configname' => $configname));
    }

    function updateAdnetworksIds($configname, $value) {
        $sql = "UPDATE adnetworks_ids SET config_value = :value WHERE config_name = :configname";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array(':value' => $value, ':configname' => $configname));
    }
    
    public function completeofferStatusData($clickId){
        
        $stmt = $this->db->prepare("UPDATE offer_status SET status = '1' WHERE cid = (:clickId) LIMIT 1");
        $stmt->bindParam(":clickId", $clickId, PDO::PARAM_STR);
        
        return $stmt->execute();
        
    }

    public function getofferStatusData($clickId){
        
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM offer_status WHERE cid = (:clickId) LIMIT 1");
        $stmt->bindParam(":clickId", $clickId, PDO::PARAM_STR);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
                                "cid" => $row['cid'],
                                "user" => $row['user'],
                                "of_id" => $row['of_id'],
                                "of_title" => $row['of_title'],
                                "of_amount" => $row['of_amount'],
                                "of_url" => $row['of_url'],
                                "partner" => $row['partner'],
                                "date" => $row['date'],
                                "status" => $row['status']);
            }
        }

        return $result;
    }
    
    public function sendPasswordResetEmail($email, $clientId){
        
        $result = array("error" =>  true, "error_code" => 404, "error_message" => "Inavlid smtp settings");
        
        $helper = new helper($this->db);
        
        if (helper::isCorrectEmail($email)) {
            
            $userInfo = $this->getUserInfoByValue('email', $email);
            
            $accountId = isset($userInfo['id']) ? $userInfo['id'] : 0;
            
            if ($accountId != 0) {
                
                if ($userInfo['state'] != ACCOUNT_STATE_BLOCKED) {
                    
                    $account = new account($this->db, $accountId);
                    $restorePointInfo = $account->restorePointCreate($email, $clientId);
                    $hash = $restorePointInfo['hash'];
                    
                    $emails = new emails($this->db);
                    $email_result = $emails->sendPasswordResetEmail($userInfo['fullname'], $userInfo['email'], $hash);
                    
                    $result['error'] = $email_result['error'];
                    $result['error_code'] = $email_result['error_code'];
                    $result['error_message'] = $email_result['error_message'];
                    
                }else{
                    
                    $result['error'] = true;
                    $result['error_code'] = 420;
                    $result['error_message'] = 'Account is either disabled or Blocked.';
                }
                
            }else{
                
                $result['error'] = true;
                $result['error_code'] = 404;
                $result['error_message'] = 'No Account is associated with the provided email address.';
            }
            
        }else{
            
            $result['error'] = true;
            $result['error_code'] = ERROR_UNKNOWN;
            $result['error_message'] = 'Incorrect email address.';
        }
        
        return $result;
        
    }

    public function sendPush($fcm_id, $title, $message, $image, $type) {
		
		$GOOGLE_API_KEY = $this->getConfig("FIREBASE_API_KEY");
		
		$fields = array(
        	'to'		=> $fcm_id ,
		'priority'	=> "high",
		'data'		=> array("title" =>$title, "message" =>$message, "image"=> $image, "type"=> $type),
        );
		
        $headers = array('https://fcm.googleapis.com/fcm/send','Content-Type: application/json','Authorization: key='.$GOOGLE_API_KEY);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		
        $result = curl_exec($ch);
        if ($result === FALSE) {
            if (isset($GLOBALS['logger'])) {
            $GLOBALS['logger']->error('cURL error in sendPush', ['curl_error' => curl_error($ch)]);
        }
            curl_close($ch);
            return false;
        }
		
        curl_close($ch);
         //echo $result;
		 //exit;
		
        return $result;
    }
	
	public function getDailyCheckinTimeLeft($userId){
	    return $this->getUserService()->getDailyCheckinTimeLeft((int)$userId);
	}

	public function isIpBanned($ip) {
		return $this->getSecurityService()->isIpBanned($ip);
	}

	public function banIp($ip, $reason = 'Too many failed attempts', $durationMinutes = 15) {
		$this->getSecurityService()->banIp($ip, $reason, $durationMinutes);
	}

	public function logFailedAttempt($identifier) {
		$this->getSecurityService()->logFailedAttempt($identifier);
	}

	public function checkRateLimit($identifier, $action, $maxAttempts = 10, $windowSeconds = 60) {
		return $this->getSecurityService()->checkRateLimit($identifier, $action, $maxAttempts, $windowSeconds);
	}

	public function logAudit($adminId, $adminName, $action, $target = '', $details = '') {
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
		$time = time();
		$stmt = $this->db->prepare("INSERT INTO audit_log (admin_id, admin_name, action, target, details, ip_addr, created_at) VALUES (:admin_id, :admin_name, :action, :target, :details, :ip_addr, :created_at)");
		return $stmt->execute(array(
			':admin_id' => $adminId,
			':admin_name' => $adminName,
			':action' => $action,
			':target' => $target,
			':details' => $details,
			':ip_addr' => $ip,
			':created_at' => $time
		));
	}

	public function creditUserPoints($username, $points, $type, $description = '', $notify = true, $push = true) {
		return $this->getPointsService()->creditUserPoints($username, $points, $type, $description, $notify, $push);
	}

}

