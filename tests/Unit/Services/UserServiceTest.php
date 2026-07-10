<?php

namespace Tests\Unit\Services;

use Tests\TestCase;

class UserServiceTest extends TestCase
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

    public function test_get_user_info_by_id(): void
    {
        $userId = $this->createTestUser('usertest1', 'usertest1@test.com');
        $service = new \FlyCash\Services\UserService($this->getPDO());
        $user = $service->getUserInfo($userId);

        $this->assertIsArray($user);
        $this->assertSame('usertest1', $user['login']);
        $this->assertSame('usertest1@test.com', $user['email']);

        $this->removeTestUser('usertest1');
    }

    public function test_get_user_info_by_value(): void
    {
        $this->createTestUser('usertest2', 'usertest2@test.com');
        $service = new \FlyCash\Services\UserService($this->getPDO());

        $user = $service->getUserInfoByValue('login', 'usertest2');
        $this->assertIsArray($user);
        $this->assertSame('usertest2@test.com', $user['email']);

        $user = $service->getUserInfoByValue('email', 'usertest2@test.com');
        $this->assertIsArray($user);
        $this->assertSame('usertest2', $user['login']);

        $this->removeTestUser('usertest2');
    }

    public function test_get_user_info_by_value_rejects_invalid_field(): void
    {
        $service = new \FlyCash\Services\UserService($this->getPDO());
        $result = $service->getUserInfoByValue('invalid_field', 'value');
        $this->assertNull($result);
    }

    public function test_update_user_access(): void
    {
        $userId = $this->createTestUser('usertest3');
        $service = new \FlyCash\Services\UserService($this->getPDO());

        $result = $service->updateUserAccess($userId);
        $this->assertTrue($result);

        $this->removeTestUser('usertest3');
    }
}
