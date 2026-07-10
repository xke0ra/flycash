<?php

namespace Tests\Unit\Services;

use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
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

    public function test_get_today_active_users_returns_int(): void
    {
        $service = new \FlyCash\Services\AnalyticsService($this->getPDO());
        $result = $service->getTodayActiveUsers();
        $this->assertIsInt($result);
    }

    public function test_get_new_users_returns_int(): void
    {
        $service = new \FlyCash\Services\AnalyticsService($this->getPDO());
        $result = $service->getNewUsers();
        $this->assertIsInt($result);
    }

    public function test_get_today_sessions_returns_int(): void
    {
        $service = new \FlyCash\Services\AnalyticsService($this->getPDO());
        $result = $service->getTodaySessions();
        $this->assertIsInt($result);
    }
}
