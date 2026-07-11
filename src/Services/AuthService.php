<?php

namespace FlyCash\Services;

use PDO;

class AuthService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @return array<string, mixed>
     */
    public function signin(string $login, string $password): array
    {
        $access_data = ['error' => true];

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $type = "email";
        } else {
            $type = "login";
        }

        $sql = "SELECT id, salt, passw, state, email_verified FROM users WHERE " . $type . " = (:username) LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":username", $login, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();

            $isDev = (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development');
            if (!$isDev && intval($row['email_verified']) === 0) {
                $access_data = [
                    "error" => true,
                    "error_code" => 403,
                    "error_description" => "Email not verified. Please check your inbox."
                ];
                return $access_data;
            }

            $enabledState = defined('ACCOUNT_STATE_ENABLED') ? ACCOUNT_STATE_ENABLED : 0;
            if ((int)$row['state'] !== $enabledState) {
                $access_data = [
                    "error" => true,
                    "error_code" => 403,
                    "error_description" => "This account is not active. Please contact support."
                ];
                return $access_data;
            }

            if (strpos($row['passw'], '$2y$') === 0 || strpos($row['passw'], '$2a$') === 0) {

                if (password_verify($password, $row['passw'])) {
                    $access_data = [
                        "error" => false,
                        "error_code" => defined('ERROR_SUCCESS') ? ERROR_SUCCESS : 0,
                        "accountId" => $row['id']
                    ];
                }

            } else {

                $passw_hash = md5(md5($password) . $row['salt']);

                if ($passw_hash === $row['passw']) {

                    $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    $upd = $this->db->prepare("UPDATE users SET passw = :hash WHERE id = :id");
                    $upd->execute([':hash' => $newHash, ':id' => $row['id']]);

                    $access_data = [
                        "error" => false,
                        "error_code" => defined('ERROR_SUCCESS') ? ERROR_SUCCESS : 0,
                        "accountId" => $row['id']
                    ];
                }
            }

            if ($access_data['error'] !== false) {
                $access_data['error_code'] = defined('ERROR_UNKNOWN') ? ERROR_UNKNOWN : 100;
                $access_data['error_description'] = 'Invalid username or password.';
            }
        }

        return $access_data;
    }

    /**
     * @return array<string, mixed>
     */
    public function signup(string $login, string $password, string $email = '', string $ip = ''): array
    {
        $result = ["error" => true, "error_description" => "Serious issue, contact developer"];

        $passw_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $currentTime = time();
        $accountState = defined('ACCOUNT_STATE_ENABLED') ? ACCOUNT_STATE_ENABLED : 1;

        $query = "INSERT INTO users (last_access, last_ip_addr, gcm_regid, state, fullname, salt, passw, login, email, image, regtime, regtype, ip_addr, mobile, points, refer, refered, referer) VALUES (:currentTime, :ip_addr, NULL, :accountState, :fullname, :salt, :passw_hash, :username, :email, :profile_pic, :currentTime2, :reg_type, :ip_addr2, '', '0', :refer, '0', '')";

        $stmt = $this->db->prepare($query);
        $createuser = $stmt->execute([
            ':currentTime' => $currentTime,
            ':ip_addr' => $ip,
            ':accountState' => $accountState,
            ':fullname' => $login,
            ':salt' => '',
            ':passw_hash' => $passw_hash,
            ':username' => $login,
            ':email' => $email,
            ':profile_pic' => '',
            ':currentTime2' => $currentTime,
            ':reg_type' => '',
            ':ip_addr2' => $ip,
            ':refer' => bin2hex(random_bytes(8)),
        ]);

        if ($createuser) {
            $result = [
                "error" => false,
                'accountId' => (int) $this->db->lastInsertId(),
                'username' => $login,
                'error_code' => defined('ERROR_SUCCESS') ? ERROR_SUCCESS : 0,
                'error_description' => 'SignUp Success!'
            ];
        }

        return $result;
    }

    public function authorize(int $accountId, string $accessToken): bool
    {
        $stmt = $this->db->prepare("SELECT id, expiresAt FROM access_data WHERE accountId = (:accountId) AND accessToken = (:accessToken) AND removeAt = 0 LIMIT 1");
        $stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $stmt->bindParam(":accessToken", $accessToken, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $expiresAt = intval($row['expiresAt']);
            if ($expiresAt > 0 && time() > $expiresAt) {
                $removeStmt = $this->db->prepare("UPDATE access_data SET removeAt = (:removeAt) WHERE accountId = (:accountId) AND accessToken = (:accessToken)");
                $removeStmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
                $removeStmt->bindParam(":accessToken", $accessToken, PDO::PARAM_STR);
                $removeAt = time();
                $removeStmt->bindParam(":removeAt", $removeAt, PDO::PARAM_INT);
                $removeStmt->execute();
                return false;
            }
            return true;
        }

        return false;
    }

    public function setSession(int $accountId, string $accessToken): bool
    {
        $currentTime = time();

        $stmt = $this->db->prepare("SELECT id FROM access_data WHERE accountId = (:accountId) AND removeAt = 0 LIMIT 1");
        $stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $upd = $this->db->prepare("UPDATE access_data SET accessToken = (:accessToken), createAt = (:createAt) WHERE accountId = (:accountId) AND removeAt = 0");
            $upd->bindParam(":accountId", $accountId, PDO::PARAM_INT);
            $upd->bindParam(":accessToken", $accessToken, PDO::PARAM_STR);
            $upd->bindParam(":createAt", $currentTime, PDO::PARAM_INT);
            return $upd->execute();
        }

        $ins = $this->db->prepare("INSERT INTO access_data (accountId, accessToken, createAt, expiresAt) VALUES (:accountId, :accessToken, :createAt, :expiresAt)");
        $ins->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $ins->bindParam(":accessToken", $accessToken, PDO::PARAM_STR);
        $ins->bindParam(":createAt", $currentTime, PDO::PARAM_INT);
        $expiresAt = $currentTime + 3600;
        $ins->bindParam(":expiresAt", $expiresAt, PDO::PARAM_INT);
        return $ins->execute();
    }

    public function isSession(int $accountId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM access_data WHERE accountId = (:accountId) AND removeAt = 0 LIMIT 1");
        $stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
