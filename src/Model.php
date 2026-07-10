<?php

namespace FlyCash;

class Model
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function setTable(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    public function setPrimaryKey(string $key): static
    {
        $this->primaryKey = $key;
        return $this;
    }

    /** @return ?array<string, mixed> */
    public function find(string|int $id): ?array
    {
        return $this->db->selectOne(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id",
            [':id' => $id]
        );
    }

    /** @return ?array<string, mixed> */
    public function findBy(string $column, mixed $value): ?array
    {
        return $this->db->selectOne(
            "SELECT * FROM `{$this->table}` WHERE `{$column}` = :value",
            [':value' => $value]
        );
    }

    /**
     * @param array<string, mixed> $conditions
     * @return array<int, array<string, mixed>>
     */
    public function findAll(array $conditions = []): array
    {
        if (empty($conditions)) {
            return $this->db->select("SELECT * FROM `{$this->table}`");
        }

        $whereParts = [];
        $params = [];
        foreach ($conditions as $col => $val) {
            $whereParts[] = "`{$col}` = :{$col}";
            $params[":{$col}"] = $val;
        }

        return $this->db->select(
            "SELECT * FROM `{$this->table}` WHERE " . implode(' AND ', $whereParts),
            $params
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        return $this->db->select("SELECT * FROM `{$this->table}`");
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): string
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $conditions
     */
    public function update(array $data, array $conditions): int
    {
        return $this->db->update($this->table, $data, $conditions);
    }

    /** @param array<string, mixed> $conditions */
    public function delete(array $conditions): int
    {
        return $this->db->delete($this->table, $conditions);
    }

    /** @param array<string, mixed> $conditions */
    public function count(array $conditions = []): int
    {
        if (empty($conditions)) {
            $stmt = $this->db->query("SELECT COUNT(*) FROM `{$this->table}`");
        } else {
            $whereParts = [];
            $params = [];
            foreach ($conditions as $col => $val) {
                $whereParts[] = "`{$col}` = :{$col}";
                $params[":{$col}"] = $val;
            }
            $stmt = $this->db->query(
                "SELECT COUNT(*) FROM `{$this->table}` WHERE " . implode(' AND ', $whereParts),
                $params
            );
        }
        return (int) $stmt->fetchColumn();
    }

    /** @param array<string, mixed> $conditions */
    public function exists(array $conditions): bool
    {
        return $this->count($conditions) > 0;
    }

    /**
     * @param array<int, mixed> $params
     * @return array<int, array<string, mixed>>
     */
    public function raw(string $sql, array $params = []): array
    {
        return $this->db->select($sql, $params);
    }

    /**
     * @param array<int, mixed> $params
     * @return ?array<string, mixed>
     */
    public function rawOne(string $sql, array $params = []): ?array
    {
        return $this->db->selectOne($sql, $params);
    }

    public function db(): Database
    {
        return $this->db;
    }
}
