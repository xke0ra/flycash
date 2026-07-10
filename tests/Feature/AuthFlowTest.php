<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    private string $testUser = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->testUser = 'auth_' . bin2hex(random_bytes(4));
        try {
            $this->getPDO()->query("SELECT 1");
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database not available');
        }
    }

    protected function tearDown(): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("DELETE FROM users WHERE login LIKE 'auth\\_%'")->execute();
        $pdo->prepare("DELETE FROM tracker WHERE username LIKE 'auth\\_%'")->execute();
        $pdo->prepare("DELETE FROM access_data WHERE accountId = 0")->execute();
        parent::tearDown();
    }

    // ─── signup ──────────────────────────────────────────────

    public function test_signup_creates_user_successfully(): void
    {
        $pdo = $this->getPDO();
        $account = new \account($pdo);
        $result = $account->signup($this->testUser, 'Test User', 'StrongPass1!', $this->testUser . '@test.com');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertFalse($result['error'], 'Signup should succeed');
        $this->assertArrayHasKey('accountId', $result);
        $this->assertGreaterThan(0, $result['accountId']);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = :id");
        $stmt->execute([':id' => $result['accountId']]);
        $this->assertSame(1, (int)$stmt->fetchColumn());
    }

    public function test_signup_rejects_duplicate_username(): void
    {
        $pdo = $this->getPDO();
        $account = new \account($pdo);

        $account->signup($this->testUser, 'Test User', 'StrongPass1!', $this->testUser . '@test.com');
        $result = $account->signup($this->testUser, 'Test User 2', 'OtherPass1!', 'other_' . $this->testUser . '@test.com');

        $this->assertArrayHasKey('error', $result);
        $this->assertTrue($result['error']);
    }

    public function test_signup_rejects_invalid_email(): void
    {
        $pdo = $this->getPDO();
        $account = new \account($pdo);
        $result = $account->signup($this->testUser, 'Test User', 'StrongPass1!', 'not-an-email');

        $this->assertArrayHasKey('error', $result);
        $this->assertTrue($result['error']);
    }

    public function test_signup_with_referral_returns_success(): void
    {
        $pdo = $this->getPDO();

        // Create referrer user
        $referrer = 'referrer_' . bin2hex(random_bytes(4));
        $pdo->prepare("INSERT INTO users (login, email, passw, points, regtime, refer, email_verified)
            VALUES (:l, :e, :pw, 500, :t, :r, 1)")
            ->execute([':l' => $referrer, ':e' => $referrer . '@test.com', ':pw' => password_hash('test', PASSWORD_BCRYPT), ':t' => time(), ':r' => 'REFCODE']);

        $account = new \account($pdo);
        $result = $account->signup($this->testUser, 'Test User', 'StrongPass1!', $this->testUser . '@test.com', 'REFCODE');

        $this->assertFalse($result['error'], 'Signup with valid referral code should succeed');

        $pdo->prepare("DELETE FROM users WHERE login = :l")->execute([':l' => $referrer]);
        $pdo->prepare("DELETE FROM tracker WHERE username = :u")->execute([':u' => $referrer]);
    }

    // ─── signin ──────────────────────────────────────────────

    public function test_signin_with_username_succeeds(): void
    {
        $pdo = $this->getPDO();
        $this->createTestUser($this->testUser, $this->testUser . '@test.com');
        $pdo->prepare("UPDATE users SET email_verified = 1 WHERE login = :l")->execute([':l' => $this->testUser]);

        $account = new \account($pdo);
        $result = $account->signin($this->testUser, 'testpass');

        $this->assertArrayHasKey('error', $result);
        $this->assertFalse($result['error']);
        $this->assertArrayHasKey('accountId', $result);
        $this->assertGreaterThan(0, $result['accountId']);
    }

    public function test_signin_with_email_succeeds(): void
    {
        $pdo = $this->getPDO();
        $email = $this->testUser . '@test.com';
        $this->createTestUser($this->testUser, $email);
        $pdo->prepare("UPDATE users SET email_verified = 1 WHERE login = :l")->execute([':l' => $this->testUser]);

        $account = new \account($pdo);
        $result = $account->signin($email, 'testpass');

        $this->assertFalse($result['error']);
        $this->assertGreaterThan(0, $result['accountId']);
    }

    public function test_signin_with_wrong_password_fails(): void
    {
        $pdo = $this->getPDO();
        $this->createTestUser($this->testUser, $this->testUser . '@test.com');
        $pdo->prepare("UPDATE users SET email_verified = 1 WHERE login = :l")->execute([':l' => $this->testUser]);

        $account = new \account($pdo);
        $result = $account->signin($this->testUser, 'wrongpassword');

        $this->assertTrue($result['error']);
    }

    public function test_signin_with_nonexistent_user_fails(): void
    {
        $pdo = $this->getPDO();
        $account = new \account($pdo);
        $result = $account->signin('nonexistent_' . bin2hex(random_bytes(4)), 'SomePass1');

        $this->assertTrue($result['error']);
    }
}
