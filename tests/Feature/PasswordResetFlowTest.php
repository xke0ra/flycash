<?php

namespace Tests\Feature;

use Tests\TestCase;

class PasswordResetFlowTest extends TestCase
{
    private string $testUser = '';
    private int $userId = 0;
    private string $email = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->testUser = 'reset_' . bin2hex(random_bytes(4));
        $this->email = $this->testUser . '@test.com';
        try {
            $pdo = $this->getPDO();
            $pdo->query("SELECT 1");
            $this->userId = $this->createTestUser($this->testUser, $this->email);
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database not available');
        }
    }

    protected function tearDown(): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("DELETE FROM restore_data WHERE accountId = :id")->execute([':id' => $this->userId]);
        $pdo->prepare("DELETE FROM users WHERE id = :id")->execute([':id' => $this->userId]);
        $pdo->prepare("DELETE FROM tracker WHERE username = :u")->execute([':u' => $this->testUser]);
        parent::tearDown();
    }

    public function test_restore_point_created_successfully(): void
    {
        $pdo = $this->getPDO();
        $account = new \account($pdo, $this->userId);

        $result = $account->restorePointCreate($this->email, 0);

        $this->assertIsArray($result);
        $this->assertFalse($result['error']);
        $this->assertArrayHasKey('hash', $result);
        $this->assertNotEmpty($result['hash']);
        $this->assertSame(64, strlen($result['hash']));
    }

    public function test_restore_point_reuses_existing_active_point(): void
    {
        $pdo = $this->getPDO();
        $account = new \account($pdo, $this->userId);

        $first = $account->restorePointCreate($this->email, 0);
        $second = $account->restorePointCreate($this->email, 0);

        $this->assertFalse($second['error']);
        $this->assertSame($first['hash'], $second['hash'], 'Should return same hash for active restore point');
    }

    public function test_restore_point_is_removed_after_use(): void
    {
        $pdo = $this->getPDO();
        $account = new \account($pdo, $this->userId);

        $createResult = $account->restorePointCreate($this->email, 0);
        $this->assertFalse($createResult['error']);

        $account->restorePointRemove();

        $infoResult = $account->restorePointInfo();
        $this->assertTrue($infoResult['error'], 'After removal, restore point should not be found');
    }
}
