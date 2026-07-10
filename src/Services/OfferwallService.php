<?php

namespace FlyCash\Services;

use PDO;

class OfferwallService
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    /** @return array<string, mixed> */
    public function getOfferwalls(int $requestId = 0, int $limit = 0, int $offset = 0): array
    {
        if ($requestId === 0) {
            $requestId = 601;
        }

        $requests = [
            "error" => false,
            "error_code" => defined('ERROR_SUCCESS') ? ERROR_SUCCESS : 0,
            "offerwalls" => [],
        ];

        $sql = "SELECT * FROM offerwalls WHERE id < :requestId ORDER BY position ASC";
        if ($limit > 0) {
            $sql .= " LIMIT " . $limit;
        }
        if ($offset > 0) {
            $sql .= " OFFSET " . $offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            while ($row = $stmt->fetch()) {
                $requests['offerwalls'][] = [
                    "offer_id" => $row['id'],
                    "offer_title" => $row['name'],
                    "offer_subtitle" => $row['subtitle'],
                    "offer_url" => $row['url'],
                    "offer_type" => $row['type'],
                    "offer_points" => $row['points'],
                    "offer_featured" => (bool)$row['featured'],
                    "offer_thumbnail" => $row['image'],
                    "offer_position" => $row['position'],
                    "offer_status" => $row['status'] == 1 ? 'Active' : 'Disabled',
                ];
            }
        }

        return $requests;
    }

    /** @return array<string, mixed> */
    public function getSingleOfferwall(int $id): array
    {
        $result = [
            "error" => true,
            "error_code" => defined('ERROR_ACCOUNT_ID') ? ERROR_ACCOUNT_ID : 400,
        ];

        $stmt = $this->db->prepare("SELECT * FROM offerwalls WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $result = [
                "offer_id" => $row['id'],
                "offer_title" => $row['name'],
                "offer_subtitle" => $row['subtitle'],
                "offer_url" => $row['url'],
                "offer_type" => $row['type'],
                "offer_points" => $row['points'],
                "offer_featured" => (bool)$row['featured'],
                "offer_thumbnail" => $row['image'],
                "offer_position" => $row['position'],
                "offer_status" => $row['status'] == 1 ? 'Active' : 'Disabled',
            ];
        }

        return $result;
    }

    public function isWhitelisted(string $ip): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM whitelists WHERE ip_addr = (:ip) LIMIT 1");
        $stmt->bindParam(":ip", $ip, PDO::PARAM_STR);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }
}
