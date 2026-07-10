<?php

namespace FlyCash\Services;

use PDO;

class UserService
{
    private PDO $db;

    /** @var array<string, string|null> */
    private static array $configCache = [];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** @return ?array<string, mixed> */
    public function getUserInfo(int $userId): ?array
    {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $userId]);
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
        $stmt->execute([':value' => $value]);
        $dbarray = $stmt->fetch();

        if (!$dbarray) {
            return null;
        }

        return $dbarray;
    }

    public function updateUserAccess(int $userId): bool
    {
        $result = false;

        $ipaddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        $time = time();

        $stmt = $this->db->prepare("UPDATE users SET last_access = (:time),last_ip_addr = (:ipaddr) WHERE id = (:id)");
        $stmt->bindParam(":ipaddr", $ipaddr, PDO::PARAM_STR);
        $stmt->bindParam(":time", $time, PDO::PARAM_STR);
        $stmt->bindParam(":id", $userId, PDO::PARAM_INT);
        $result = $stmt->execute();

        return $result;
    }

    public function getUserReferredMembers(string $refererCode): int
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM users where referer = :referer");
        $stmt->execute([':referer' => $refererCode]);

        return (int) $stmt->fetchColumn();
    }

    public function getUserReferIncome(int $userId): int
    {
        $type = $this->getConfig('REFERER_BONUS_TITLE');
        $stmt = $this->db->prepare("SELECT SUM(points) FROM tracker WHERE user_id = :userId AND type = :type");
        $stmt->execute([':userId' => $userId, ':type' => $type]);

        $actual_referIncome = $stmt->fetchColumn();

        $userIncomeFromReferredMembers = 0;
        if ($actual_referIncome > 1) {
            $userIncomeFromReferredMembers = $actual_referIncome;
        }

        return (int) $userIncomeFromReferredMembers;
    }

    public function getUserRedeemedPoints(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT SUM(points_used) FROM redemptions WHERE user_id = :userId");
        $stmt->execute([':userId' => $userId]);
        $total = $stmt->fetchColumn();

        $userRedeemedPoints = 0;
        if ($total > 1) {
            $userRedeemedPoints = $total;
        }

        return (int) $userRedeemedPoints;
    }

    public function getDailyCheckinTimeLeft(int $userId): int
    {
        $timeLeft = -1;

        $checkinBonusTitle = $this->getConfig('CHECKIN_BONUS_TITLE');

        $sql = "SELECT * FROM tracker WHERE user_id = :userId AND type = :checkinBonusTitle ORDER BY id DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userId' => $userId, ':checkinBonusTitle' => $checkinBonusTitle]);

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();

            $timeData = $row['date'];

            $timeCurrent = time();

            $timeCalculated = $timeData + 24 * 3600;

            $difference = $timeCalculated - $timeCurrent;

            if ($timeCalculated > $timeCurrent) {

                $timeLeft = $difference;
            }
        }

        return $timeLeft;
    }

    private function getConfig(string $value): ?string
    {
        if (isset(self::$configCache[$value])) {
            return self::$configCache[$value];
        }
        $sql = "SELECT config_value FROM configuration WHERE config_name = :value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':value' => $value]);
        $result = $stmt->fetchColumn();
        self::$configCache[$value] = $result ?: null;
        return self::$configCache[$value];
    }
}
