<?php

namespace Tests\Unit\Services;

use Tests\TestCase;

class EmailVerificationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        try {
            $this->getPDO()->query("SELECT 1");
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database not available');
        }
    }

    public function test_is_email_verified_returns_false_for_unverified(): void
    {
        $userId = $this->createTestUser('emailtest1', 'emailtest1@test.com');
        $service = new \FlyCash\Services\EmailVerificationService($this->getPDO());

        $this->assertFalse($service->isEmailVerified($userId));

        $this->removeTestUser('emailtest1');
    }

    public function test_verify_email(): void
    {
        $userId = $this->createTestUser('emailtest2', 'emailtest2@test.com');
        $service = new \FlyCash\Services\EmailVerificationService($this->getPDO());
        $pdo = $this->getPDO();

        $hash = bin2hex(random_bytes(32));
        $pdo->prepare("INSERT INTO email_verify (accountId, email, hash, createAt, expiresAt) VALUES (:aid, :email, :hash, :ca, :ea)")
            ->execute([':aid' => $userId, ':email' => 'emailtest2@test.com', ':hash' => $hash, ':ca' => time(), ':ea' => time() + 86400]);

        $result = $service->verifyEmail($hash);
        $this->assertTrue($result);
        $this->assertTrue($service->isEmailVerified($userId));

        $pdo->prepare("DELETE FROM email_verify WHERE accountId = :aid")->execute([':aid' => $userId]);
        $this->removeTestUser('emailtest2');
    }

    public function test_send_verification_email_returns_false_without_smtp(): void
    {
        $userId = $this->createTestUser('emailtest3', 'emailtest3@test.com');
        $service = new \FlyCash\Services\EmailVerificationService($this->getPDO());

        $result = $service->sendVerificationEmail($userId);
        $this->assertFalse($result);

        $this->removeTestUser('emailtest3');
    }

    public function test_verify_email_with_expired_hash_returns_false(): void
    {
        $userId = $this->createTestUser('emailtest4', 'emailtest4@test.com');
        $service = new \FlyCash\Services\EmailVerificationService($this->getPDO());
        $pdo = $this->getPDO();

        $hash = bin2hex(random_bytes(32));
        $pdo->prepare("INSERT INTO email_verify (accountId, email, hash, createAt, expiresAt) VALUES (:aid, :email, :hash, :ca, :ea)")
            ->execute([':aid' => $userId, ':email' => 'emailtest4@test.com', ':hash' => $hash, ':ca' => 0, ':ea' => 1]);

        $this->assertFalse($service->verifyEmail($hash));

        $pdo->prepare("DELETE FROM email_verify WHERE accountId = :aid")->execute([':aid' => $userId]);
        $this->removeTestUser('emailtest4');
    }
}
