<?php
class account extends db_connect
{

    private $id = 0;

    public function __construct($dbo = NULL, $accountId = 0)
    {
        
        parent::__construct($dbo);
        
        $this->setId($accountId);
    }

    public function signup($username, $fullname, $password, $email, $refererCode = 0, $profile_pic = '', $reg_type = '')
    {

        $result = array("error" => true, "error_description" => "Serious issue, contact developer");

        $helper = new helper($this->db);

        if (empty($fullname)) {

            $result = array("error" => true,
                "error_code" => ERROR_UNKNOWN,
                "error_type" => 3,
                "error_description" => "Empty user full name");

            return $result;
        }

        if (!helper::isCorrectLogin($username)) {

            $result = array("error" => true,
                "error_code" => ERROR_UNKNOWN,
                "error_type" => 0,
                "error_description" => "Incorrect Username");

            return $result;
        }

        if ($helper->isLoginExists($username)) {

            $result = array("error" => true,
                "error_code" => ERROR_LOGIN_TAKEN,
                "error_type" => 0,
                "error_description" => "Username already taken");

            return $result;
        }

        if (!helper::isCorrectEmail($email)) {

            $result = array("error" => true,
                "error_code" => ERROR_UNKNOWN,
                "error_type" => 2,
                "error_description" => "Wrong email");

            return $result;
        }

        if ($helper->isEmailExists($email)) {

            $result = array("error" => true,
                "error_code" => ERROR_EMAIL_TAKEN,
                "error_type" => 2,
                "error_description" => "Email is already registered");

            return $result;
        }

        if (!helper::isCorrectPassword($password)) {

            $result = array("error" => true,
                "error_code" => ERROR_UNKNOWN,
                "error_type" => 1,
                "error_description" => "Incorrect password");

            return $result;
        }
        
        $ip_addr = helper::ip_addr();
        
        $configs = new functions($this->db);
        
        $is_oaod_enable = $configs->getConfig('OAOD_CHECK');
        
        if ($is_oaod_enable && $helper->isIpExists($ip_addr)) {

            $result = array("error" => true,
                "error_code" => ERROR_IP_TAKEN,
                "error_type" => 4,
                "error_description" => "This Device is already registered, only one Account for one device !");

            return $result;
        }

        $salt = '';
        $refer = helper::generateRandomString();
        $passw_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $currentTime = time();

        
        $accountState = ACCOUNT_STATE_ENABLED;

		$query = "INSERT INTO users (last_access, last_ip_addr, gcm_regid, state, fullname, salt, passw, login, email, image, regtime, regtype, ip_addr, mobile, points, refer, refered, referer) VALUES (:currentTime, :ip_addr, NULL, :accountState, :fullname, :salt, :passw_hash, :username, :email, :profile_pic, :currentTime2, :reg_type, :ip_addr2, '', '0', :refer, '0', '')";
        
		$stmt = $this->db->prepare($query);
		$createuser = $stmt->execute(array(
			':currentTime' => $currentTime,
			':ip_addr' => $ip_addr,
			':accountState' => $accountState,
			':fullname' => $fullname,
			':salt' => $salt,
			':passw_hash' => $passw_hash,
			':username' => $username,
			':email' => $email,
			':profile_pic' => $profile_pic,
			':currentTime2' => $currentTime,
			':reg_type' => $reg_type,
			':ip_addr2' => $ip_addr,
			':refer' => $refer
		));
        
        if ($createuser) {

            $this->setId($this->db->lastInsertId());

            $result = array("error" => false,
                            'accountId' => $this->id,
                            'username' => $username,
                            'error_code' => ERROR_SUCCESS,
                            'error_description' => 'SignUp Success!');
                            
                            
            if($refererCode !== '0' && $refererCode !== ''){
                
                $referdata = $this->getreferer($refererCode);
                
                $rererUserName = isset($referdata['username']) ? $referdata['username'] : '0';
                
                if($rererUserName !== '0'){
                    
                    $notify = new functions($this->db);
                    
                    $referReward = $notify->getConfig('REFER_REWARD');
                    $referBonusTitle = $notify->getConfig('REFERAL_BONUS_TITLE');
                    $refererBonusTitle = $notify->getConfig('REFERER_BONUS_TITLE');
                    
                    $refererUserId = (int)$referdata['id'];
                    
                    // Set referer tracking on new user, then credit via PointsService (atomic)
                    $sql = "UPDATE users SET referer = :refererCode, refered = '1' WHERE id = :id";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute(array(':refererCode' => $refererCode, ':id' => $this->id));
                    
                    // Credit referrer's bonus atomically via PointsService (no push to avoid abort risk)
                    $pointsService = new \FlyCash\Services\PointsService($this->db);
                    $pointsService->creditUserPointsByUserId($refererUserId, (int)$referReward, (string)$referBonusTitle, 'Referral bonus for referring ' . $username, true, false);
                    
                    // Credit new user's referral bonus atomically via PointsService
                    $pointsService->creditUserPoints($username, (int)$referReward, (string)$refererBonusTitle, 'You earned ' . $referReward . ' points as a signup bonus', true, false);
                    
                    // Manually send push to referrer (only after both credits succeeded)
                    if (!empty($referdata['gcm'])) {
                        $notify->sendPush($referdata['gcm'], "referer", $referReward, "none", "none");
                    }
                    
                    // Invitation Bonus Added
                    $result['error_description'] = "SignUp Success, Invitation Bonus Added !";
                    
                }else{
                    
                    // Invalid Refer Code
                    $result['error_description'] = "SignUp Success, Invitation Bonus Not Added - Invalid Referer !";
                    
                }
            }

            return $result;
        }

        return $result;
    }

