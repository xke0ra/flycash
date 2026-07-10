<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminActionTest extends TestCase
{
    private string $testUser = '';
    private int $userId = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testUser = 'adminact_' . bin2hex(random_bytes(4));
        try {
            $pdo = $this->getPDO();
            $pdo->query("SELECT 1");
            $this->userId = $this->createTestUser($this->testUser, $this->testUser . '@test.com');
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database not available');
        }
    }

    protected function tearDown(): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("DELETE FROM users WHERE id = :id")->execute([':id' => $this->userId]);
        $pdo->prepare("DELETE FROM tracker WHERE username = :u")->execute([':u' => $this->testUser]);
        parent::tearDown();
    }

    // ─── setState (block/unblock) ───────────────────────────

    public function test_set_state_blocks_user(): void
    {
        $pdo = $this->getPDO();
        $account = new \account($pdo, $this->userId);

        $account->setState(2);

        $stmt = $pdo->prepare("SELECT state FROM users WHERE id = :id");
        $stmt->execute([':id' => $this->userId]);
        $this->assertSame(2, (int)$stmt->fetchColumn());
    }

    public function test_set_state_unblocks_user(): void
    {
        $pdo = $this->getPDO();
        $account = new \account($pdo, $this->userId);

        $account->setState(2);
        $account2 = new \account($pdo, $this->userId);
        $account2->setState(0);

        $stmt = $pdo->prepare("SELECT state FROM users WHERE id = :id");
        $stmt->execute([':id' => $this->userId]);
        $this->assertSame(0, (int)$stmt->fetchColumn());
    }

    public function test_get_state_returns_correct_value(): void
    {
        $pdo = $this->getPDO();
        $account = new \account($pdo, $this->userId);

        $state = $account->getState();
        $this->assertSame(0, $state);

        $account->setState(2);
        $state = $account->getState();
        $this->assertSame(2, $state);
    }

    // ─── Manual points adjustment ───────────────────────────

    public function test_credit_user_points_increases_balance(): void
    {
        $pdo = $this->getPDO();
        $service = new \FlyCash\Services\PointsService($pdo);

        $result = $service->creditUserPointsByUserId($this->userId, 250, 'AdminAdjust', 'Manual credit', false, false);
        $this->assertTrue($result);

        $stmt = $pdo->prepare("SELECT points FROM users WHERE id = :id");
        $stmt->execute([':id' => $this->userId]);
        $this->assertSame(1250, (int)$stmt->fetchColumn());
    }

    public function test_credit_user_points_by_user_id_rejects_overdraft(): void
    {
        $pdo = $this->getPDO();

        // Set balance to 100
        $pdo->prepare("UPDATE users SET points = 100 WHERE id = :id")->execute([':id' => $this->userId]);

        $service = new \FlyCash\Services\PointsService($pdo);
        $result = $service->creditUserPointsByUserId($this->userId, -500, 'AdminAdjust', 'Attempt overdraft', false, false);

        $this->assertFalse($result);

        $stmt = $pdo->prepare("SELECT points FROM users WHERE id = :id");
        $stmt->execute([':id' => $this->userId]);
        $this->assertSame(100, (int)$stmt->fetchColumn(), 'Balance should remain unchanged on overdraft');
    }
}
