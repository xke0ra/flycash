<?php

namespace Tests\Feature;

use Tests\TestCase;

class RedeemFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        try {
            $this->getPDO()->query("SELECT 1");
            $this->createTestUser('redeemtest', 'redeemtest@test.com');
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database not available');
        }
    }

    protected function tearDown(): void
    {
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :u");
        $stmt->execute([':u' => 'redeemtest']);
        $userId = (int)$stmt->fetchColumn();
        if ($userId > 0) {
            $pdo->prepare("DELETE FROM redemptions WHERE user_id = :uid")->execute([':uid' => $userId]);
        }
        $this->removeTestUser('redeemtest');
        parent::tearDown();
    }

    public function test_redeem_deducts_points_and_creates_request(): void
    {
        $pdo = $this->getPDO();
        $pointsService = new \FlyCash\Services\PointsService($pdo);

        // Credit some points first
        $credited = $pointsService->creditUserPoints('redeemtest', 500, 'Redeem Test', 'Test credit');
        $this->assertTrue($credited);

        // Verify points added
        $stmt = $pdo->prepare("SELECT points FROM users WHERE login = :u");
        $stmt->execute([':u' => 'redeemtest']);
        $this->assertSame(1500, (int)$stmt->fetchColumn());

        // Deduct points (simulating redeem)
        $deducted = $pointsService->creditUserPoints('redeemtest', -300, 'Redeem', 'Redeemed 300 points', true, false);
        $this->assertTrue($deducted);

        // Verify points deducted
        $stmt->execute();
        $this->assertSame(1200, (int)$stmt->fetchColumn());

        // Create a Request record (simulating the redeem request flow)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :u");
        $stmt->execute([':u' => 'redeemtest']);
        $userId = (int)$stmt->fetchColumn();

        $sql = "INSERT INTO redemptions (user_id, request_from, dev_name, dev_man, gift_name, req_amount, points_used, date, status, username)
                VALUES (:uid, :to, :dev, :man, :gift, :amount, :points, :time, 'pending', :user)";
        $stmt = $pdo->prepare($sql);
        $inserted = $stmt->execute([
            ':uid' => $userId,
            ':to' => 'redeemtest@test.com',
            ':dev' => 'Test Device',
            ':man' => 'Test Manufacturer',
            ':gift' => 'Test Gift',
            ':amount' => 5.00,
            ':points' => 300,
            ':time' => time(),
            ':user' => 'redeemtest',
        ]);
        $this->assertTrue($inserted);

        // Verify request exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM redemptions WHERE user_id = :uid AND status = 'pending'");
        $stmt->execute([':uid' => $userId]);
        $this->assertSame(1, (int)$stmt->fetchColumn());
    }

    public function test_redeem_atomic_transaction_prevents_double_spend(): void
    {
        $pdo = $this->getPDO();
        $userId = $this->createTestUser('redeem_atomic_' . bin2hex(random_bytes(4)), 'redeem_atomic@test.com');
        $pointsRequired = 300;

        // Simulate the exact atomic pattern from the redeem endpoint
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT points FROM users WHERE id = :id LIMIT 1 FOR UPDATE");
        $stmt->execute([':id' => $userId]);
        $userRow = $stmt->fetch();

        $hasBalance = $userRow && (int)$userRow['points'] >= $pointsRequired;
        $this->assertTrue($hasBalance, 'User should have enough balance');

        $st = $pdo->prepare("UPDATE users SET points = points - :delta WHERE id = :id AND points - :delta >= 0");
        $st->execute([':delta' => $pointsRequired, ':id' => $userId]);
        $this->assertSame(1, $st->rowCount(), 'Points should be deducted atomically');

        $time = time();
        $trackerSt = $pdo->prepare("INSERT INTO tracker (user_id, username, points, type, date) VALUES (:uid, :uname, :points, :type, :time)");
        $trackerSt->execute([':uid' => $userId, ':uname' => 'redeem_atomic', ':points' => -$pointsRequired, ':type' => 'Redeem', ':time' => time()]);

        $reqSt = $pdo->prepare("INSERT INTO redemptions (user_id, request_from, dev_name, dev_man, gift_name, req_amount, points_used, date, status, username)
            VALUES (:uid, :to, :dev, :man, :gift, :amount, :points, :time, 'pending', :uname)");
        $reqSt->execute([':uid' => $userId, ':to' => 'test@example.com', ':dev' => 'Test', ':man' => 'Test', ':gift' => 'Gift', ':amount' => 10.00, ':points' => $pointsRequired, ':time' => $time, ':uname' => 'redeem_atomic']);

        $pdo->commit();

        // Verify balance deducted
        $stmt = $pdo->prepare("SELECT points FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $this->assertSame(700, (int)$stmt->fetchColumn());

        // Verify redemption created
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM redemptions WHERE user_id = :uid AND status = 'pending'");
        $stmt->execute([':uid' => $userId]);
        $this->assertSame(1, (int)$stmt->fetchColumn());

        // Second attempt with a larger amount should fail (not enough balance)
        $pdo->beginTransaction();
        $stmt2 = $pdo->prepare("SELECT points FROM users WHERE id = :id LIMIT 1 FOR UPDATE");
        $stmt2->execute([':id' => $userId]);
        $userRow2 = $stmt2->fetch();
        $remainingPoints = (int)$userRow2['points'];
        $this->assertSame(700, $remainingPoints);
        // Attempt to deduct more than remaining should fail
        $st2 = $pdo->prepare("UPDATE users SET points = points - :delta WHERE id = :id AND points - :delta >= 0");
        $st2->execute([':delta' => 800, ':id' => $userId]);
        $this->assertSame(0, $st2->rowCount(), 'Overdraft should affect zero rows');
        $pdo->rollBack();

        // Cleanup
        $pdo->prepare("DELETE FROM redemptions WHERE user_id = :uid")->execute([':uid' => $userId]);
        $pdo->prepare("DELETE FROM tracker WHERE user_id = :uid")->execute([':uid' => $userId]);
        $pdo->prepare("DELETE FROM users WHERE id = :id")->execute([':id' => $userId]);
    }

    public function test_redeem_insufficient_points_fails(): void
    {
        $pdo = $this->getPDO();
        $pointsService = new \FlyCash\Services\PointsService($pdo);

        // User has 1000 points (from setUp), try to deduct more
        $result = $pointsService->creditUserPoints('redeemtest', -2000, 'Redeem', 'Attempt overdraft', true, false);
        $this->assertFalse($result);

        // Verify balance is unchanged (atomic update prevents negative)
        $stmt = $pdo->prepare("SELECT points FROM users WHERE login = :u");
        $stmt->execute([':u' => 'redeemtest']);
        $this->assertSame(1000, (int)$stmt->fetchColumn());
    }
}
