<?php
class offerwalls extends db_connect
{
    private ?\FlyCash\Services\OfferwallService $offerwallService = null;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);
    }

    public function getOfferwallService(): \FlyCash\Services\OfferwallService
    {
        if ($this->offerwallService === null) {
            $this->offerwallService = new \FlyCash\Services\OfferwallService($this->db);
        }
        return $this->offerwallService;
    }

    public function getSingleOfferWall($id)
    {
        return $this->getOfferwallService()->getSingleOfferwall($id);
    }

    public function getOfferwalls($requestId = 0, $limit = 0, $offset = 0)
    {
        return $this->getOfferwallService()->getOfferwalls($requestId, $limit, $offset);
    }
    

    public function getSingleYoutubeOffer($id)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM youtube WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();
                
                $status = "Disabled";
                
                if($row['status'] == 1){ $status = "Active"; }
                
                $result = array("offer_id" => $row['id'],
                                "offer_title" => $row['title'],
                                "offer_subtitle" => $row['subtitle'],
                                "offer_url" => $row['url'],
                                "offer_duration" => $row['duration'],
                                "offer_points" => $row['points'],
                                "offer_thumbnail" => $row['image'],
                                "offer_status" => $status);
            }
        }

        return $result;
    }

    public function getYoutubeOffers($requestId = 0, $limit = 0, $offset = 0)
    {

        $requests = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "youtubeoffers" => array());

        $sql = "SELECT * FROM youtube WHERE id > :requestId ORDER BY id ASC";
        if ($limit > 0) $sql .= " LIMIT " . intval($limit);
        if ($offset > 0) $sql .= " OFFSET " . intval($offset);
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $status = "Disabled";

                if($row['status'] == 1){ $status = "Active"; }

                $requestInfo = array("offer_id" => $row['id'],
                                "offer_title" => $row['title'],
                                "offer_subtitle" => $row['subtitle'],
                                "offer_url" => $row['url'],
                                "offer_duration" => $row['duration'],
                                "offer_points" => $row['points'],
                                "offer_thumbnail" => $row['image'],
                                "offer_status" => $status);

                array_push($requests['youtubeoffers'], $requestInfo);

                unset($requestInfo);
            }
        }

        return $requests;
    }

    public function getMyWatchedvideos()
    {

        $accountId = account::getUserID();
        $result = array();

        $stmt = $this->db->prepare("SELECT video_id FROM watched_video WHERE user_id = (:userId)");
        
        $stmt->bindParam(':userId', $accountId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                array_push($result, $row['video_id']);
                
            }
        }

        return $result;
    }
    
    public function getMyYoutubeOffers($requestId = 0)
    {

        $requests = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "youtubeoffers" => array());

        $array = $this->getMyWatchedvideos();
        $placeholders = array();
        $params = array();
        foreach ($array as $key => $value) {
            $placeholders[] = ":vid_".$key;
            $params[":vid_".$key] = (int)$value;
        }
        $placeholder_str = implode(",", $placeholders);
        $stmt = $this->db->prepare("SELECT * FROM youtube WHERE id NOT IN (".$placeholder_str.") AND status = 1 ORDER BY id ASC");

        if ($stmt->execute($params)) {

            while ($row = $stmt->fetch()) {
                if(!in_array($row['id'],$array))
                {
                $status = "Active";

                $requestInfo = array("offer_id" => $row['id'],
                                "offer_title" => $row['title'],
                                "offer_subtitle" => $row['subtitle'],
                                "offer_url" => $row['url'],
                                "offer_duration" => $row['duration'],
                                "offer_points" => $row['points'],
                                "offer_thumbnail" => $row['image'],
                                "offer_status" => $status);

                array_push($requests['youtubeoffers'], $requestInfo);

                unset($requestInfo);
                }
            }
        }

        return $requests;
    }
    
}
