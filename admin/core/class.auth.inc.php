<?php
class auth extends db_connect
{
    private $access_valid_sec = 0;
    private $refresh_valid_sec = 0;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);
        $this->access_valid_sec = 3600;
        $this->refresh_valid_sec = 30 * 24 * 3600;
    }

    public function getAccessValidSeconds()
    {
        return $this->access_valid_sec;
    }

    public function getRefreshValidSeconds()
    {
        return $this->refresh_valid_sec;
    }

    private function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    public function authorize($accountId, $accessToken)
    {
        $accountId = helper::clearInt($accountId);
        $accessToken = helper::clearText($accessToken);
        $accessToken = helper::escapeText($accessToken);

        $stmt = $this->db->prepare("SELECT id, expiresAt FROM access_data WHERE accountId = (:accountId) AND accessToken = (:accessToken) AND removeAt = 0 LIMIT 1");
        $stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $stmt->bindParam(":accessToken", $accessToken, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $expiresAt = intval($row['expiresAt']);
            if ($expiresAt > 0 && time() > $expiresAt) {
                $this->remove($accountId, $accessToken);
                return false;
            }
            return true;
        }

        return false;
    }

    public function remove($accountId, $accessToken)
    {
        $accountId = helper::clearInt($accountId);
        $accessToken = helper::clearText($accessToken);
        $accessToken = helper::escapeText($accessToken);

        $currentTime = time();
        $stmt = $this->db->prepare("UPDATE access_data SET removeAt = (:removeAt) WHERE accountId = (:accountId) AND accessToken = (:accessToken)");
        $stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $stmt->bindParam(":accessToken", $accessToken, PDO::PARAM_STR);
        $stmt->bindParam(":removeAt", $currentTime, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function removeByRefresh($refreshToken)
    {
        $refreshToken = helper::clearText($refreshToken);
        $refreshToken = helper::escapeText($refreshToken);

        $currentTime = time();
        $stmt = $this->db->prepare("UPDATE access_data SET removeAt = (:removeAt) WHERE refreshToken = (:refreshToken)");
        $stmt->bindParam(":removeAt", $currentTime, PDO::PARAM_INT);
        $stmt->bindParam(":refreshToken", $refreshToken, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function removeAll($accountId)
    {
        $accountId = helper::clearInt($accountId);

        $currentTime = time();
        $stmt = $this->db->prepare("UPDATE access_data SET removeAt = (:removeAt) WHERE accountId = (:accountId)");
        $stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $stmt->bindParam(":removeAt", $currentTime, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function create($accountId, $clientId = 0)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $currentTime = time();

        $u_agent = helper::u_agent();
        $ip_addr = helper::ip_addr();

        $accessToken = $this->generateToken();
        $refreshToken = $this->generateToken();
        $expiresAt = $currentTime + $this->access_valid_sec;

        $stmt = $this->db->prepare("INSERT INTO access_data (accountId, accessToken, refreshToken, clientId, createAt, expiresAt, u_agent, ip_addr) value (:accountId, :accessToken, :refreshToken, :clientId, :createAt, :expiresAt, :u_agent, :ip_addr)");
        $stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $stmt->bindParam(":accessToken", $accessToken, PDO::PARAM_STR);
        $stmt->bindParam(":refreshToken", $refreshToken, PDO::PARAM_STR);
        $stmt->bindParam(":clientId", $clientId, PDO::PARAM_INT);
        $stmt->bindParam(":createAt", $currentTime, PDO::PARAM_INT);
        $stmt->bindParam(":expiresAt", $expiresAt, PDO::PARAM_INT);
        $stmt->bindParam(":u_agent", $u_agent, PDO::PARAM_STR);
        $stmt->bindParam(":ip_addr", $ip_addr, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $result = array('error'=> false,
                            'error_code' => ERROR_SUCCESS,
                            'accessToken' => $accessToken,
                            'refreshToken' => $refreshToken,
                            'accountId' => $accountId,
                            'expiresIn' => $this->access_valid_sec);
        }

        return $result;
    }

    public function refresh($refreshToken)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCESS_TOKEN);

        $refreshToken = helper::clearText($refreshToken);
        $refreshToken = helper::escapeText($refreshToken);

        $currentTime = time();

        $stmt = $this->db->prepare("SELECT id, accountId, clientId FROM access_data WHERE refreshToken = (:refreshToken) AND removeAt = 0 LIMIT 1");
        $stmt->bindParam(":refreshToken", $refreshToken, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return $result;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $accountId = intval($row['accountId']);
        $origClientId = intval($row['clientId']);

        $stmt2 = $this->db->prepare("SELECT createAt FROM access_data WHERE id = (:id) LIMIT 1");
        $stmt2->bindParam(":id", $row['id'], PDO::PARAM_INT);
        $stmt2->execute();
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

        $elapsed = $currentTime - intval($row2['createAt']);
        if ($elapsed > $this->refresh_valid_sec) {
            $this->removeByRefresh($refreshToken);
            return $result;
        }

        $newAccessToken = $this->generateToken();
        $newRefreshToken = $this->generateToken();
        $newExpiresAt = $currentTime + $this->access_valid_sec;

        $this->removeByRefresh($refreshToken);

        $u_agent = helper::u_agent();
        $ip_addr = helper::ip_addr();

        $stmt3 = $this->db->prepare("INSERT INTO access_data (accountId, accessToken, refreshToken, clientId, createAt, expiresAt, u_agent, ip_addr) value (:accountId, :accessToken, :refreshToken, :clientId, :createAt, :expiresAt, :u_agent, :ip_addr)");
        $stmt3->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $stmt3->bindParam(":accessToken", $newAccessToken, PDO::PARAM_STR);
        $stmt3->bindParam(":refreshToken", $newRefreshToken, PDO::PARAM_STR);
        $stmt3->bindParam(":clientId", $origClientId, PDO::PARAM_INT);
        $stmt3->bindParam(":createAt", $currentTime, PDO::PARAM_INT);
        $stmt3->bindParam(":expiresAt", $newExpiresAt, PDO::PARAM_INT);
        $stmt3->bindParam(":u_agent", $u_agent, PDO::PARAM_STR);
        $stmt3->bindParam(":ip_addr", $ip_addr, PDO::PARAM_STR);

        if ($stmt3->execute()) {
            $result = array('error'=> false,
                            'error_code' => ERROR_SUCCESS,
                            'accessToken' => $newAccessToken,
                            'refreshToken' => $newRefreshToken,
                            'accountId' => $accountId,
                            'expiresIn' => $this->access_valid_sec);
        }

        return $result;
    }
}
