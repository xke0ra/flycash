<?php

namespace Tests\Unit\Services;

use Tests\TestCase;

class ProfileServiceTest extends TestCase
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

    public function test_get_returns_user_data(): void
    {
        $userId = $this->createTestUser('proftest1', 'proftest1@test.com');
        $service = new \FlyCash\Services\ProfileService($this->getPDO());

        $user = $service->get($userId);
        $this->assertIsArray($user);
        $this->assertSame('proftest1', $user['username']);
        $this->assertSame('proftest1@test.com', $user['email']);
        $this->assertSame(1000, (int)$user['points']);

        $this->removeTestUser('proftest1');
    }

    public function test_get_state_returns_integer(): void
    {
        $userId = $this->createTestUser('proftest2');
        $service = new \FlyCash\Services\ProfileService($this->getPDO());

        $state = $service->getState($userId);
        $this->assertIsInt($state);

        $this->removeTestUser('proftest2');
    }

    public function test_set_state(): void
    {
        $userId = $this->createTestUser('proftest3');
        $service = new \FlyCash\Services\ProfileService($this->getPDO());

        $this->assertTrue($service->setState($userId, 1));
        $this->assertSame(1, $service->getState($userId));

        $this->assertTrue($service->setState($userId, 0));
        $this->assertSame(0, $service->getState($userId));

        $this->removeTestUser('proftest3');
    }

    public function test_get_user_info_by_value(): void
    {
        $this->createTestUser('proftest4', 'proftest4@test.com');
        $service = new \FlyCash\Services\ProfileService($this->getPDO());

        $user = $service->getUserInfoByValue('login', 'proftest4');
        $this->assertIsArray($user);
        $this->assertSame('proftest4@test.com', $user['email']);

        $this->removeTestUser('proftest4');
    }

    public function test_get_user_data_by_username(): void
    {
        $this->createTestUser('proftest5');
        $service = new \FlyCash\Services\ProfileService($this->getPDO());

        $user = $service->getUserData('proftest5');
        $this->assertIsArray($user);

        $this->removeTestUser('proftest5');
    }
}
