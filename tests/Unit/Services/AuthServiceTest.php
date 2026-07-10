<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use PDO;

class AuthServiceTest extends TestCase
{
    private ?PDO $pdo = null;

    protected function getPDO(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER, DB_PASS,
                [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return $this->pdo;
    }

    protected function createUser(string $username): int
    {
        $pdo = $this->getPDO();
        $pdo->prepare("INSERT IGNORE INTO users (login, email, passw, points, regtime)
            VALUES (:l, :e, :pw, 1000, :t)")
            ->execute([':l' => $username, ':e' => $username . '@test.com', ':pw' => password_hash('test', PASSWORD_BCRYPT), ':t' => time()]);

        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :l");
        $stmt->execute([':l' => $username]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id'] : 0;
    }

    protected function cleanUser(string $username): void
    {
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :l");
        $stmt->execute([':l' => $username]);
        $row = $stmt->fetch();
        if ($row) {
            $pdo->prepare("DELETE FROM access_data WHERE accountId = :id")->execute([':id' => $row['id']]);
        }
        $pdo->prepare("DELETE FROM users WHERE login = :l")->execute([':l' => $username]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        try {
            $this->getPDO()->query("SELECT 1");
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database not available');
        }
    }

    public function test_auth_creates_and_authorizes_token(): void
    {
        $auth = new \auth($this->getPDO());
        $userId = $this->createUser('authtest');

        if ($userId === 0) {
            $this->markTestSkipped('Could not create test user');
        }

        $result = $auth->create($userId, 1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('accessToken', $result);
        $this->assertArrayHasKey('refreshToken', $result);
        $this->assertArrayHasKey('accountId', $result);

        $this->assertTrue($auth->authorize($result['accountId'], $result['accessToken']));
        $this->assertFalse($auth->authorize($result['accountId'], 'fake-token'));

        $auth->removeAll($userId);
        $this->cleanUser('authtest');
    }

    public function test_auth_remove_invalidates_token(): void
    {
        $auth = new \auth($this->getPDO());
        $userId = $this->createUser('authtest2');

        if ($userId === 0) {
            $this->markTestSkipped('Could not create test user');
        }

        $result = $auth->create($userId, 1);
        $this->assertTrue($auth->authorize($result['accountId'], $result['accessToken']));

        $auth->remove($result['accountId'], $result['accessToken']);
        $this->assertFalse($auth->authorize($result['accountId'], $result['accessToken']));

        $this->cleanUser('authtest2');
    }
}
