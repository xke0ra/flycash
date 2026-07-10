<?php

namespace Tests\Unit\Services;

use Tests\TestCase;

class ConfigServiceTest extends TestCase
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

    public function test_get_returns_config_value(): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("INSERT IGNORE INTO configuration (config_name, config_value) VALUES (:n, :v)")
            ->execute([':n' => 'TEST_CONFIG_KEY', ':v' => 'test_value_123']);

        $service = new \FlyCash\Services\ConfigService($pdo);
        $this->assertSame('test_value_123', $service->get('TEST_CONFIG_KEY'));

        $pdo->prepare("DELETE FROM configuration WHERE config_name = :n")
            ->execute([':n' => 'TEST_CONFIG_KEY']);
    }

    public function test_get_returns_default_when_missing(): void
    {
        $service = new \FlyCash\Services\ConfigService($this->getPDO());
        $this->assertSame('default', $service->get('NONEXISTENT_KEY', 'default'));
    }

    public function test_get_int_returns_integer(): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("INSERT IGNORE INTO configuration (config_name, config_value) VALUES (:n, :v)")
            ->execute([':n' => 'TEST_INT_KEY', ':v' => '42']);

        $service = new \FlyCash\Services\ConfigService($pdo);
        $this->assertSame(42, $service->getInt('TEST_INT_KEY'));
        $this->assertSame(99, $service->getInt('NONEXISTENT_INT', 99));

        $pdo->prepare("DELETE FROM configuration WHERE config_name = :n")
            ->execute([':n' => 'TEST_INT_KEY']);
    }

    public function test_get_bool_returns_boolean(): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("INSERT IGNORE INTO configuration (config_name, config_value) VALUES (:n, :v)")
            ->execute([':n' => 'TEST_BOOL_KEY', ':v' => '1']);

        $service = new \FlyCash\Services\ConfigService($pdo);
        $this->assertTrue($service->getBool('TEST_BOOL_KEY'));

        $pdo->prepare("DELETE FROM configuration WHERE config_name = :n")
            ->execute([':n' => 'TEST_BOOL_KEY']);
    }

    public function test_get_all_returns_array(): void
    {
        $service = new \FlyCash\Services\ConfigService($this->getPDO());
        $all = $service->getAll();
        $this->assertIsArray($all);
    }
}
