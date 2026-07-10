<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use PDO;

class PointsServiceTest extends TestCase
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

    protected function createTestUser(string $username): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("INSERT IGNORE INTO users (login, email, passw, points, regtime)
            VALUES (:l, :e, :pw, 1000, :t)")
            ->execute([':l' => $username, ':e' => $username . '@test.com', ':pw' => password_hash('test', PASSWORD_BCRYPT), ':t' => time()]);
    }

    protected function removeTestUser(string $username): void
    {
        $this->getPDO()->prepare("DELETE FROM users WHERE login = :l")->execute([':l' => $username]);
        $this->getPDO()->prepare("DELETE FROM tracker WHERE username = :u")->execute([':u' => $username]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Skip if no DB connection
        try {
            $this->getPDO()->query("SELECT 1");
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database not available: ' . $e->getMessage());
        }
    }

    public function test_credit_user_points_increases_balance(): void
    {
        $username = 'pstest_' . bin2hex(random_bytes(4));
        $pdo = $this->getPDO();

        $pdo->prepare("INSERT INTO users (login, email, passw, points, regtime)
            VALUES (:l, :e, :pw, 500, :t)")
            ->execute([':l' => $username, ':e' => $username . '@test.com', ':pw' => password_hash('test', PASSWORD_BCRYPT), ':t' => time()]);

        $service = new \FlyCash\Services\PointsService($pdo);
        $result = $service->creditUserPoints($username, 200, 'Test', 'Test credit', false, false);
        $this->assertTrue($result);

        $stmt = $pdo->prepare("SELECT points FROM users WHERE login = :l");
        $stmt->execute([':l' => $username]);
        $this->assertSame(700, (int)$stmt->fetchColumn());

        $pdo->prepare("DELETE FROM tracker WHERE username = :u")->execute([':u' => $username]);
        $pdo->prepare("DELETE FROM users WHERE login = :l")->execute([':l' => $username]);
    }

    public function test_credit_user_points_decreases_balance(): void
    {
        $username = 'pstest_' . bin2hex(random_bytes(4));
        $pdo = $this->getPDO();

        $pdo->prepare("INSERT INTO users (login, email, passw, points, regtime)
            VALUES (:l, :e, :pw, 1000, :t)")
            ->execute([':l' => $username, ':e' => $username . '@test.com', ':pw' => password_hash('test', PASSWORD_BCRYPT), ':t' => time()]);

        $service = new \FlyCash\Services\PointsService($pdo);
        $result = $service->creditUserPoints($username, -300, 'Redeem', 'Test redeem', false, false);
        $this->assertTrue($result);

        $stmt = $pdo->prepare("SELECT points FROM users WHERE login = :l");
        $stmt->execute([':l' => $username]);
        $this->assertSame(700, (int)$stmt->fetchColumn());

        $pdo->prepare("DELETE FROM tracker WHERE username = :u")->execute([':u' => $username]);
        $pdo->prepare("DELETE FROM users WHERE login = :l")->execute([':l' => $username]);
    }

    public function test_credit_user_points_rejects_negative_balance(): void
    {
        $username = 'pstest_' . bin2hex(random_bytes(4));
        $pdo = $this->getPDO();

        $pdo->prepare("INSERT INTO users (login, email, passw, points, regtime)
            VALUES (:l, :e, :pw, 100, :t)")
            ->execute([':l' => $username, ':e' => $username . '@test.com', ':pw' => password_hash('test', PASSWORD_BCRYPT), ':t' => time()]);

        $service = new \FlyCash\Services\PointsService($pdo);
        $result = $service->creditUserPoints($username, -500, 'Redeem', 'Test overdraft', false, false);
        $this->assertFalse($result);

        $stmt = $pdo->prepare("SELECT points FROM users WHERE login = :l");
        $stmt->execute([':l' => $username]);
        $this->assertSame(100, (int)$stmt->fetchColumn());

        $pdo->prepare("DELETE FROM tracker WHERE username = :u")->execute([':u' => $username]);
        $pdo->prepare("DELETE FROM users WHERE login = :l")->execute([':l' => $username]);
    }

    public function test_credit_user_points_by_user_id_uses_atomic_update(): void
    {
        $username = 'pstest_' . bin2hex(random_bytes(4));
        $pdo = $this->getPDO();

        $pdo->prepare("INSERT INTO users (login, email, passw, points, regtime)
            VALUES (:l, :e, :pw, 300, :t)")
            ->execute([':l' => $username, ':e' => $username . '@test.com', ':pw' => password_hash('test', PASSWORD_BCRYPT), ':t' => time()]);

        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :l");
        $stmt->execute([':l' => $username]);
        $userId = (int)$stmt->fetchColumn();

        $service = new \FlyCash\Services\PointsService($pdo);
        $result = $service->creditUserPointsByUserId($userId, 150, 'Test', 'Test by user id', false, false);
        $this->assertTrue($result);

        $stmt = $pdo->prepare("SELECT points FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $this->assertSame(450, (int)$stmt->fetchColumn());

        $pdo->prepare("DELETE FROM tracker WHERE username = :u")->execute([':u' => $username]);
        $pdo->prepare("DELETE FROM users WHERE id = :i")->execute([':i' => $userId]);
    }
}
