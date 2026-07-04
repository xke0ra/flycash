<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */

class stats extends db_connect
{
    private $requestFrom = 0;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    public function getUsersCount($accountState)
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM users WHERE state = (:state)");
        $stmt->bindParam(":state", $accountState, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    private function getMaxAccountId()
    {
        $stmt = $this->db->prepare("SELECT MAX(id) FROM users");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    private function getMaxAuthId()
    {
        $stmt = $this->db->prepare("SELECT MAX(id) FROM access_data");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getAccounts($userId = 0, $limit = 0, $offset = 0)
    {
        if ($userId == 0) {

            $userId = $this->getMaxAccountId();
            $userId++;
        }

        $users = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "userId" => $userId,
                        "users" => array());

        $sql = "SELECT * FROM users WHERE id < :userId ORDER BY id";
        if ($limit > 0) $sql .= " LIMIT " . intval($limit);
        if ($offset > 0) $sql .= " OFFSET " . intval($offset);
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $accountInfo = array(
                    "error" => false,
                    "id" => $row['id'],
                    "username" => $row['login'],
                    "fullname" => $row['fullname'],
                    "email" => $row['email'],
                    "phone" => $row['mobile'],
                    "points" => $row['points'],
                    "regtime" => $row['regtime'],
                    "regtype" => $row['regtype'],
                    "state" => $row['state'],
                    "image" => $row['image'],
                    "cover" => $row['cover'],
                    "birthday" => $row['birthday'],
                    "last_access" => $row['last_access'],
                    "last_ip_addr" => $row['last_ip_addr'],
                    "ip_addr" => $row['ip_addr'],
                    "refer" => $row['refer'],
                    "refered" => $row['refered'],
                    "referer" => $row['referer'],
                    "gcm_regid" => $row['gcm_regid'],
                    "mobile" => $row['mobile']
                );

                array_push($users['users'], $accountInfo);

                $users['userId'] = $row['id'];

                unset($accountInfo);
            }
        }

        return $users;
    }

    public function getRecentAccounts()
	{

        $users = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "users" => array());

        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY id DESC LIMIT 5");

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $accountInfo = array(
                    "error" => false,
                    "id" => $row['id'],
                    "username" => $row['login'],
                    "fullname" => $row['fullname'],
                    "email" => $row['email'],
                    "phone" => $row['mobile'],
                    "points" => $row['points'],
                    "regtime" => $row['regtime'],
                    "regtype" => $row['regtype'],
                    "state" => $row['state'],
                    "image" => $row['image'],
                    "cover" => $row['cover'],
                    "birthday" => $row['birthday'],
                    "last_access" => $row['last_access'],
                    "last_ip_addr" => $row['last_ip_addr'],
                    "ip_addr" => $row['ip_addr'],
                    "refer" => $row['refer'],
                    "refered" => $row['refered'],
                    "referer" => $row['referer'],
                    "gcm_regid" => $row['gcm_regid'],
                    "mobile" => $row['mobile']
                );

                array_push($users['users'], $accountInfo);

                unset($accountInfo);
            }
        }

        return $users;
    }

    public function getConfigs() {
		
        $users = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "configs" => array());
						
        $sql = "SELECT * FROM configuration WHERE id < 1000 ORDER BY id";
        $stmt = $this->db->prepare($sql);
        
		if($stmt->execute()){
			
			while ($row = $stmt->fetch()) {

                $configInfo = array(
                    "error" => false,
                    "id" => $row['id'],
                    "name" => $row['config_name'],
                    "value" => $row['config_value']
                );

                array_push($users['configs'], $configInfo);

                unset($configInfo);
            }
			
		}
		
		
        return $users;
    }

    public function getAuthData($accountId, $authId = 0)
    {
        if ($authId == 0) {

            $authId = $this->getMaxAuthId();
            $authId++;
        }

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "authId" => $authId,
                        "data" => array());

        $stmt = $this->db->prepare("SELECT * FROM access_data WHERE accountId = (:accountId) AND id < (:authId) ORDER BY id");
        $stmt->bindParam(':authId', $authId, PDO::PARAM_INT);
        $stmt->bindParam(':accountId', $accountId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {;

                $dataInfo = array("id" => $row['id'],
                                  "accountId" => $row['accountId'],
                                  "accessToken" => $row['accessToken'],
                                  "clientId" => $row['clientId'],
                                  "createAt" => $row['createAt'],
                                  "removeAt" => $row['removeAt'],
                                  "u_agent" => $row['u_agent'],
                                  "ip_addr" => $row['ip_addr']);

                array_push($result['data'], $dataInfo);

                $result['authId'] = $row['id'];

                unset($dataInfo);
            }
        }

        return $result;
    }

    public function setRequestFrom($requestFrom)
    {
        $this->requestFrom = $requestFrom;
    }

    public function getRequestFrom()
    {
        return $this->requestFrom;
    }
}

