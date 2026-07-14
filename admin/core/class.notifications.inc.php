<?php

class notifications {

    private $db;

    public function __construct($dbo) {
        $this->db = $dbo;
    }

    public function add($username, $title, $message, $points = 0, $type = 'credit') {
        $time = time();
        $sql = "INSERT INTO notifications (username, title, message, points, type, created_at) VALUES (:uname, :title, :message, :points, :type, :created_at)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array(
            ':uname' => $username,
            ':title' => $title,
            ':message' => $message,
            ':points' => $points,
            ':type' => $type,
            ':created_at' => $time
        ));
    }

    public function getUnread($username, $limit = 10) {
        $sql = "SELECT * FROM notifications WHERE username = :username AND is_read = 0 ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($id, $username) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id AND username = :username";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array(':id' => $id, ':username' => $username));
    }

    public function markAllAsRead($username) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE username = :username AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array(':username' => $username));
    }

    public function countUnread($username) {
        $sql = "SELECT COUNT(*) FROM notifications WHERE username = :username AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':username' => $username));
        return (int) $stmt->fetchColumn();
    }
}