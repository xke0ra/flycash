<?php

namespace Tests\Integration;

use Tests\TestCase;

class RedemptionsGetRequestsTest extends TestCase
{
    private int $userId;

    protected function setUp(): void
    {
        parent::setUp();
        try {
            $this->getPDO()->query("SELECT 1");
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database not available');
        }

        // Create a test user
        $this->userId = $this->createTestUser('redemption_filter_test', 'filter_test@test.com');

        // Insert 4 redemptions rows with different statuses
        $pdo = $this->getPDO();
        $time = time();
        $rows = [
            ['status' => 'pending',    'points_used' => 100],
            ['status' => 'processing', 'points_used' => 200],
            ['status' => 'completed',  'points_used' => 300],
            ['status' => 'rejected',   'points_used' => 400],
        ];

        $sql = "INSERT INTO redemptions (user_id, request_from, dev_name, dev_man, gift_name, req_amount, points_used, date, status, username)
                VALUES (:uid, :from, :dev, :man, :gift, :amount, :points, :time, :status, :user)";

        foreach ($rows as $row) {
            $pdo->prepare($sql)->execute([
                ':uid'    => $this->userId,
                ':from'   => 'test@example.com',
                ':dev'    => 'Test Device',
                ':man'    => 'Test Manufacturer',
                ':gift'   => 'Test Gift',
                ':amount' => 10.00,
                ':points' => $row['points_used'],
                ':time'   => $time,
                ':status' => $row['status'],
                ':user'   => 'redemption_filter_test',
            ]);
        }
    }

    protected function tearDown(): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("DELETE FROM redemptions WHERE user_id = :uid")->execute([':uid' => $this->userId]);
        $this->removeTestUser('redemption_filter_test');
        parent::tearDown();
    }

    public function test_getRequests_with_processing_filter_returns_only_processing(): void
    {
        $redemptions = new \redemptions($this->getPDO());
        $result = $redemptions->getRequests(0, 0, 0, 'processing');

        $this->assertFalse($result['error']);
        $this->assertCount(1, $result['requests']);
        $this->assertSame('processing', $result['requests'][0]['status']);
        $this->assertSame(200, (int)$result['requests'][0]['points_used']);
    }

    public function test_getRequests_with_rejected_filter_returns_only_rejected(): void
    {
        $redemptions = new \redemptions($this->getPDO());
        $result = $redemptions->getRequests(0, 0, 0, 'rejected');

        $this->assertFalse($result['error']);
        $this->assertCount(1, $result['requests']);
        $this->assertSame('rejected', $result['requests'][0]['status']);
        $this->assertSame(400, (int)$result['requests'][0]['points_used']);
    }

    public function test_getRequests_without_filter_returns_all_statuses(): void
    {
        $redemptions = new \redemptions($this->getPDO());
        $result = $redemptions->getRequests(0, 0, 0, null);

        $this->assertFalse($result['error']);
        // At minimum our 4 inserted rows are present
        $this->assertGreaterThanOrEqual(4, count($result['requests']));
        $statuses = array_column($result['requests'], 'status');
        $this->assertContains('pending', $statuses);
        $this->assertContains('processing', $statuses);
        $this->assertContains('completed', $statuses);
        $this->assertContains('rejected', $statuses);
    }

    public function test_getRequests_with_completed_filter_returns_only_completed(): void
    {
        $redemptions = new \redemptions($this->getPDO());
        $result = $redemptions->getRequests(0, 0, 0, 'completed');

        $this->assertFalse($result['error']);
        $this->assertCount(1, $result['requests']);
        $this->assertSame('completed', $result['requests'][0]['status']);
        $this->assertSame(300, (int)$result['requests'][0]['points_used']);
    }
}
