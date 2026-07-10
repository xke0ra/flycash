<?php
class analytics extends db_connect
{

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    public function getRequestsCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM analytics");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    private function getMaxRequestsId()
    {
        $stmt = $this->db->prepare("SELECT MAX(id) FROM analytics");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getTodaySessions() {
        $today = date("Y-m-d", time());
        $sql = "SELECT sessions FROM analytics WHERE date = :today";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':today' => $today));
		$row = $stmt->fetchColumn();
		
		if($row < 1){
			$row = 0;
		}
		
        return $row;
    }

    public function getSingleRequest($id)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM analytics WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

				//date( 'd M Y', strtotime($row['date'])
				
                $result = array("date" => date( 'd M Y', strtotime($row['date'])),
                                "sessions" => $row['sessions'],
                                "requests" => $row['requests'],
                                "completed" => $row['completed']);
            }
        }

        return $result;
    }

    public function getAnalytics($requestId = 0)
    {
        if ($requestId == 0) {

            $requestId = $this->getMaxRequestsId();
            $requestId++;
        }

        $requests = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "data" => array());

        $stmt = $this->db->prepare("SELECT id FROM analytics WHERE id < (:requestId) ORDER BY id DESC LIMIT 10");
        $stmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $requestInfo = $this->getSingleRequest($row['id']);

                array_push($requests['data'], $requestInfo);

                //$requests['requestId'] = $requestInfo['id'];

                unset($requestInfo);
            }
        }

        return $requests;
    }
	
	

}

