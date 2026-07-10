<?php
class redeem extends db_connect
{

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    public function getSinglePayout($id = 0)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM payouts WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();
                
                $status = "Disabled";
                
                if($row['status'] == 1){ $status = "Active"; }

                $result = array("payout_id" => $row['id'],
                                "payout_title" => $row['name'],
                                "payout_subtitle" => $row['subtitle'],
                                "payout_message" => $row['message'],
                                "payout_amount" => $row['amount'],
                                "payout_pointsRequired" => $row['points'],
                                "payout_thumbnail" => $row['image'],
                                "payout_status" => $status);
            }
        }

        return $result;
    }

    public function getPayouts($requestId = 0, $limit = 0, $offset = 0)
    {
        if ($requestId == 0) {

            $requestId = 20;
            $requestId++;
        }

        $requests = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "payouts" => array());

        $sql = "SELECT * FROM payouts WHERE id < :requestId ORDER BY id";
        if ($limit > 0) $sql .= " LIMIT " . intval($limit);
        if ($offset > 0) $sql .= " OFFSET " . intval($offset);
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $status = "Disabled";

                if($row['status'] == 1){ $status = "Active"; }

                $requestInfo = array("payout_id" => $row['id'],
                                "payout_title" => $row['name'],
                                "payout_subtitle" => $row['subtitle'],
                                "payout_message" => $row['message'],
                                "payout_amount" => $row['amount'],
                                "payout_pointsRequired" => $row['points'],
                                "payout_thumbnail" => $row['image'],
                                "payout_status" => $status);

                array_push($requests['payouts'], $requestInfo);

                unset($requestInfo);
            }
        }

        return $requests;
    }
    
}
