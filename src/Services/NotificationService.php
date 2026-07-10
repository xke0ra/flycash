<?php

namespace FlyCash\Services;

use PDO;

class NotificationService
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function add(int $userId, string $title, string $message, int $points = 0, string $type = 'credit'): bool
    {
        $time = time();
        $sql = "INSERT INTO notifications (user_id, username, title, message, points, type, created_at) VALUES (:uid, '', :title, :message, :points, :type, :created_at)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':uid' => $userId,
            ':title' => $title,
            ':message' => $message,
            ':points' => $points,
            ':type' => $type,
            ':created_at' => $time,
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    public function getUnread(int $userId, int $limit = 10): array
    {
        $sql = "SELECT * FROM notifications WHERE user_id = :userId AND is_read = 0 ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead(int $id, int $userId): bool
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :userId";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':userId' => $userId]);
    }

    public function markAllAsRead(int $userId): bool
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = :userId AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':userId' => $userId]);
    }

    public function countUnread(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :userId AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return (int) $stmt->fetchColumn();
    }
}
