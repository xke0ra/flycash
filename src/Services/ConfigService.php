<?php

namespace FlyCash\Services;

use PDO;

class ConfigService
{
    private PDO $db;

    /** @var array<string, string|null> */
    private array $cache = [];
    private bool $allLoaded = false;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** Clear instance cache (useful for testing with mutable database) */
    public function clearCache(): void
    {
        $this->cache = [];
        $this->allLoaded = false;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $stmt = $this->db->prepare("SELECT config_value FROM configuration WHERE config_name = :key LIMIT 1");
        $stmt->execute([':key' => $key]);
        $result = $stmt->fetchColumn();
        $value = $result !== false ? $result : $default;

        $this->cache[$key] = $value;
        return $value;
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
        if ($this->allLoaded) {
            return $this->cache;
        }

        $stmt = $this->db->query("SELECT config_name, config_value FROM configuration");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $config = [];
        foreach ($rows as $row) {
            $config[$row['config_name']] = $row['config_value'];
        }

        $this->cache = $config;
        $this->allLoaded = true;
        return $config;
    }
}
