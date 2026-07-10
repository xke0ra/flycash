<?php

namespace Tests\Integration\Postback;

use PHPUnit\Framework\TestCase;
use PDO;

class OgAdsTest extends TestCase
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

    protected function createTestUser(string $username): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("INSERT IGNORE INTO users (login, email, passw, points, regtime)
            VALUES (:l, :e, :pw, 1000, :t)")
            ->execute([':l' => $username, ':e' => $username . '@test.com', ':pw' => password_hash('test', PASSWORD_BCRYPT), ':t' => time()]);
    }

    protected function removeTestUser(string $username): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("DELETE FROM users WHERE login = :l")->execute([':l' => $username]);
    }

    protected function removeTrackerData(string $username): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("DELETE FROM tracker WHERE user_id = (SELECT id FROM users WHERE login = :l)")
            ->execute([':l' => $username]);
    }

    protected function addWhitelistIp(): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("INSERT IGNORE INTO whitelists (name, ip_addr) VALUES ('test', '127.0.0.1')")->execute();
    }

    protected function removeWhitelistIp(): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare("DELETE FROM whitelists WHERE ip_addr = '127.0.0.1'")->execute();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->addWhitelistIp();
        $this->createTestUser('ogadstest');
    }

    protected function tearDown(): void
    {
        $this->removeTrackerData('ogadstest');
        $this->removeTestUser('ogadstest');
        $this->removeWhitelistIp();
        parent::tearDown();
    }

    public function test_parse_request_extracts_params_and_converts_usd(): void
    {
        $handler = new \FlyCash\Postback\Handlers\OgAdsHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $parseRequest = $ref->getMethod('parseRequest');

        $_GET['user'] = 'ogadstest';
        $_GET['amount'] = '2.50';

        $parseRequest->invoke($handler);

        $this->assertSame('ogadstest', $ref->getProperty('userId')->getValue($handler));
        $this->assertSame(750, $ref->getProperty('amount')->getValue($handler)); // 2.50 * 300
        $this->assertSame('', $ref->getProperty('transactionId')->getValue($handler));
    }

    public function test_parse_request_with_zero_usd(): void
    {
        $handler = new \FlyCash\Postback\Handlers\OgAdsHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $parseRequest = $ref->getMethod('parseRequest');

        $_GET['user'] = 'ogadstest';
        $_GET['amount'] = '0';

        $parseRequest->invoke($handler);

        $this->assertSame(0, $ref->getProperty('amount')->getValue($handler));
    }

    public function test_should_process_returns_true_for_valid_amount(): void
    {
        $handler = new \FlyCash\Postback\Handlers\OgAdsHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $shouldProcess = $ref->getMethod('shouldProcess');

        $ref->getProperty('amount')->setValue($handler, 300);
        $ref->getProperty('userId')->setValue($handler, 'ogadstest');
        $this->assertTrue($shouldProcess->invoke($handler));
    }

    public function test_should_process_returns_false_for_zero_amount(): void
    {
        $handler = new \FlyCash\Postback\Handlers\OgAdsHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $shouldProcess = $ref->getMethod('shouldProcess');

        $ref->getProperty('amount')->setValue($handler, 0);
        $ref->getProperty('userId')->setValue($handler, 'ogadstest');
        $this->assertFalse($shouldProcess->invoke($handler));
    }

    public function test_should_process_returns_false_for_empty_user(): void
    {
        $handler = new \FlyCash\Postback\Handlers\OgAdsHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $shouldProcess = $ref->getMethod('shouldProcess');

        $ref->getProperty('amount')->setValue($handler, 300);
        $ref->getProperty('userId')->setValue($handler, '');
        $this->assertFalse($shouldProcess->invoke($handler));
    }

    public function test_validate_user_rejects_nonexistent_user(): void
    {
        $handler = new \FlyCash\Postback\Handlers\OgAdsHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $validateUser = $ref->getMethod('validateUser');

        $ref->getProperty('userId')->setValue($handler, 'nobody');
        $this->assertFalse($validateUser->invoke($handler));
    }

    public function test_validate_user_accepts_valid_user(): void
    {
        $handler = new \FlyCash\Postback\Handlers\OgAdsHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $parseRequest = $ref->getMethod('parseRequest');
        $validateUser = $ref->getMethod('validateUser');

        $_GET['user'] = 'ogadstest';
        $_GET['amount'] = '1.00';

        $parseRequest->invoke($handler);
        $this->assertTrue($validateUser->invoke($handler));
    }

    public function test_verify_signature_returns_true_when_whitelisted(): void
    {
        $handler = new \FlyCash\Postback\Handlers\OgAdsHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $verifySignature = $ref->getMethod('verifySignature');

        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $this->assertTrue($verifySignature->invoke($handler));
    }
}