    public function isEmailVerified($accountId)
    {
        $stmt = $this->db->prepare("SELECT email_verified FROM users WHERE id = :id LIMIT 1");
        $stmt->bindParam(":id", $accountId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && intval($row['email_verified']) === 1;
    }

    public function sendVerificationEmail($accountId)
    {
        $stmt = $this->db->prepare("SELECT id, login, email, email_verified FROM users WHERE id = :id LIMIT 1");
        $stmt->bindParam(":id", $accountId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || intval($user['email_verified']) === 1) {
            return array("error" => true, "error_code" => ERROR_UNKNOWN, "error_description" => "Email already verified.");
        }

        $hash = bin2hex(random_bytes(32));
        $currentTime = time();
        $expiresAt = $currentTime + 86400;

        $stmt = $this->db->prepare("INSERT INTO email_verify (accountId, email, hash, createAt, expiresAt) VALUES (:aid, :email, :hash, :ca, :ea)");
        $stmt->execute(array(':aid' => $accountId, ':email' => $user['email'], ':hash' => $hash, ':ca' => $currentTime, ':ea' => $expiresAt));

        $configs = new functions($this->db);
        $appName = $configs->getConfig('APP_NAME');
        $webRoot = $configs->getConfig('WEB_ROOT');
        $verifyLink = $webRoot . 'admin/api/v4/auth/verify?hash=' . $hash;

        $mailer = new emails($this->db);
        return $mailer->sendVerificationEmail($user['login'], $user['email'], $verifyLink, $appName);
    }

    public function verifyEmail($hash)
    {
        $hash = helper::clearText($hash);
        $hash = helper::escapeText($hash);

        $stmt = $this->db->prepare("SELECT id, accountId, expiresAt FROM email_verify WHERE hash = :hash AND used = 0 LIMIT 1");
        $stmt->execute(array(':hash' => $hash));

        if ($stmt->rowCount() === 0) {
            return array("error" => true, "error_code" => ERROR_UNKNOWN, "error_description" => "Invalid verification link.");
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (time() > intval($row['expiresAt'])) {
            return array("error" => true, "error_code" => ERROR_UNKNOWN, "error_description" => "Verification link expired.");
        }

        $stmt = $this->db->prepare("UPDATE email_verify SET used = 1 WHERE id = :id");
        $stmt->execute(array(':id' => $row['id']));

        $stmt = $this->db->prepare("UPDATE users SET email_verified = 1 WHERE id = :id");
        $stmt->execute(array(':id' => $row['accountId']));

        return array("error" => false, "error_code" => ERROR_SUCCESS, "error_description" => "Email verified successfully.");
    }

    public function signin($username, $password)
    {
        $access_data = array('error' => true);

        $username = helper::clearText($username);
        $password = trim($password);
        
        if (helper::isCorrectEmail($username)) {
            
            $type = "email";
            
        }else{
            $type = "login";
            
        }
        
        $sql = "SELECT id, salt, passw, state, email_verified FROM users WHERE ".$type." = (:username) LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();

            $isDev = (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development');
            if (!$isDev && intval($row['email_verified']) === 0) {
                $access_data = array("error" => true,
                                     "error_code" => 403,
                                     "error_description" => "Email not verified. Please check your inbox.");
                return $access_data;
            }

            if ((int)$row['state'] !== (defined('ACCOUNT_STATE_ENABLED') ? ACCOUNT_STATE_ENABLED : 0)) {
                $access_data = array("error" => true,
                                     "error_code" => 403,
                                     "error_description" => "This account is not active. Please contact support.");
                return $access_data;
            }

            if (strpos($row['passw'], '$2y$') === 0 || strpos($row['passw'], '$2a$') === 0) {

                if (password_verify($password, $row['passw'])) {
                    $access_data = array("error" => false,
                                         "error_code" => ERROR_SUCCESS,
                                         "accountId" => $row['id']);
                }

            } else {

                $passw_hash = md5(md5($password).$row['salt']);

                if ($passw_hash === $row['passw']) {

                    $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);


                    $upd = $this->db->prepare("UPDATE users SET passw = :hash WHERE id = :id");
                    $upd->execute(array(':hash' => $newHash, ':id' => $row['id']));

                    $access_data = array("error" => false,
                                         "error_code" => ERROR_SUCCESS,
                                         "accountId" => $row['id']);
                }
            }
        }

        return $access_data;
    }

    public function logout($accountId, $accessToken)
    {
        $auth = new auth($this->db);
        $auth->remove($accountId, $accessToken);
    }

    public function newPassword($password)
    {
        $newSalt = '';
        $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $this->db->prepare("UPDATE users SET passw = (:newHash), salt = (:newSalt) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":newHash", $newHash, PDO::PARAM_STR);
        $stmt->bindParam(":newSalt", $newSalt, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function changePassword($password)
    {
        $result = array("error" => false,
                            'error_code' => ERROR_UNKNOWN,
                            "error_type" => 1,
                            'error_description' => 'There was an issue changing password, please try again.');
                          
        if (!helper::isCorrectPassword($password)) {

            $result = array("error" => true,
                "error_code" => ERROR_UNKNOWN,
                "error_type" => 1,
                "error_description" => "Invalid password Length or Format, please choose different password.");

            return $result;
        }
        
        $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $this->db->prepare("UPDATE users SET passw = (:newHash), salt = '' WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":newHash", $newHash, PDO::PARAM_STR);
        
        if($stmt->execute()){
            
            $result = array("error" => false,
                            'error_code' => ERROR_SUCCESS,
                            'error_description' => 'Your Password has been Changed Successfully.');
        }
        
        return $result;
    }
    
    public function updateAccount($fullname, $email, $mobile, $newEmail){
        
        $result = array("error" => false,
                            'error_code' => ERROR_UNKNOWN,
                            "error_type" => 1,
                            'error_description' => 'There was an issue updating your profile, please try again.');
                            
        $helper = new helper($this->db);
        
        if (empty($fullname)) {

            $result = array("error" => true,
                "error_code" => ERROR_UNKNOWN,
                "error_type" => 3,
                "error_description" => "Empty user full name");

            return $result;
        }
        
        // INcase new Email
        if($newEmail){
            
            if (!helper::isCorrectEmail($email)) {
    
                $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN,
                    "error_type" => 2,
                    "error_description" => "Wrong email format, choose a different email address.");
    
                return $result;
            }
    
            if ($helper->isEmailExists($email)) {
    
                $result = array("error" => true,
                    "error_code" => ERROR_EMAIL_TAKEN,
                    "error_type" => 2,
                    "error_description" => "Email is already registered with us, choose a different email address");
    
                return $result;
            }
            
        }
    

        $stmt = $this->db->prepare("UPDATE users SET fullname = (:name), email = (:email), mobile = (:mobile) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":name", $fullname, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":mobile", $mobile, PDO::PARAM_STR);
        
        if($stmt->execute()){
            
            $result = array("error" => false,
                            'error_code' => ERROR_SUCCESS,
                            'error_description' => 'Your Profile has been Updated Successfully.');
        }
        
        return $result;
        
    }

    public function restorePointCreate($email, $clientId)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $restorePointInfo = $this->restorePointInfo();

        if ($restorePointInfo['error'] === false) {

            return $restorePointInfo;
        }

        $currentTime = time();
        $expiresAt = $currentTime + 3600;

        $hash = bin2hex(random_bytes(32));

        $stmt = $this->db->prepare("INSERT INTO restore_data (accountId, hash, email, removeAt, expiresAt) VALUES (:accountId, :hash, :email, 0, :expiresAt)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":hash", $hash, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":expiresAt", $expiresAt, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS,
                            'accountId' => $this->id,
                            'hash' => $hash,
                            'email' => $email);
        }

        return $result;
    }

    public function restorePointInfo()
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("SELECT * FROM restore_data WHERE accountId = (:accountId) AND removeAt = 0 AND (expiresAt = 0 OR expiresAt > :now) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $now = time();
        $stmt->bindParam(":now", $now, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS,
                            'accountId' => $row['accountId'],
                            'hash' => $row['hash'],
                            'email' => $row['email']);
        }

        return $result;
    }

    public function restorePointRemove()
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $removeAt = time();

        $stmt = $this->db->prepare("UPDATE restore_data SET removeAt = (:removeAt) WHERE accountId = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":removeAt", $removeAt, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS);
        }

        return $result;
    }

    public function setState($accountState)
    {

        $stmt = $this->db->prepare("UPDATE users SET state = (:accountState) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":accountState", $accountState, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function remAccount($accountId)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getState()
    {
        $stmt = $this->db->prepare("SELECT state FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['state'];
        }

        return 0;
    }

    public function get()
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
                                "last_access" => $row['last_access'],
                                "last_ip_addr" => $row['last_ip_addr'],
                                "gcm" => $row['gcm_regid'],
                                "state" => $row['state'],
                                "fullname" => stripcslashes($row['fullname']),
                                "username" => $row['login'],
                                "email" => $row['email'],
                                "regtime" => $row['regtime'],
                                "ip_addr" => $row['ip_addr'],
                                "mobile" => $row['mobile'],
                                "points" => $row['points'],
                                "refer" => $row['refer'],
                                "refered" => $row['refered']);
            }
        }

