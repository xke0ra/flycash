<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminLoginFlowTest extends TestCase
{
    private string $testUser = 'admintest_' . '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->testUser = 'admintest_' . bin2hex(random_bytes(4));

        try {
            $this->getPDO()->query("SELECT 1");
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database not available');
        }
    }

    protected function tearDown(): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("DELETE FROM users WHERE login = :u")->execute([':u' => $this->testUser]);
        $pdo->prepare("DELETE FROM access_data WHERE accountId = :aid")->execute([':aid' => 0]);
        $pdo->prepare("DELETE FROM tracker WHERE username = :u")->execute([':u' => $this->testUser]);
        parent::tearDown();
    }

    public function test_admin_login_with_special_chars_password(): void
    {
        $pdo = $this->getPDO();
        $specialPassword = "Test&Pass'word\"<More>";

        // Create user with special chars password (mimicking signup with trim only)
        $hash = password_hash($specialPassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare(
            "INSERT INTO users (login, email, passw, state, email_verified, points, regtime)
             VALUES (:login, :email, :passw, 0, 1, 100, :regtime)"
        );
        $stmt->execute([
            ':login' => $this->testUser,
            ':email' => $this->testUser . '@test.com',
            ':passw' => $hash,
            ':regtime' => time(),
        ]);

        // Now try to login via the admin path (LoginController)
        $controller = new \FlyCash\Controller\LoginController($pdo);
        $result = $controller->login($this->testUser, $specialPassword, 0);

        $this->assertArrayHasKey('error', $result, 'Login result must contain error key');
        $this->assertFalse($result['error'], 'Login should succeed with special chars password');
        $this->assertArrayHasKey('accountId', $result, 'Login result must contain accountId');
        $this->assertIsInt($result['accountId'], 'accountId must be an integer');
        $this->assertGreaterThan(0, $result['accountId'], 'accountId must be positive');
        $this->assertArrayHasKey('accessToken', $result, 'Login result must contain accessToken');
        $this->assertNotEmpty($result['accessToken'], 'accessToken must not be empty');
    }

    public function test_admin_login_wrong_password_fails(): void
    {
        $pdo = $this->getPDO();
        $password = "NormalPass123";

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare(
            "INSERT INTO users (login, email, passw, state, email_verified, points, regtime)
             VALUES (:login, :email, :passw, 0, 1, 100, :regtime)"
        );
        $stmt->execute([
            ':login' => $this->testUser,
            ':email' => $this->testUser . '@test.com',
            ':passw' => $hash,
            ':regtime' => time(),
        ]);

        $controller = new \FlyCash\Controller\LoginController($pdo);
        $result = $controller->login($this->testUser, 'WrongPassword', 0);

        $this->assertArrayHasKey('error', $result);
        $this->assertTrue($result['error'], 'Login with wrong password should fail');
    }

    public function test_admin_login_blocked_account_fails(): void
    {
        $pdo = $this->getPDO();
        $password = "BlockedUser99";

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare(
            "INSERT INTO users (login, email, passw, state, email_verified, points, regtime)
             VALUES (:login, :email, :passw, 2, 1, 100, :regtime)"
        );
        $stmt->execute([
            ':login' => $this->testUser,
            ':email' => $this->testUser . '@test.com',
            ':passw' => $hash,
            ':regtime' => time(),
        ]);

        $controller = new \FlyCash\Controller\LoginController($pdo);
        $result = $controller->login($this->testUser, $password, 0);

        $this->assertArrayHasKey('error', $result);
        $this->assertTrue($result['error'], 'Login with blocked account should fail');
    }
}
