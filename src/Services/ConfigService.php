<?php

namespace FlyCash\Services;

use PDO;

class ConfigService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $stmt = $this->db->prepare("SELECT config_value FROM configuration WHERE config_name = :key LIMIT 1");
        $stmt->execute([':key' => $key]);
        $result = $stmt->fetchColumn();
        return $result !== false ? $result : $default;
    }

    public function getInt(string $key, int $default = 0): int
    {
        $value = $this->get($key);
        return $value !== null ? (int) $value : $default;
    }

    public function getBool(string $key, bool $default = false): bool
    {
        $value = $this->get($key);
        if ($value === null) {
            return $default;
        }
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function getFloat(string $key, float $default = 0.0): float
    {
        $value = $this->get($key);
        return $value !== null ? (float) $value : $default;
    }

    /** @return array<string, string|null> */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT config_name, config_value FROM configuration");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $config = [];
        foreach ($rows as $row) {
            $config[$row['config_name']] = $row['config_value'];
        }
        return $config;
    }
}
