<?php

class redemptions extends db_connect
{
    private $requestFrom = 0;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);
    }

    public function getRequestsCount($status = null)
    {
        $sql = "SELECT count(*) FROM redemptions";
        if ($status !== null) {
            $sql .= " WHERE status = :status";
        }
        $stmt = $this->db->prepare($sql);
        if ($status !== null) {
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    private function getMaxId($status = null)
    {
        $sql = "SELECT MAX(id) FROM redemptions";
        if ($status !== null) {
            $sql .= " WHERE status = :status";
        }
        $stmt = $this->db->prepare($sql);
        if ($status !== null) {
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getSingleRequest($id, $status = null)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);
        $sql = "SELECT * FROM redemptions WHERE id = :id";
        if ($status !== null) {
            $sql .= " AND status = :status";
        }
        $sql .= " LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        if ($status !== null) {
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        }
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
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

    public function getRequests($requestId = 0, $limit = 0, $offset = 0, $status = null)
    {
        if ($requestId == 0) {
            $requestId = $this->getMaxId($status);
            $requestId++;
        }
        $requests = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "requestId" => $requestId,
                        "requests" => array());
        $sql = "SELECT * FROM redemptions WHERE id < :requestId";
        if ($status !== null) {
            $sql .= " AND status = :status";
        }
        $sql .= " ORDER BY id";
        if ($limit > 0) $sql .= " LIMIT " . intval($limit);
        if ($offset > 0) $sql .= " OFFSET " . intval($offset);
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
        if ($status !== null) {
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        }
        if ($stmt->execute()) {
            while ($row = $stmt->fetch()) {
                $requestInfo = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
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
                $requests['requestId'] = $requestInfo['id'];
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
        $stmt = $this->db->prepare("SELECT * FROM redemptions ORDER BY id DESC LIMIT 5");
        if ($stmt->execute()) {
            while ($row = $stmt->fetch()) {
                $requestInfo = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
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

    public function getuserRequests($userId, $status = null)
    {
        $requests = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "requests" => array());
        $sql = "SELECT * FROM redemptions WHERE user_id = :uid";
        if ($status !== null) {
            $sql .= " AND status = :status";
        }
        $sql .= " ORDER BY id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':uid', $userId, PDO::PARAM_INT);
        if ($status !== null) {
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        }
        if ($stmt->execute()) {
            while ($row = $stmt->fetch()) {
                $requestInfo = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
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
        $stmt = $this->db->prepare("UPDATE redemptions SET status = 'completed', note = :note WHERE id = :id");
        if ($stmt->execute(array(':note' => $note, ':id' => $id))) {
            return true;
        }
        return false;
    }

    public function ProcessingRequest($id, $note = 'none')
    {
        if($note !== 'none'){
            $stmt = $this->db->prepare("UPDATE redemptions SET note = :note WHERE id = :id");
            $stmt->execute(array(':note' => $note, ':id' => $id));
        }
        $stmt = $this->db->prepare("UPDATE redemptions SET status = 'processing' WHERE id = :id");
        if ($stmt->execute(array(':id' => $id))) {
            return true;
        }
        return false;
    }

    public function RejectRequest($id, $note = 'none')
    {
        $stmt = $this->db->prepare("UPDATE redemptions SET status = 'rejected', note = :note WHERE id = :id");
        if ($stmt->execute(array(':note' => $note, ':id' => $id))) {
            return true;
        }
        return false;
    }
}
