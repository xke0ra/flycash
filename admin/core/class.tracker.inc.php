<?php
class tracker extends db_connect
{

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    public function getRequestsCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM tracker");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    private function getMaxRequestsId()
    {
        $stmt = $this->db->prepare("SELECT MAX(id) FROM tracker");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getSingleRequest($id)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM tracker WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();
                
                $date = $row['date'];
                $tn_date = "0";
                
                if(strpos($date, '-') !== false){ $tn_date = date('d M Y', strtotime($date)); }else{ $tn_date = date('d M Y', $date); }

                $result = array("id" => $row['id'],
                                "username" => $row['username'],
                                "points" => $row['points'],
                                "type" => $row['type'],
                                "date" => $tn_date);
            }
        }

        return $result;
    }

    public function getAllTrackerData($requestId = 0)
    {
        if ($requestId == 0) {

            $requestId = $this->getMaxRequestsId();
            $requestId++;
        }

        $requests = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "requestId" => $requestId,
                        "requests" => array());

        $stmt = $this->db->prepare("SELECT id FROM tracker WHERE id < (:requestId) ORDER BY id");
        $stmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $requestInfo = $this->getSingleRequest($row['id']);

                array_push($requests['requests'], $requestInfo);

                $requests['requestId'] = $requestInfo['id'];

                unset($requestInfo);
            }
        }

        return $requests;
    }

    public function getuserTrackerData($userId)
    {
        
        $requests = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "requests" => array());

        $stmt = $this->db->prepare("SELECT id FROM tracker WHERE user_id = :uid ORDER BY id");
        $stmt->bindParam(':uid', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $requestInfo = $this->getSingleRequest($row['id']);

                array_push($requests['requests'], $requestInfo);

                unset($requestInfo);
            }
        }

        return $requests;
    }

    public function getuserTransactionsAPI($userId)
    {
        
        $userdata = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "user" => $userId,
                        "transactions" => array());

        $stmt = $this->db->prepare("SELECT id FROM tracker WHERE user_id = :uid ORDER BY id DESC");
        $stmt->bindParam(':uid', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $requestInfo = $this->getModifiedCreditData($row['id']);

                array_push($userdata['transactions'], $requestInfo);

                unset($requestInfo);
            }
        }

        $stmt = $this->db->prepare("SELECT id FROM redemptions WHERE user_id = :uid ORDER BY id DESC");
        $stmt->bindParam(':uid', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $requestInfo = $this->getModifiedRequestsData($row['id']);

                array_push($userdata['transactions'], $requestInfo);

                unset($requestInfo);
            }
        }
        
        $transactions_loaded = count($userdata['transactions']);
    
        if ($transactions_loaded > 2) {
            
            foreach ($userdata['transactions'] as $key => $part) {
                
                $sort[$key] = strtotime($part['tn_date']);
                
            }
            
            array_multisort($sort, SORT_DESC, $userdata['transactions']);
            
        }
        
        return $userdata;
        
    }

    public function getModifiedCreditData($id)
    {
        $result = array("error" => true, "error_code" => ERROR_ACCOUNT_ID);
        $config = new functions($this->db);
		
        $prefix = $config->getConfig('TRANSACTION_PREFIX') . $config->getConfig('TRANSACTION_CREDIT_PREFIX');

        $stmt = $this->db->prepare("SELECT * FROM tracker WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();
                
                $date = $row['date'];
                $tn_date = "0";
                
                if(strpos($date, '-') !== false){ $tn_date = date('d M Y', strtotime($date)); }else{ $tn_date = date('d M Y', $date); }
            
                $result = array("tn_id" => $prefix.$row['id'],
                                "tn_type" => 'cr',
                                "tn_name" => $row['type'],
                                "tn_points" => $row['points'],
                                "tn_date" => $tn_date,
                                "tn_status" => '1');
            }
        }

        return $result;
    }

    public function getModifiedRequestsData($id)
    {
        $result = array("error" => true, "error_code" => ERROR_ACCOUNT_ID);
        $config = new functions($this->db);
		
        $prefix = $config->getConfig('TRANSACTION_PREFIX') . $config->getConfig('TRANSACTION_DEBIT_PREFIX');
        
        
        $stmt = $this->db->prepare("SELECT * FROM redemptions WHERE id = :id LIMIT 1");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $result = array("tn_id" => $prefix.$row['id'],
                                "tn_type" => 'db',
                                "tn_name" => $row['req_amount'] . ' ' . $row['gift_name'],
                                "tn_points" => $row['points_used'],
                                "tn_date" => date('d M Y', strtotime($row['date'])),
                                "tn_status" => $row['status']);
            }
        }

        return $result;
    }

    public function getModifiedCompletedData($id)
    {
        return $this->getModifiedRequestsData($id);
    }
	

}

