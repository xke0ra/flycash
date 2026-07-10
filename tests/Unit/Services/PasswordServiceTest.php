<?php

namespace Tests\Unit\Services;

use Tests\TestCase;

class PasswordServiceTest extends TestCase
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

    public function test_new_password_returns_bcrypt_hash(): void
    {
        $service = new \FlyCash\Services\PasswordService($this->getPDO());
        $hash = $service->newPassword('test_password');

        $this->assertStringStartsWith('$2y$', $hash);
        $this->assertTrue(password_verify('test_password', $hash));
    }

    public function test_change_password_updates_hash(): void
    {
        $userId = $this->createTestUser('pwtest1');
        $service = new \FlyCash\Services\PasswordService($this->getPDO());

        $result = $service->changePassword($userId, 'new_password');
        $this->assertTrue($result);

        $stmt = $this->getPDO()->prepare("SELECT passw FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $hash = $stmt->fetchColumn();

        $this->assertStringStartsWith('$2y$', $hash);
        $this->assertTrue(password_verify('new_password', $hash));

        $this->removeTestUser('pwtest1');
    }
}
