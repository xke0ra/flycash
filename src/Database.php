<?php

namespace FlyCash;

use PDO;
use PDOStatement;
use PDOException;

class Database
{
    private PDO $pdo;
    private int $totalQueries = 0;
    private float $totalTime = 0.0;

    public function __construct(?PDO $pdo = null)
    {
        if ($pdo !== null) {
            $this->pdo = $pdo;
        } else {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function getTotalQueries(): int
    {
        return $this->totalQueries;
    }

    public function getTotalTime(): float
    {
        return $this->totalTime;
    }

    public function prepare(string $sql): PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

    /** @param array<int|string, mixed> $params */
    public function execute(PDOStatement $stmt, array $params = []): PDOStatement
    {
        $start = microtime(true);
        $stmt->execute($params);
        $time = (microtime(true) - $start) * 1000;

        $this->totalQueries++;
        $this->totalTime += $time;

        Logger::debug('Query executed', [
            'sql' => $stmt->queryString,
            'params' => $params,
            'time_ms' => round($time, 2),
        ]);

        return $stmt;
    }

    /** @param array<int|string, mixed> $params */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->prepare($sql);
        return $this->execute($stmt, $params);
    }

    /**
     * @param array<int|string, mixed> $params
     * @return array<int, array<string, mixed>>
     */
    public function select(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * @param array<int|string, mixed> $params
     * @return ?array<string, mixed>
     */
    public function selectOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** @param array<string, mixed> $data */
    public function insert(string $table, array $data): string
    {
        $columns = implode('`, `', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO `{$table}` (`{$columns}`) VALUES ({$placeholders})";
        $this->query($sql, $data);

        return $this->pdo->lastInsertId();
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $conditions
     */
    public function update(string $table, array $data, array $conditions): int
    {
        $setParts = [];
        foreach (array_keys($data) as $col) {
            $setParts[] = "`{$col}` = :set_{$col}";
        }

        $whereParts = [];
        foreach (array_keys($conditions) as $col) {
            $whereParts[] = "`{$col}` = :where_{$col}";
        }

        $params = [];
        foreach ($data as $col => $val) {
            $params["set_{$col}"] = $val;
        }
        foreach ($conditions as $col => $val) {
            $params["where_{$col}"] = $val;
        }

        $sql = "UPDATE `{$table}` SET " . implode(', ', $setParts) . " WHERE " . implode(' AND ', $whereParts);
        $stmt = $this->query($sql, $params);

        return $stmt->rowCount();
    }

    /** @param array<string, mixed> $conditions */
    public function delete(string $table, array $conditions): int
    {
        $whereParts = [];
        foreach (array_keys($conditions) as $col) {
            $whereParts[] = "`{$col}` = :{$col}";
        }

        $sql = "DELETE FROM `{$table}` WHERE " . implode(' AND ', $whereParts);
        $stmt = $this->query($sql, $conditions);

        return $stmt->rowCount();
    }

    public function beginTransaction(): bool
    {
        Logger::debug('Beginning transaction');
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        Logger::debug('Committing transaction');
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        Logger::warning('Rolling back transaction');
        return $this->pdo->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
