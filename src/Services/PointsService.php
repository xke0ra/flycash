<?php

namespace FlyCash\Services;

use PDO;

class PointsService
{
    private PDO $db;

    /** @var array<string, string|null> */
    private static array $configCache = [];

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function creditUserPoints(string $username, int $points, string $type, string $description = '', bool $notify = true, bool $push = true, ?\account $account = null): bool
    {
        $result = false;
        $timeCurrent = time();
        $delta = intval($points);

        $stmt = $this->db->prepare("SELECT id, gcm_regid FROM users WHERE login = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user) {
            $userId = (int)$user['id'];

            try {
                $this->db->beginTransaction();

                $sql = "UPDATE users SET points = points + :delta WHERE login = :username AND points + :delta >= 0";
                $st = $this->db->prepare($sql);
                $st->execute([':delta' => $delta, ':username' => $username]);

                if ($st->rowCount() === 0) {
                    $this->db->rollBack();
                    return false;
                }

                $sql2 = "INSERT INTO tracker (user_id, username, points, type, date) VALUES (:uid, :username, :points, :type, :timeCurrent)";
                $st2 = $this->db->prepare($sql2);
                $st2->execute([':uid' => $userId, ':username' => $username, ':points' => $points, ':type' => $type, ':timeCurrent' => $timeCurrent]);

                $this->db->commit();
                $result = true;
            } catch (\Exception $e) {
                $this->db->rollBack();
                return false;
            }

            if ($notify) {
                $notif = new \notifications($this->db);
                $notif->add($username, $type, $description ?: $type . ' ' . $points . ' points', $points);
            }

            if ($push && !empty($user['gcm_regid'])) {
                $this->sendPush($user['gcm_regid'], 'credit', $points, 'none', 'none');
            }
        }

        return $result;
    }

    public function getCurrentTotalPoints(): int
    {
        $sql = "SELECT SUM(points) FROM users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getTotalTodayPoints(): int
    {
        $today = strtotime(date("d-m-Y", time()));
        $type = "Daily Checkin Credit Test Credit";
        $stmt = $this->db->prepare("SELECT SUM(points) FROM tracker where (date >= :today AND type != :type)");
        $stmt->execute([':today' => $today, ':type' => $type]);
        return (int) $stmt->fetchColumn();
    }

    public function getTotalYesterdayPoints(): int
    {
        $time = time();
        $oldtime = $time - 24 * 3600;
        $today = strtotime(date("d-m-Y", $time));
        $yesterday = strtotime(date("d-m-Y", $oldtime));
        $type = "Daily Checkin Credit Test Credit";
        $stmt = $this->db->prepare("SELECT SUM(points) FROM tracker where (date BETWEEN :yesterday AND :today) AND type != :type");
        $stmt->execute([':yesterday' => $yesterday, ':today' => $today, ':type' => $type]);
        $number_of_rows = $stmt->fetchColumn();

        if ($number_of_rows < 1) {
            $number_of_rows = 1;
        }

        return (int) $number_of_rows;
    }

    public function getTotalAllTimePoints(): int
    {
        $type = "Daily Checkin Credit Test Credit";
        $stmt = $this->db->prepare("SELECT SUM(points) FROM tracker where type != :type");
        $stmt->execute([':type' => $type]);
        return (int) $stmt->fetchColumn();
    }

    public function getTotalMonthPoints(): int
    {
        $type = "Daily Checkin Credit Test Credit";
        $time = strtotime("01-" . date("m-Y", time()));
        $month = strtotime(date("Y-m-d", $time));
        $stmt = $this->db->prepare("SELECT SUM(points) FROM tracker where (date >= :month AND type != :type)");
        $stmt->execute([':month' => $month, ':type' => $type]);
        return (int) $stmt->fetchColumn();
    }

    public function getTotalWeekPoints(): int
    {
        $type = "Daily Checkin Credit Test Credit";
        $day = date('w');
        $week = strtotime(date('Y-m-d', strtotime('-' . $day . ' days')));
        $stmt = $this->db->prepare("SELECT SUM(points) FROM tracker where (date >= :week AND type != :type)");
        $stmt->execute([':week' => $week, ':type' => $type]);
        return (int) $stmt->fetchColumn();
    }

    public function creditUserPointsByUserId(int $userId, int $points, string $type, string $description = '', bool $notify = true, bool $push = true): bool
    {
        $result = false;
        $timeCurrent = time();
        $delta = intval($points);
        $login = '';

        $stmt = $this->db->prepare("SELECT login, gcm_regid FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch();

        if ($user) {
            $login = $user['login'];

            try {
                $this->db->beginTransaction();

                $sql = "UPDATE users SET points = points + :delta WHERE id = :id AND points + :delta >= 0";
                $st = $this->db->prepare($sql);
                $st->execute([':delta' => $delta, ':id' => $userId]);

                if ($st->rowCount() === 0) {
                    $this->db->rollBack();
                    return false;
                }

                $sql2 = "INSERT INTO tracker (user_id, username, points, type, date) VALUES (:uid, :uname, :points, :type, :timeCurrent)";
                $st2 = $this->db->prepare($sql2);
                $st2->execute([':uid' => $userId, ':uname' => $login, ':points' => $points, ':type' => $type, ':timeCurrent' => $timeCurrent]);

                $this->db->commit();
                $result = true;
            } catch (\Exception $e) {
                $this->db->rollBack();
                return false;
            }

            if ($notify) {
                $notif = new \notifications($this->db);
                $notif->add($username, $type, $description ?: $type . ' ' . $points . ' points', $points);
            }

            if ($push && !empty($user['gcm_regid'])) {
                $this->sendPush($user['gcm_regid'], 'credit', $points, 'none', 'none');
            }
        }

        return $result;
    }

    public function getUserPoints(string $username): int
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE login = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $userId = (int)$stmt->fetchColumn();
        if ($userId === 0) {
            return 0;
        }
        return $this->getUserPointsByUserId($userId);
    }

    public function getUserPointsByUserId(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT SUM(points) FROM tracker WHERE user_id = :userId");
        $stmt->execute([':userId' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function getTotalEarnedPoints(): int
    {
        $sql = "SELECT SUM(points) FROM users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
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

    private function sendPush(string $fcm_id, string $title, $message, string $image, string $type)
    {
        $GOOGLE_API_KEY = $this->getConfig("FIREBASE_API_KEY");

        $fields = [
            'to'       => $fcm_id,
            'priority' => "high",
            'data'     => ["title" => $title, "message" => $message, "image" => $image, "type" => $type],
        ];

        $headers = [
            'https://fcm.googleapis.com/fcm/send',
            'Content-Type: application/json',
            'Authorization: key=' . $GOOGLE_API_KEY,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        if ($result === false) {
            if (isset($GLOBALS['logger'])) {
                $GLOBALS['logger']->error('cURL error in sendPush', ['curl_error' => curl_error($ch)]);
            }
            if (isset($GLOBALS['logger'])) {
                $GLOBALS['logger']->error('sendPush failed (cURL)', ['curl_error' => curl_error($ch)]);
            }
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        return $result;
    }
}
