<?php

namespace FlyCash\Services;

use PDO;

class ProfileService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function updateAccount(int $accountId, string $fullname, string $email, string $mobile, string $newEmail = ''): bool
    {
        if (empty($fullname)) {
            return false;
        }

        if ($newEmail) {
            if (!\helper::isCorrectEmail($email)) {
                return false;
            }

            $helper = new \helper($this->db);
            if ($helper->isEmailExists($email)) {
                return false;
            }
        }

        $stmt = $this->db->prepare("UPDATE users SET fullname = (:name), email = (:email), mobile = (:mobile) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $stmt->bindParam(":name", $fullname, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":mobile", $mobile, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /** @return ?array<string, mixed> */
    public function get(int $accountId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $accountId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();

                return array(
                    "id" => $row['id'],
                    "last_access" => $row['last_access'],
                    "last_ip_addr" => $row['last_ip_addr'],
                    "gcm" => $row['gcm_regid'],
                    "state" => $row['state'],
                    "fullname" => stripcslashes($row['fullname']),
                    "username" => $row['login'],
                    "email" => $row['email'],
                    "regtime" => $row['regtime'],
                    "ip_addr" => $row['ip_addr'],
                    "mobile" => $row['mobile'],
                    "points" => $row['points'],
                    "refer" => $row['refer'],
                    "refered" => $row['refered']
                );
            }
        }

        return null;
    }

    public function getState(int $accountId): int
    {
        $stmt = $this->db->prepare("SELECT state FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $accountId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $row = $stmt->fetch();
            return $row['state'];
        }

        return 0;
    }

    public function setState(int $accountId, int $state): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET state = (:accountState) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $stmt->bindParam(":accountState", $state, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function remAccount(int $accountId): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET state = 3 WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /** @return ?array<string, mixed> */
    public function getReferer(string $refererCode): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE refer = (:refercode) LIMIT 1");
        $stmt->bindParam(":refercode", $refererCode, PDO::PARAM_STR);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();

                return array(
                    "id" => $row['id'],
                    "last_access" => $row['last_access'],
                    "last_ip_addr" => $row['last_ip_addr'],
                    "gcm" => $row['gcm_regid'],
                    "state" => $row['state'],
                    "fullname" => stripcslashes($row['fullname']),
                    "username" => $row['login'],
                    "email" => $row['email'],
                    "regtime" => $row['regtime'],
                    "ip_addr" => $row['ip_addr'],
                    "mobile" => $row['mobile'],
                    "points" => $row['points'],
                    "refer" => $row['refer'],
                    "refered" => $row['refered']
                );
            }
        }

        return null;
    }

    /** @return array<string, mixed> */
    public function getOldRefersData(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM referers WHERE user_id = :userId LIMIT 1");
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();

                return array(
                    "id" => $row['id'],
                    "username" => $row['username'],
                    "referer" => $row['referer'],
                    "points" => $row['points'],
                    "type" => $row['type'],
                    "date" => $row['date']
                );
            }
        }

        return array("error" => true, "error_code" => ERROR_ACCOUNT_ID);
    }

    /** @return ?array<string, mixed> */
    public function getUserData(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE login = (:username) LIMIT 1");
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();

                return array(
                    "id" => $row['id'],
                    "last_access" => $row['last_access'],
                    "last_ip_addr" => $row['last_ip_addr'],
                    "gcm" => $row['gcm_regid'],
                    "state" => $row['state'],
                    "fullname" => stripcslashes($row['fullname']),
                    "username" => $row['login'],
                    "email" => $row['email'],
                    "regtime" => $row['regtime'],
                    "ip_addr" => $row['ip_addr'],
                    "mobile" => $row['mobile'],
                    "points" => $row['points'],
                    "refer" => $row['refer'],
                    "refered" => $row['refered']
                );
            }
        }

        return null;
    }

    /** @return array<string, mixed> */
    public function getConfigs(int $accountId, int $fcm = 0): array
    {
        $conf = array();

        $stmt = $this->db->prepare("SELECT * FROM configuration WHERE api_status = 1");

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch()) {

                    if (strlen($row['config_value']) == 1) {

                        if ($row['config_value'] == 1) {

                            $conf[$row['config_name']] = true;

                        } else if ($row['config_value'] == 0) {

                            $conf[$row['config_name']] = false;

                        } else {

                            $conf[$row['config_name']] = $row['config_value'];

                        }

                    } else {

                        $conf[$row['config_name']] = $row['config_value'];

                    }

                }
            }
        }

        $config = new \functions($this->db);
        $config->updateAnalyticsSessions();

        $ipaddr = $_SERVER['REMOTE_ADDR'];
        $time = time();

        if ($fcm == 0) {

            $stmt = $this->db->prepare("UPDATE users SET last_access = (:time),last_ip_addr = (:ipaddr),gcm_regid = (:fcm) WHERE id = (:id)");
            $stmt->bindParam(":fcm", $fcm, PDO::PARAM_STR);
            $stmt->bindParam(":ipaddr", $ipaddr, PDO::PARAM_STR);
            $stmt->bindParam(":time", $time, PDO::PARAM_STR);
            $stmt->bindParam(":id", $accountId, PDO::PARAM_INT);
            $stmt->execute();

        } else {

            $stmt = $this->db->prepare("UPDATE users SET last_access = (:time),last_ip_addr = (:ipaddr) WHERE id = (:id)");
            $stmt->bindParam(":ipaddr", $ipaddr, PDO::PARAM_STR);
            $stmt->bindParam(":time", $time, PDO::PARAM_STR);
            $stmt->bindParam(":id", $accountId, PDO::PARAM_INT);
            $stmt->execute();

        }

        return $conf;
    }

    /** @return ?array<string, mixed> */
    public function getUserInfo(int $userId): ?array
    {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(':id' => $userId));
        $dbarray = $stmt->fetch();

        if (!$dbarray) {
            return null;
        }

        return $dbarray;
    }

    /** @return ?array<string, mixed> */
    public function getUserInfoByValue(string $field, string $value): ?array
    {
        $allowedFields = ['id', 'login', 'email', 'mobile', 'refer'];
        if (!in_array($field, $allowedFields, true)) {
            return null;
        }

        $query = "SELECT * FROM users WHERE `" . $field . "` = :value";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(':value' => $value));
        $dbarray = $stmt->fetch();

        if (!$dbarray) {
            return null;
        }

        return $dbarray;
    }
}
