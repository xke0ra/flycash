<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PDO;

class Phase0RegressionTest extends TestCase
{
    private ?PDO $pdo = null;

    protected function getPDO(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER, DB_PASS,
                [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return $this->pdo;
    }

    protected function setUp(): void
    {
        parent::setUp();
        try {
            $this->getPDO()->query("SELECT 1");
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database not available');
        }
    }

    protected function tearDown(): void
    {
        unset($this->pdo);
        parent::tearDown();
    }

    // ─── Phase 0.1: gcm_regid crash ───────────────────────────────

    public function test_credit_user_points_with_null_gcm_regid_does_not_crash(): void
    {
        $username = 'gcmnull_' . bin2hex(random_bytes(4));
        $pdo = $this->getPDO();

        $pdo->prepare("INSERT INTO users (login, email, passw, points, regtime, gcm_regid)
            VALUES (:l, :e, :pw, 500, :t, NULL)")
            ->execute([':l' => $username, ':e' => $username . '@test.com', ':pw' => password_hash('test', PASSWORD_BCRYPT), ':t' => time()]);

        $service = new \FlyCash\Services\PointsService($pdo);
        $result = $service->creditUserPoints($username, 100, 'RegressionTest', 'gcm_regid null', true, true);

        $this->assertTrue($result);

        $pdo->prepare("DELETE FROM tracker WHERE username = :u")->execute([':u' => $username]);
        $pdo->prepare("DELETE FROM users WHERE login = :l")->execute([':l' => $username]);
    }

    public function test_credit_user_points_with_empty_gcm_regid_does_not_crash(): void
    {
        $username = 'gcmempty_' . bin2hex(random_bytes(4));
        $pdo = $this->getPDO();

        $pdo->prepare("INSERT INTO users (login, email, passw, points, regtime, gcm_regid)
            VALUES (:l, :e, :pw, 500, :t, '')")
            ->execute([':l' => $username, ':e' => $username . '@test.com', ':pw' => password_hash('test', PASSWORD_BCRYPT), ':t' => time()]);

        $service = new \FlyCash\Services\PointsService($pdo);
        $result = $service->creditUserPoints($username, 100, 'RegressionTest', 'gcm_regid empty', true, true);

        $this->assertTrue($result);

        $pdo->prepare("DELETE FROM tracker WHERE username = :u")->execute([':u' => $username]);
        $pdo->prepare("DELETE FROM users WHERE login = :l")->execute([':l' => $username]);
    }

    // ─── Phase 0.2: Postback signature verification ──────────────

    public function test_adgatemedia_handler_rejects_invalid_signature(): void
    {
        $pdo = $this->getPDO();

        // Ensure a secret is configured (so handler uses HMAC, not IP whitelist)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM configuration WHERE config_name = 'ADGATEMEDIA_SECRET'");
        $stmt->execute();
        $hasConfig = (int)$stmt->fetchColumn() > 0;
        $origValue = '';
        if ($hasConfig) {
            $stmt = $pdo->prepare("SELECT config_value FROM configuration WHERE config_name = 'ADGATEMEDIA_SECRET'");
            $stmt->execute();
            $origValue = $stmt->fetchColumn() ?: '';
        }

        try {
            if ($hasConfig) {
                $pdo->prepare("UPDATE configuration SET config_value = 'regression_test_secret' WHERE config_name = 'ADGATEMEDIA_SECRET'")->execute();
            } else {
                $pdo->prepare("INSERT INTO configuration (config_name, config_value) VALUES ('ADGATEMEDIA_SECRET', 'regression_test_secret')")->execute();
            }

            $_GET = [
                'user_id' => 'regression_user',
                'point_value' => '100',
                'tx_id' => 'regression_tx',
                'signature' => 'this_is_a_bad_signature',
            ];

            $handler = new \FlyCash\Postback\Handlers\AdGateMediaHandler($pdo);

            $reflection = new \ReflectionClass($handler);

            $parseMethod = $reflection->getMethod('parseRequest');
            $parseMethod->setAccessible(true);
            $parseMethod->invoke($handler);

            $verifyMethod = $reflection->getMethod('verifySignature');
            $verifyMethod->setAccessible(true);

            $this->assertFalse($verifyMethod->invoke($handler));

        } finally {
            // Restore original config
            if ($hasConfig) {
                $pdo->prepare("UPDATE configuration SET config_value = :v WHERE config_name = 'ADGATEMEDIA_SECRET'")->execute([':v' => $origValue]);
            } else {
                $pdo->prepare("DELETE FROM configuration WHERE config_name = 'ADGATEMEDIA_SECRET'")->execute();
            }
        }
    }

    public function test_adgatemedia_handler_accepts_valid_signature(): void
    {
        $pdo = $this->getPDO();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM configuration WHERE config_name = 'ADGATEMEDIA_SECRET'");
        $stmt->execute();
        $hasConfig = (int)$stmt->fetchColumn() > 0;
        $origValue = '';
        if ($hasConfig) {
            $stmt = $pdo->prepare("SELECT config_value FROM configuration WHERE config_name = 'ADGATEMEDIA_SECRET'");
            $stmt->execute();
            $origValue = $stmt->fetchColumn() ?: '';
        }

        try {
            $secret = 'regression_test_secret_2';
            if ($hasConfig) {
                $pdo->prepare("UPDATE configuration SET config_value = :v WHERE config_name = 'ADGATEMEDIA_SECRET'")->execute([':v' => $secret]);
            } else {
                $pdo->prepare("INSERT INTO configuration (config_name, config_value) VALUES ('ADGATEMEDIA_SECRET', :v)")->execute([':v' => $secret]);
            }

            $userId = 'regression_user2';
            $amount = 150;
            $txId = 'regression_tx2';
            $expectedSig = hash_hmac('sha256', $userId . $amount . $secret, $secret);

            $_GET = [
                'user_id' => $userId,
                'point_value' => (string)$amount,
                'tx_id' => $txId,
                'signature' => $expectedSig,
            ];

            $handler = new \FlyCash\Postback\Handlers\AdGateMediaHandler($pdo);

            $reflection = new \ReflectionClass($handler);
            $parseMethod = $reflection->getMethod('parseRequest');
            $parseMethod->setAccessible(true);
            $parseMethod->invoke($handler);

            $verifyMethod = $reflection->getMethod('verifySignature');
            $verifyMethod->setAccessible(true);

            $this->assertTrue($verifyMethod->invoke($handler));

        } finally {
            if ($hasConfig) {
                $pdo->prepare("UPDATE configuration SET config_value = :v WHERE config_name = 'ADGATEMEDIA_SECRET'")->execute([':v' => $origValue]);
            } else {
                $pdo->prepare("DELETE FROM configuration WHERE config_name = 'ADGATEMEDIA_SECRET'")->execute();
            }
        }
    }

    // ─── Phase 0.3: Duplicate transaction_id rejection ──────────

    public function test_isDuplicate_returns_true_for_existing_transaction(): void
    {
        $pdo = $this->getPDO();
        $txId = 'dupreg_' . bin2hex(random_bytes(8));

        $pdo->prepare("INSERT IGNORE INTO postback_log (provider, transaction_id, user_id, amount, status, ip_addr, created_at)
            VALUES ('RegressionTest', :tx, 'testuser', 100, 'success', '127.0.0.1', NOW())")
            ->execute([':tx' => $txId]);

        $handler = new class ($pdo) extends \FlyCash\Postback\Handler {
            protected string $providerName = 'RegressionTest';
            protected function parseRequest(): void {}
            protected function validateUser(): bool { return true; }
            protected function creditUser(): bool { return true; }
            protected function verifySignature(): bool { return true; }
        };

        $reflection = new \ReflectionClass($handler);
        $txProp = $reflection->getProperty('transactionId');
        $txProp->setAccessible(true);
        $txProp->setValue($handler, $txId);

        $method = $reflection->getMethod('isDuplicate');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($handler));

        $pdo->prepare("DELETE FROM postback_log WHERE transaction_id = :tx")->execute([':tx' => $txId]);
    }

    public function test_isDuplicate_returns_false_for_new_transaction(): void
    {
        $pdo = $this->getPDO();
        $txId = 'newtx_' . bin2hex(random_bytes(8));

        $handler = new class ($pdo) extends \FlyCash\Postback\Handler {
            protected string $providerName = 'RegressionTest';
            protected function parseRequest(): void {}
            protected function validateUser(): bool { return true; }
            protected function creditUser(): bool { return true; }
            protected function verifySignature(): bool { return true; }
        };

        $reflection = new \ReflectionClass($handler);
        $txProp = $reflection->getProperty('transactionId');
        $txProp->setAccessible(true);
        $txProp->setValue($handler, $txId);

        $method = $reflection->getMethod('isDuplicate');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($handler));
    }

    // ─── Phase 0.4: OgAdsHandler — no transaction ID, relies on IP + cap ──

    public function test_ogads_handler_parses_request_and_sets_empty_transaction_id(): void
    {
        $pdo = $this->getPDO();

        $_GET = [
            'status' => '1',
            'user' => 'ogadstest_user',
            'of_id' => 'offer_123',
            'amount' => '100',
        ];

        $handler = new \FlyCash\Postback\Handlers\OgAdsHandler($pdo);

        $reflection = new \ReflectionClass($handler);
        $parseMethod = $reflection->getMethod('parseRequest');
        $parseMethod->setAccessible(true);
        $parseMethod->invoke($handler);

        $txProp = $reflection->getProperty('transactionId');
        $txProp->setAccessible(true);
        $this->assertSame('', $txProp->getValue($handler), 'OgAds intentionally leaves transactionId empty');

        $userIdProp = $reflection->getProperty('userId');
        $userIdProp->setAccessible(true);
        $this->assertSame('ogadstest_user', $userIdProp->getValue($handler));

        $amountProp = $reflection->getProperty('amount');
        $amountProp->setAccessible(true);
        $this->assertSame(30000, $amountProp->getValue($handler));
    }
}
