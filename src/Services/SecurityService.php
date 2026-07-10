<?php

namespace FlyCash\Services;

use PDO;

class SecurityService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function isIpBanned(string $ip): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM banned_ips WHERE ip_addr = :ip AND expires_at > :now LIMIT 1");
        $stmt->execute(array(':ip' => $ip, ':now' => time()));
        return $stmt->rowCount() > 0;
    }

    public function banIp(string $ip, string $reason = 'Too many failed attempts', int $durationMinutes = 15): void
    {
        $now = time();
        $expires = $now + ($durationMinutes * 60);
        $stmt = $this->db->prepare("INSERT INTO banned_ips (ip_addr, reason, banned_at, expires_at) VALUES (:ip, :reason, :now, :exp)");
        $stmt->execute(array(':ip' => $ip, ':reason' => $reason, ':now' => $now, ':exp' => $expires));
        $stmt = $this->db->prepare("DELETE FROM rate_limits WHERE identifier = :ip AND window_start < :old");
        $stmt->execute(array(':ip' => $ip, ':old' => $now));
    }

    public function logFailedAttempt(string $identifier): void
    {
        $maxFail = 10;
        $windowSec = 900;
        $now = time();
        $ws = $now - $windowSec;

        $stmt = $this->db->prepare("SELECT SUM(attempts) FROM rate_limits WHERE identifier = :id AND action = 'failed_login' AND window_start > :ws");
        $stmt->execute(array(':id' => $identifier, ':ws' => $ws));
        $total = intval($stmt->fetchColumn()) + 1;

        $stmt = $this->db->prepare("INSERT INTO rate_limits (identifier, action, attempts, window_start) VALUES (:id, 'failed_login', 1, :now)");
        $stmt->execute(array(':id' => $identifier, ':now' => $now));

        if ($total >= $maxFail) {
            $this->banIp($identifier, 'Auto-ban: ' . $total . ' failed attempts');
            return;
        }

        $stmt = $this->db->prepare("DELETE FROM rate_limits WHERE window_start < :old");
        $stmt->execute(array(':old' => $now - $windowSec * 2));
    }

    public function checkRateLimit(string $identifier, string $action, int $maxAttempts = 10, int $windowSeconds = 60): bool
    {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        if ($this->isIpBanned($ip)) {
            return false;
        }

        $now = time();
        $windowStart = $now - $windowSeconds;

        $stmt = $this->db->prepare("SELECT SUM(attempts) FROM rate_limits WHERE identifier = :id AND action = :act AND window_start > :ws");
        $stmt->execute(array(':id' => $identifier, ':act' => $action, ':ws' => $windowStart));
        $total = intval($stmt->fetchColumn());

        if ($total >= $maxAttempts) {
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO rate_limits (identifier, action, attempts, window_start) VALUES (:id, :act, 1, :now)");
        $stmt->execute(array(':id' => $identifier, ':act' => $action, ':now' => $now));

        $stmt = $this->db->prepare("DELETE FROM rate_limits WHERE window_start < :old");
        $stmt->execute(array(':old' => $now - $windowSeconds * 2));

        return true;
    }

    public function isWhitelisted(string $ip): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM whitelists WHERE ip_addr = (:ip) LIMIT 1");
        $stmt->bindParam(":ip", $ip, PDO::PARAM_STR);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return true;
            }
        }

        return false;
    }
}
