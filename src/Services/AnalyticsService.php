<?php

namespace FlyCash\Services;

use PDO;

class AnalyticsService
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function getTodayActiveUsers(): int
    {
        $today = strtotime(date("d-m-Y", time()));
        $stmt = $this->db->prepare("SELECT count(*) FROM users where last_access >= :today");
        $stmt->execute([':today' => $today]);
        return (int) $stmt->fetchColumn();
    }

    public function getNewUsers(): int
    {
        $today = strtotime(date("d-m-Y", time()));
        $stmt = $this->db->prepare("SELECT count(*) FROM users where regtime >= :today");
        $stmt->execute([':today' => $today]);
        return (int) $stmt->fetchColumn();
    }

    public function getTodaySessions(): int
    {
        $today = date("Y-m-d", time());
        $stmt = $this->db->prepare("SELECT sessions FROM analytics WHERE date = :today");
        $stmt->execute([':today' => $today]);
        $row = $stmt->fetchColumn();
        return $row > 0 ? (int) $row : 0;
    }
}
