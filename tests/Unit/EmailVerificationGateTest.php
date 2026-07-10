<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PDO;

class EmailVerificationGateTest extends TestCase
{
    private ?PDO $pdo = null;
    private string $testUser = '';
    private string $testEmail = '';

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

    protected function setUp(): void
    {
        parent::setUp();
        try {
            $this->getPDO()->query("SELECT 1");
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database not available');
        }
        $rand = bin2hex(random_bytes(4));
        $this->testUser = 'evgate_' . $rand;
        $this->testEmail = $this->testUser . '@test.com';
    }

    protected function tearDown(): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("DELETE FROM users WHERE login LIKE 'evgate\\_%'")->execute();
        $pdo->prepare("DELETE FROM access_data WHERE accountId = 0")->execute();
        parent::tearDown();
    }

    private function createUnverifiedUser(): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("INSERT INTO users (login, email, passw, points, regtime, email_verified)
            VALUES (:l, :e, :pw, 1000, :t, 0)")
            ->execute([
                ':l' => $this->testUser,
                ':e' => $this->testEmail,
                ':pw' => password_hash('testpass', PASSWORD_BCRYPT),
                ':t' => time()
            ]);
    }

    // ─── account::signin gate ─────────────────────────────────

    public function test_account_signin_rejects_unverified_email(): void
    {
        $originalEnv = $_ENV['APP_ENV'] ?? null;
        unset($_ENV['APP_ENV']);

        $this->createUnverifiedUser();
        $account = new \account($this->getPDO());
        $result = $account->signin($this->testUser, 'testpass');

        $this->assertTrue($result['error']);
        $this->assertSame(403, $result['error_code']);
        $this->assertStringContainsString('Email not verified', $result['error_description']);

        if ($originalEnv !== null) {
            $_ENV['APP_ENV'] = $originalEnv;
        } else {
            unset($_ENV['APP_ENV']);
        }
    }

    public function test_account_signin_allows_unverified_in_dev(): void
    {
        $originalEnv = $_ENV['APP_ENV'] ?? null;
        $_ENV['APP_ENV'] = 'development';

        $this->createUnverifiedUser();
        $account = new \account($this->getPDO());
        $result = $account->signin($this->testUser, 'testpass');

        $this->assertFalse($result['error']);

        if ($originalEnv !== null) {
            $_ENV['APP_ENV'] = $originalEnv;
        } else {
            unset($_ENV['APP_ENV']);
        }
    }

    // ─── AuthService::signin gate ─────────────────────────────

    public function test_authservice_signin_rejects_unverified_email(): void
    {
        $originalEnv = $_ENV['APP_ENV'] ?? null;
        unset($_ENV['APP_ENV']);

        $this->createUnverifiedUser();
        $service = new \FlyCash\Services\AuthService($this->getPDO());
        $result = $service->signin($this->testUser, 'testpass');

        $this->assertTrue($result['error']);
        $this->assertSame(403, $result['error_code']);
        $this->assertStringContainsString('Email not verified', $result['error_description']);

        if ($originalEnv !== null) {
            $_ENV['APP_ENV'] = $originalEnv;
        } else {
            unset($_ENV['APP_ENV']);
        }
    }

    public function test_authservice_signin_allows_unverified_in_dev(): void
    {
        $originalEnv = $_ENV['APP_ENV'] ?? null;
        $_ENV['APP_ENV'] = 'development';

        $this->createUnverifiedUser();
        $service = new \FlyCash\Services\AuthService($this->getPDO());
        $result = $service->signin($this->testUser, 'testpass');

        $this->assertFalse($result['error']);

        if ($originalEnv !== null) {
            $_ENV['APP_ENV'] = $originalEnv;
        } else {
            unset($_ENV['APP_ENV']);
        }
    }
}
