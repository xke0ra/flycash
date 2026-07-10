<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PDO;

abstract class TestCase extends BaseTestCase
{
    protected ?PDO $pdo = null;

    protected function getPDO(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }
        return $this->pdo;
    }

    protected function createTestUser(string $username = 'testuser', string $email = 'test@example.com'): int
    {
        $pdo = $this->getPDO();
        $time = time();

        $stmt = $pdo->prepare(
            "INSERT INTO users (login, email, passw, points, regtime)
             VALUES (:login, :email, :passw, :points, :regtime)"
        );
        $stmt->execute([
            ':login' => $username,
            ':email' => $email,
            ':passw' => password_hash('testpass', PASSWORD_BCRYPT),
            ':points' => 1000,
            ':regtime' => $time,
        ]);

        return (int)$pdo->lastInsertId();
    }

    protected function removeTestUser(string $username = 'testuser'): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("DELETE FROM users WHERE login = :login")->execute([':login' => $username]);
        $pdo->prepare("DELETE FROM tracker WHERE username = :username")->execute([':username' => $username]);
    }

    protected function addWhitelistIp(string $ip = '127.0.0.1'): void
    {
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare("INSERT IGNORE INTO whitelists (name, ip_addr) VALUES ('test', :ip)");
        $stmt->execute([':ip' => $ip]);
    }

    protected function removeWhitelistIp(string $ip = '127.0.0.1'): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("DELETE FROM whitelists WHERE ip_addr = :ip")->execute([':ip' => $ip]);
    }
}
