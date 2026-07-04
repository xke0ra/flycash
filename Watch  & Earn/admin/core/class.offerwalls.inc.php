<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */

class offerwalls extends db_connect
{

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    public function getSingleOfferWall($id)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM offerwalls WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

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
                                "offer_url" => $row['url'],
                                "offer_type" => $row['type'],
                                "offer_points" => $row['points'],
                                "offer_featured" => $featured,
                                "offer_thumbnail" => $row['image'],
                                "offer_position" => $row['position'],
                                "offer_status" => $status);
            }
        }

        return $result;
    }

    public function getOfferwalls($requestId = 0)
    {
        if ($requestId == 0) {

            $requestId = 600;
            $requestId++;
        }

        $requests = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "offerwalls" => array());

        $stmt = $this->db->prepare("SELECT id FROM offerwalls WHERE id < (:requestId) ORDER BY position ASC");
        $stmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $requestInfo = $this->getSingleOfferWall($row['id']);

                array_push($requests['offerwalls'], $requestInfo);

                unset($requestInfo);
            }
        }

        return $requests;
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

    public function getYoutubeOffers($requestId = 0)
    {

        $requests = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "youtubeoffers" => array());

        $stmt = $this->db->prepare("SELECT id FROM youtube WHERE id > (:requestId) ORDER BY id ASC");
        $stmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $requestInfo = $this->getSingleYoutubeOffer($row['id']);

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
        $ids = implode(",",$array);
        $stmt = $this->db->prepare("SELECT id FROM youtube WHERE id NOT IN ( '" .$ids. "' ) AND status = 1 ORDER BY id ASC");

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {
                if(!in_array($row['id'],$array))
                {
                $requestInfo = $this->getSingleYoutubeOffer($row['id']);

                array_push($requests['youtubeoffers'], $requestInfo);

                unset($requestInfo);
                }
            }
        }

        return $requests;
    }
    
}
