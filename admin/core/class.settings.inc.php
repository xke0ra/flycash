<?php
class settings extends db_connect
{
    private $requestFrom = 0;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    function changeAdminName($fullname, $id) {
		$stmt = $this->db->prepare("UPDATE admins SET fullname = (:fullname) WHERE id = (:id)");
		$stmt->bindParam(":fullname", $fullname, PDO::PARAM_STR);
		$stmt->bindParam(":id", $id, PDO::PARAM_STR);
		
		return $stmt->execute();
		
	}

    function apiInstall($panl) {
		
		$set = new functions($this->db);
		$install = $set->getConfig('INSTALL');
		$result = false;
		
		if($install == '0'){
			
			$uri = 'http://www.aym.com/api/vendor/install.php';
        	$data = [
        		'install' => $panl, 
        		'grant_type' => 'authorization_code'
        	];
        	$ch = curl_init();
        	curl_setopt($ch, CURLOPT_URL, $uri);
        	curl_setopt($ch, CURLOPT_POST, true);
        	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        	curl_setopt($ch, CURLOPT_HEADER, 0);
        	curl_setopt($ch, CURLOPT_NOBODY, 0);
        	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        	$result = curl_exec($ch);
            $curl_error = curl_errno($ch);
            curl_close($ch);
			
			if($curl_error == '0'){
				
				$json = json_decode($result, true);
				
                if( isset( $json['access_token'] ) ){
					
					$result = $set->updateConfigs('1', 'INSTALL');
					$result = $set->updateConfigs($json['access_token'], 'ACCESS_TOKEN');
					
                }else{
					$result = $set->updateConfigs('0', 'INSTALL');
				}
            
            }else{
				$result = $set->updateConfigs('0', 'INSTALL');
			}
		}
		
		return $result;
		
	}

    function saveSettings($appname, $tagline, $webpanel_url) {
		
		$set = new functions($this->db);
		
		$result = $set->updateConfigs($appname, 'APP_NAME');
		$result = $set->updateConfigs($tagline, 'APP_DESC');
		$result = $set->updateConfigs($webpanel_url, 'WEB_ROOT');
		$result = $set->updateConfigs(time(), 'LAST_SAVE');
		$result = $this->apiInstall($webpanel_url);
		
		if(!$set->getConfig('INSTALL')){ $this->apiInstall($webpanel_url); }
		
		return $result;
		
	}

    function changepass($acid, $old_pass, $new_pass, $cnf_pass) {
		
		$result = 422;
        $old_password = trim($old_pass);
		$new_password = trim($new_pass);
		$cnf_password = trim($cnf_pass);
		
		if($new_password == $cnf_password){
			
			$stmt = $this->db->prepare("SELECT id, salt, password FROM admins WHERE id = (:id) LIMIT 1");
			$stmt->bindParam(":id", $acid, PDO::PARAM_STR);
			$stmt->execute();
			
			if ($stmt->rowCount() > 0) {
				
				$row = $stmt->fetch();
				
				if (strpos($row['password'], '$2y$') === 0 || strpos($row['password'], '$2a$') === 0) {
					$validOld = password_verify($old_password, $row['password']);
				} else {
					$validOld = (md5(md5($old_password).$row['salt']) === $row['password']);
				}
				
				if ($validOld) {
					
					$new_passw = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
					$stmt = $this->db->prepare("UPDATE admins SET password = :password WHERE id = :id");
					$stmt->bindParam(":password", $new_passw, PDO::PARAM_STR);
					$stmt->bindParam(":id", $acid, PDO::PARAM_INT);
					
					if($stmt->execute()){
						$result = 1;
					}else{
						$result = 424;
					}
					
				}else{
					$result = 425;
				}
			}else{
				$result = 420;
			}
			
		}else{
			$result = 422;
		}
		
        return $result;
    }
	
	
	

}

