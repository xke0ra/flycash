<?php

class notifications {

    private $db;

    public function __construct($dbo) {
        $this->db = $dbo;
    }

    public function add($userId, $title, $message, $points = 0, $type = 'credit') {
        $time = time();
        $sql = "INSERT INTO notifications (user_id, username, title, message, points, type, created_at) VALUES (:uid, '', :title, :message, :points, :type, :created_at)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array(
            ':uid' => $userId,
            ':title' => $title,
            ':message' => $message,
            ':points' => $points,
            ':type' => $type,
            ':created_at' => $time
        ));
    }

    public function getUnread($userId, $limit = 10) {
        $sql = "SELECT * FROM notifications WHERE user_id = :userId AND is_read = 0 ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($id, $userId) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :userId";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array(':id' => $id, ':userId' => $userId));
    }

    public function markAllAsRead($userId) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = :userId AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array(':userId' => $userId));
    }

    public function countUnread($userId) {
        $sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :userId AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':userId' => $userId));
        return (int) $stmt->fetchColumn();
    }
}
