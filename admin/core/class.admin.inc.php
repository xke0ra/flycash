<?php
class admin extends db_connect
{

	private $requestFrom = 0;
    private $id = 0;

	public function __construct($dbo = NULL)
    {
		parent::__construct($dbo);
	}

    public function getCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM admins");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function signup($username, $password, $fullname)
    {

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        if (!helper::isCorrectLogin($username)) {

            $result = array("error" => true,
                            "error_code" => ERROR_UNKNOWN,
                            "error_type" => 0,
                            "error_description" => "Incorrect login");

            return $result;
        }

        if (!helper::isCorrectPassword($password)) {

            $result = array("error" => true,
                            "error_code" => ERROR_UNKNOWN,
                            "error_type" => 1,
                            "error_description" => "Incorrect password");

            return $result;
        }

        $passw_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $currentTime = time();

        $stmt = $this->db->prepare("INSERT INTO admins (username, salt, password, fullname, createAt) value (:username, :salt, :password, :fullname, :createAt)");
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->bindParam(":salt", $currentTime, PDO::PARAM_STR);
        $stmt->bindParam(":password", $passw_hash, PDO::PARAM_STR);
        $stmt->bindParam(":fullname", $fullname, PDO::PARAM_STR);
        $stmt->bindParam(":createAt", $currentTime, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $this->setId($this->db->lastInsertId());

            $result = array("error" => false,
                            'accountId' => $this->id,
                            'username' => $username,
                            'error_code' => ERROR_SUCCESS,
                            'error_description' => 'SignUp Success!');

            return $result;
        }

        return $result;
    }

    public function getRole($adminId)
    {
        $stmt = $this->db->prepare("SELECT a.role_id, r.name AS role_name, r.permissions FROM admins a LEFT JOIN admin_roles r ON a.role_id = r.id WHERE a.id = :id LIMIT 1");
        $stmt->bindParam(":id", $adminId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function hasPermission($adminId, $permission)
    {
        $role = $this->getRole($adminId);
        if (!$role) return false;
        if ($role['permissions'] === '*') return true;
        $perms = explode(',', $role['permissions']);
        return in_array($permission, $perms);
    }

    static function requirePermission($permission)
    {
        if (!isset($_SESSION['admin_role_permissions'])) {
            header("Location: /admin/");
            exit;
        }
        if ($_SESSION['admin_role_permissions'] === '*') return;
        $perms = explode(',', $_SESSION['admin_role_permissions']);
        if (!in_array($permission, $perms)) {
            header("Location: /admin/");
            exit;
        }
    }

    public function signin($username, $password)
    {
        $result = array('error' => true,
                        "error_code" => ERROR_UNKNOWN);

        $username = helper::clearText($username);
        $password = trim($password);

        $stmt = $this->db->prepare("SELECT id, salt, password, role_id FROM admins WHERE username = (:username) LIMIT 1");
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();

            if (strpos($row['password'], '$2y$') === 0 || strpos($row['password'], '$2a$') === 0) {

                if (password_verify($password, $row['password'])) {
                    $roleInfo = $this->getRole($row['id']);
                    $result = array("error" => false,
                                    "error_code" => ERROR_SUCCESS,
                                    "accountId" => $row['id'],
                                    "role_id" => intval($row['role_id']),
                                    "role_name" => $roleInfo ? $roleInfo['role_name'] : '');
                }

            } else {

                $passw_hash = md5(md5($password).$row['salt']);

                if ($passw_hash === $row['password']) {

                    $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    $upd = $this->db->prepare("UPDATE admins SET password = :hash WHERE id = :id");
                    $upd->execute(array(':hash' => $newHash, ':id' => $row['id']));

                    $roleInfo = $this->getRole($row['id']);
                    $result = array("error" => false,
                                    "error_code" => ERROR_SUCCESS,
                                    "accountId" => $row['id'],
                                    "role_id" => intval($row['role_id']),
                                    "role_name" => $roleInfo ? $roleInfo['role_name'] : '');
                }
            }
        }

        return $result;
    }

    public function setId($accountId)
    {
        $this->id = $accountId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setRequestFrom($requestFrom)
    {
        $this->requestFrom = $requestFrom;
    }

    public function getRequestFrom()
    {
        return $this->requestFrom;
    }

    static function isSession()
    {
        if (isset($_SESSION) && isset($_SESSION['admin_id'])) {

            return true;

        } else {

            return false;
        }
    }

    static function setSession($admin_id, $access_token, $admin_username = '', $rolePermissions = '')
    {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin_id;
        $_SESSION['admin_access_token'] = $access_token;
        if ($admin_username) {
            $_SESSION['admin_username'] = $admin_username;
        }
        if ($rolePermissions) {
            $_SESSION['admin_role_permissions'] = $rolePermissions;
        }
    }

    static function unsetSession()
    {
        session_regenerate_id(true);
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_access_token']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_role_permissions']);
    }

    static function getAdminUsername()
    {
        return isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'admin';
    }

    static function getAdminID()
    {
        if (isset($_SESSION) && isset($_SESSION['admin_id'])) {

            return $_SESSION['admin_id'];

        } else {

            return "undefined";
        }
    }

    static function getAccessToken()
    {
        if (isset($_SESSION) && isset($_SESSION['admin_access_token'])) {

            return $_SESSION['admin_access_token'];

        } else {

            return "undefined";
        }
    }

    static function createAccessToken()
    {
        $access_token = bin2hex(random_bytes(32));

        if (isset($_SESSION)) {

            $_SESSION['admin_access_token'] = $access_token;
        }
    }
}

