<?php

namespace Tests\Unit\Services;

use Tests\TestCase;

class OfferwallServiceTest extends TestCase
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

    public function test_get_offerwalls_returns_array(): void
    {
        $service = new \FlyCash\Services\OfferwallService($this->getPDO());
        $result = $service->getOfferwalls();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('offerwalls', $result);
        $this->assertArrayHasKey('error', $result);
    }

    public function test_is_whitelisted(): void
    {
        $this->addWhitelistIp('10.0.0.1');
        $service = new \FlyCash\Services\OfferwallService($this->getPDO());

        $this->assertTrue($service->isWhitelisted('10.0.0.1'));
        $this->assertFalse($service->isWhitelisted('10.0.0.2'));

        $this->removeWhitelistIp('10.0.0.1');
    }
}
