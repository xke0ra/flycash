<?php

namespace Tests\Feature;

use Tests\TestCase;

class PostbackFlowTest extends TestCase
{
    private string $testUser = '';
    private string $testTxId = '';
    private array $savedGet = [];
    private string $savedRemoteAddr = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->testUser = 'pb_' . bin2hex(random_bytes(4));
        $this->testTxId = 'tx_' . bin2hex(random_bytes(8));
        $this->savedGet = $_GET;
        $this->savedRemoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';

        try {
            $this->getPDO()->query("SELECT 1");
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database not available');
        }

        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $this->ensureWhitelisted('127.0.0.1');
    }

    protected function tearDown(): void
    {
        $_GET = $this->savedGet;
        $_SERVER['REMOTE_ADDR'] = $this->savedRemoteAddr;

        $pdo = $this->getPDO();
        $pdo->prepare("DELETE FROM users WHERE login LIKE 'pb\\_%'")->execute();
        $pdo->prepare("DELETE FROM postback_log WHERE transaction_id LIKE 'tx\\_%'")->execute();
        $pdo->prepare("DELETE FROM tracker WHERE username LIKE 'pb\\_%'")->execute();
        parent::tearDown();
    }

    private function ensureWhitelisted(string $ip): void
    {
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare("SELECT id FROM whitelists WHERE ip_addr = :ip LIMIT 1");
        $stmt->execute([':ip' => $ip]);
        if (!$stmt->fetch()) {
            $pdo->prepare("INSERT INTO whitelists (name, ip_addr) VALUES ('test', :ip)")
                ->execute([':ip' => $ip]);
        }
    }

    private function callProtected(object $obj, string $method, array $args = []): mixed
    {
        $ref = new \ReflectionMethod($obj, $method);
        $ref->setAccessible(true);
        return $ref->invoke($obj, ...$args);
    }

    private function getProtectedProp(object $obj, string $prop): mixed
    {
        $ref = new \ReflectionProperty($obj, $prop);
        $ref->setAccessible(true);
        return $ref->getValue($obj);
    }

    public function test_full_postback_flow_credits_user_and_logs(): void
    {
        $pdo = $this->getPDO();
        $this->createTestUser($this->testUser, $this->testUser . '@test.com');

        $beforePoints = $this->getUserPoints($this->testUser);

        $_GET = [
            'user_id' => $this->testUser,
            'point_value' => '100',
            'tx_id' => $this->testTxId,
        ];

        $handler = new \FlyCash\Postback\Handlers\AdGateMediaHandler($pdo);
        $this->callProtected($handler, 'parseRequest');

        $this->assertTrue($this->callProtected($handler, 'verifySignature'));
        $this->assertTrue($this->callProtected($handler, 'validateUser'));
        $this->assertTrue($this->callProtected($handler, 'creditUser'));

        $afterPoints = $this->getUserPoints($this->testUser);
        $this->assertSame($beforePoints + 100, $afterPoints, 'User points should increase by 100');

        $this->callProtected($handler, 'logPostback');

        $stmt = $pdo->prepare("SELECT * FROM postback_log WHERE transaction_id = :tx LIMIT 1");
        $stmt->execute([':tx' => $this->testTxId]);
        $log = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertNotFalse($log, 'postback_log entry should exist');
        $this->assertSame('AdGateMedia', $log['provider']);
        $this->assertSame('100', (string)$log['amount']);
    }

    public function test_duplicate_transaction_is_rejected(): void
    {
        $pdo = $this->getPDO();
        $this->createTestUser($this->testUser, $this->testUser . '@test.com');

        $pdo->prepare("INSERT INTO postback_log (provider, transaction_id, user_id, amount, status, ip_addr, created_at)
            VALUES ('AdGateMedia', :tx, 'test', 100, 'success', '127.0.0.1', NOW())")
            ->execute([':tx' => $this->testTxId]);

        $_GET = [
            'user_id' => $this->testUser,
            'point_value' => '100',
            'tx_id' => $this->testTxId,
        ];

        $handler = new \FlyCash\Postback\Handlers\AdGateMediaHandler($pdo);
        $this->callProtected($handler, 'parseRequest');

        $this->assertTrue($this->callProtected($handler, 'hasTransactionId'));
        $this->assertTrue($this->callProtected($handler, 'isDuplicate'),
            'isDuplicate should return true for existing transaction_id');

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM postback_log WHERE transaction_id = :tx");
        $stmt->execute([':tx' => $this->testTxId]);
        $this->assertSame(1, (int)$stmt->fetchColumn(), 'No additional postback_log entry should be created');
    }

    public function test_invalid_user_is_rejected(): void
    {
        $pdo = $this->getPDO();

        $_GET = [
            'user_id' => 'nonexistent_' . bin2hex(random_bytes(4)),
            'point_value' => '100',
            'tx_id' => $this->testTxId,
        ];

        $handler = new \FlyCash\Postback\Handlers\AdGateMediaHandler($pdo);
        $this->callProtected($handler, 'parseRequest');

        $this->assertFalse($this->callProtected($handler, 'validateUser'),
            'validateUser should fail for non-existent user');
    }

    public function test_missing_transaction_id_still_processes(): void
    {
        $pdo = $this->getPDO();
        $this->createTestUser($this->testUser, $this->testUser . '@test.com');

        $_GET = [
            'user_id' => $this->testUser,
            'point_value' => '50',
        ];

        $handler = new \FlyCash\Postback\Handlers\AdGateMediaHandler($pdo);
        $this->callProtected($handler, 'parseRequest');
        $this->assertFalse($this->callProtected($handler, 'hasTransactionId'));
        $this->assertTrue($this->callProtected($handler, 'validateUser'));
        $this->assertTrue($this->callProtected($handler, 'creditUser'));
    }

    private function getUserPoints(string $username): int
    {
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare("SELECT points FROM users WHERE login = :l LIMIT 1");
        $stmt->execute([':l' => $username]);
        $val = $stmt->fetchColumn();
        return $val !== false ? (int)$val : 0;
    }
}