        return $result;
    }

    public function getreferer($refererCode)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM users WHERE refer = (:refercode) LIMIT 1");
        $stmt->bindParam(":refercode", $refererCode, PDO::PARAM_STR);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
                                "last_access" => $row['last_access'],
                                "last_ip_addr" => $row['last_ip_addr'],
                                "gcm" => $row['gcm_regid'],
                                "state" => $row['state'],
                                "fullname" => stripcslashes($row['fullname']),
                                "username" => $row['login'],
                                "email" => $row['email'],
                                "regtime" => $row['regtime'],
                                "ip_addr" => $row['ip_addr'],
                                "mobile" => $row['mobile'],
                                "points" => $row['points'],
                                "refer" => $row['refer'],
                                "refered" => $row['refered']);
            }
        }

        return $result;
    }

    public function getOldRefersData($userId)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM referers WHERE user_id = :userId LIMIT 1");
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $result = array("id" => $row['id'],
                                "username" => $row['username'],
                                "referer" => $row['referer'],
                                "points" => $row['points'],
                                "type" => $row['type'],
                                "date" => $row['date']);
            }
        }

        return $result;
    }
    
    public function getuserdata($username)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM users WHERE login = (:username) LIMIT 1");
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
                                "last_access" => $row['last_access'],
                                "last_ip_addr" => $row['last_ip_addr'],
                                "gcm" => $row['gcm_regid'],
                                "state" => $row['state'],
                                "fullname" => stripcslashes($row['fullname']),
                                "username" => $row['login'],
                                "email" => $row['email'],
                                "regtime" => $row['regtime'],
                                "ip_addr" => $row['ip_addr'],
                                "mobile" => $row['mobile'],
                                "points" => $row['points'],
                                "refer" => $row['refer'],
                                "refered" => $row['refered']);
            }
        }

        return $result;
    }

    public function getConfigs($fcm = 0)
    {
        
        $conf = array();
        $config = new functions($this->db);
        
        $stmt = $this->db->prepare("SELECT * FROM configuration WHERE api_status = 1");

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                while($row = $stmt->fetch()) {
                    
                    if(strlen($row['config_value']) == 1){
                        
                        if($row['config_value'] == 1){
                            
                            $conf[$row['config_name']] = true;
                            
                        }else if($row['config_value'] == 0){
                            
                            $conf[$row['config_name']] = false;
                            
                        }else{
                            
                            $conf[$row['config_name']] = $row['config_value'];
                            
                        }
                        
                    }else{
                        
                        $conf[$row['config_name']] = $row['config_value'];
                        
                    }
                    
                    
                }
            }
        }
        
        
        $config->updateAnalyticsSessions();
        
        $ipaddr = $_SERVER['REMOTE_ADDR'];
        $time = time();
        
        if ($fcm == 0) {
            
            $stmt = $this->db->prepare("UPDATE users SET last_access = (:time),last_ip_addr = (:ipaddr),gcm_regid = (:fcm) WHERE id = (:id)");
            $stmt->bindParam(":fcm", $fcm, PDO::PARAM_STR);
            $stmt->bindParam(":ipaddr", $ipaddr, PDO::PARAM_STR);
            $stmt->bindParam(":time", $time, PDO::PARAM_STR);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            $stmt->execute();

        }else{
        
            $stmt = $this->db->prepare("UPDATE users SET last_access = (:time),last_ip_addr = (:ipaddr) WHERE id = (:id)");
            $stmt->bindParam(":ipaddr", $ipaddr, PDO::PARAM_STR);
            $stmt->bindParam(":time", $time, PDO::PARAM_STR);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            $stmt->execute();
            
        }
        
        return $conf;
    }

    public function getConfig()
    {
        $result = array("error" => true,"error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM configuration WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();
				//$row['config_name'] => $row['config_value']
                $result = array("config_name" => $row['config_name'],"config_value" => $row['config_value']);
            }
        }

        return $result;
    }

    public function setId($accountId)
    {
        $this->id = $accountId;
    }

    public function getId()
    {
        return $this->id;
    }
    
    static function isSession()
    {
        if (isset($_SESSION) && isset($_SESSION['user_id'])) {

            return true;

        } else {

            return false;
        }
    }

    static function setSession($user_id, $access_token)
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_access_token'] = $access_token;
    }

    static function unsetSession()
    {
        session_regenerate_id(true);
        unset($_SESSION['user_id']);
        unset($_SESSION['user_access_token']);
    }

    static function getUserID()
    {
        if (isset($_SESSION) && isset($_SESSION['user_id'])) {

            return $_SESSION['user_id'];

        } else {

            return "undefined";
        }
    }

    static function getAccessToken()
    {
        if (isset($_SESSION) && isset($_SESSION['user_access_token'])) {

            return $_SESSION['user_access_token'];

        } else {

            return "undefined";
        }
    }

    static function createAccessToken()
    {
        $access_token = bin2hex(random_bytes(32));

        if (isset($_SESSION)) {

            $_SESSION['user_access_token'] = $access_token;
        }
    }
}

