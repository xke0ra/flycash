<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */

class requests extends db_connect
{
    private $requestFrom = 0;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    public function getRequestsCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM Requests");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    private function getMaxRequestsId()
    {
        $stmt = $this->db->prepare("SELECT MAX(rid) FROM Requests");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getSingleRequest($id)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM Requests WHERE rid = (:id) LIMIT 1");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "rid" => $row['rid'],
                                "request_from" => $row['request_from'],
                                "dev_name" => $row['dev_name'],
                                "dev_man" => $row['dev_man'],
                                "gift_name" => stripcslashes($row['gift_name']),
                                "req_amount" => $row['req_amount'],
                                "points_used" => $row['points_used'],
                                "date" => $row['date'],
                                "status" => $row['status'],
                                "username" => $row['username']);
            }
        }

        return $result;
    }

    public function getRequests($requestId = 0, $limit = 0, $offset = 0)
    {
        if ($requestId == 0) {

            $requestId = $this->getMaxRequestsId();
            $requestId++;
        }

        $requests = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "requestId" => $requestId,
                        "requests" => array());

        $sql = "SELECT * FROM Requests WHERE rid < :requestId ORDER BY rid";
        if ($limit > 0) $sql .= " LIMIT " . intval($limit);
        if ($offset > 0) $sql .= " OFFSET " . intval($offset);
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $requestInfo = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "rid" => $row['rid'],
                                "request_from" => $row['request_from'],
                                "dev_name" => $row['dev_name'],
                                "dev_man" => $row['dev_man'],
                                "gift_name" => stripcslashes($row['gift_name']),
                                "req_amount" => $row['req_amount'],
                                "points_used" => $row['points_used'],
                                "date" => $row['date'],
                                "status" => $row['status'],
                                "username" => $row['username']);

                array_push($requests['requests'], $requestInfo);

                $requests['requestId'] = $requestInfo['rid'];

                unset($requestInfo);
            }
        }

        return $requests;
    }

    public function recentRequests()
    {

        $requests = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "requests" => array());

        $stmt = $this->db->prepare("SELECT * FROM Requests ORDER BY rid DESC LIMIT 5");

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $requestInfo = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "rid" => $row['rid'],
                                "request_from" => $row['request_from'],
                                "dev_name" => $row['dev_name'],
                                "dev_man" => $row['dev_man'],
                                "gift_name" => stripcslashes($row['gift_name']),
                                "req_amount" => $row['req_amount'],
                                "points_used" => $row['points_used'],
                                "date" => $row['date'],
                                "status" => $row['status'],
                                "username" => $row['username']);

                array_push($requests['requests'], $requestInfo);

                unset($requestInfo);
            }
        }

        return $requests;
    }

    public function getuserRequests($username)
    {
        
        $requests = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "requests" => array());

        $stmt = $this->db->prepare("SELECT * FROM Requests WHERE username = (:username) ORDER BY rid");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $requestInfo = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "rid" => $row['rid'],
                                "request_from" => $row['request_from'],
                                "dev_name" => $row['dev_name'],
                                "dev_man" => $row['dev_man'],
                                "gift_name" => stripcslashes($row['gift_name']),
                                "req_amount" => $row['req_amount'],
                                "points_used" => $row['points_used'],
                                "date" => $row['date'],
                                "status" => $row['status'],
                                "username" => $row['username']);

                array_push($requests['requests'], $requestInfo);

                unset($requestInfo);
            }
        }

        return $requests;
    }

    public function CompleteRequest($id, $note = 'none')
	{
	    
	    if($note !== 'none'){
	        $stmt = $this->db->prepare("UPDATE Requests SET note = :note WHERE rid = :id");
	        $stmt->execute(array(':note' => $note, ':id' => $id));
	    }
	    
	    
        $stmt = $this->db->prepare("INSERT INTO Completed(rid,request_from,dev_name,dev_man,gift_name,req_amount,points_used,date,username,note) SELECT rid,request_from,dev_name,dev_man,gift_name,req_amount,points_used,date,username,note FROM Requests WHERE rid = :id");
        
        if ($stmt->execute(array(':id' => $id))) {
			
			$stmt = $this->db->prepare("DELETE FROM Requests WHERE rid = :id");
			
			if ($stmt->execute(array(':id' => $id))) {
				
				return true;
				
			}
			
        }

        return false;
    }

    public function ProcessingRequest($id, $note = 'none')
	{
	    
	    if($note !== 'none'){
	        $stmt = $this->db->prepare("UPDATE Requests SET note = :note WHERE rid = :id");
	        $stmt->execute(array(':note' => $note, ':id' => $id));
	    }
	    
        $stmt = $this->db->prepare("UPDATE Requests SET status = 2 WHERE rid = :id");
        
        if ($stmt->execute(array(':id' => $id))) {
			
			return true;
			
        }

        return false;
    }

    public function RejectRequest($id, $note = 'none')
	{
	    
	    if($note !== 'none'){
	        $stmt = $this->db->prepare("UPDATE Requests SET note = :note WHERE rid = :id");
	        $stmt->execute(array(':note' => $note, ':id' => $id));
	    }
	    
        $stmt = $this->db->prepare("UPDATE Requests SET status = 3, note= :note WHERE rid = :id");
        
        if ($stmt->execute(array(':note' => $note, ':id' => $id))) {
			
			return true;
			
        }

        return false;
    }
	
}

