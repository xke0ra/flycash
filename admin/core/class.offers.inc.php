<?php
class offers extends db_connect
{

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    public function getOfferStatus($id)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM offer_status WHERE cid = (:id) ORDER BY id DESC LIMIT 1");
        $stmt->bindParam(":id", $id, PDO::PARAM_STR);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();
                
                $featured = false;
                $status = "Disabled";
                
                if($row['featured'] == 1){ $featured = true; }
                if($row['status'] == 1){ $status = "Active"; }
                
                $result = array("offer_id" => $row['id'],
                                "offer_title" => $row['name'],
                                "offer_subtitle" => $row['subtitle'],
                                "offer_type" => $row['type'],
                                "offer_points" => $row['points'],
                                "offer_featured" => $featured,
                                "offer_thumbnail" => $row['image'],
                                "offer_status" => $status);
            }
        }

        return $result;
    }

    public function getStatus($cid,$of_id,$user)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM offer_status WHERE cid = :cid AND user = :user AND of_id = :of_id ORDER BY id DESC LIMIT 1");
        $stmt->execute(array(':cid' => $cid, ':user' => $user, ':of_id' => $of_id));
        
        if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();
                
                $result = array("id" => $row['id'],
                                "cid" => $row['cid'],
                                "user" => $row['user'],
                                "of_id" => $row['of_id'],
                                "of_title" => $row['of_title'],
                                "of_amount" => $row['of_amount'],
                                "of_url" => $row['of_url'],
                                "partner" => $row['partner'],
                                "ip_addr" => $row['ip_addr'],
                                "dev_name" => $row['dev_name'],
                                "dev_man" => $row['dev_man'],
                                "date" => $row['date'],
                                "status" => $row['status']);
            
        }else{
            
            $stmt = $this->db->prepare("SELECT * FROM offer_status WHERE user = :user AND of_id = :of_id ORDER BY id DESC LIMIT 1");
            $stmt->execute(array(':user' => $user, ':of_id' => $of_id));
            
                if ($stmt->rowCount() > 0) {
                    
                    $row = $stmt->fetch();
                    
                    if ($row['status'] == 1 || $row['status'] == 3 || $row['status'] == 2) {
                        
                        $result = array("id" => $row['id'],
                                "cid" => $row['cid'],
                                "user" => $row['user'],
                                "of_id" => $row['of_id'],
                                "of_title" => $row['of_title'],
                                "of_amount" => $row['of_amount'],
                                "of_url" => $row['of_url'],
                                "partner" => $row['partner'],
                                "ip_addr" => $row['ip_addr'],
                                "dev_name" => $row['dev_name'],
                                "dev_man" => $row['dev_man'],
                                "date" => $row['date'],
                                "status" => $row['status']);
                        
                    }

                }
            
        }

        return $result;
    }
    
}
