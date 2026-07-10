<?php

namespace Tests\Integration\Postback;

use PHPUnit\Framework\TestCase;
use PDO;

class AdMantumTest extends TestCase
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
        $this->createTestUser('admantumtest');
    }

    protected function tearDown(): void
    {
        $this->removeTestUser('admantumtest');
        $this->removeWhitelistIp();
        parent::tearDown();
    }

    public function test_parse_request_extracts_params(): void
    {
        $handler = new \FlyCash\Postback\Handlers\AdMantumHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $parseRequest = $ref->getMethod('parseRequest');
        $getTransactionId = $ref->getMethod('hasTransactionId');

        $_GET['click_id'] = 'click123';
        $_GET['amount'] = '150';
        $_GET['user'] = 'admantumtest';

        $parseRequest->invoke($handler);

        $this->assertSame('click123', $ref->getProperty('transactionId')->getValue($handler));
        $this->assertSame(150, $ref->getProperty('amount')->getValue($handler));
        $this->assertSame('admantumtest', $ref->getProperty('userId')->getValue($handler));
        $this->assertTrue($getTransactionId->invoke($handler));
    }

    public function test_should_process_returns_true_for_positive_amount(): void
    {
        $handler = new \FlyCash\Postback\Handlers\AdMantumHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $shouldProcess = $ref->getMethod('shouldProcess');

        $ref->getProperty('amount')->setValue($handler, 10);
        $this->assertTrue($shouldProcess->invoke($handler));
    }

    public function test_should_process_returns_false_for_zero_amount(): void
    {
        $handler = new \FlyCash\Postback\Handlers\AdMantumHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $shouldProcess = $ref->getMethod('shouldProcess');

        $ref->getProperty('amount')->setValue($handler, 0);
        $this->assertFalse($shouldProcess->invoke($handler));
    }

    public function test_validate_user_rejects_nonexistent_username(): void
    {
        $handler = new \FlyCash\Postback\Handlers\AdMantumHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $parseRequest = $ref->getMethod('parseRequest');
        $validateUser = $ref->getMethod('validateUser');

        $_GET['click_id'] = 'nonexistent_user';
        $_GET['amount'] = '100';
        $_GET['user'] = 'nonexistent_user';

        $parseRequest->invoke($handler);
        $this->assertFalse($validateUser->invoke($handler));
    }

    public function test_verify_signature_returns_true_when_whitelisted(): void
    {
        $handler = new \FlyCash\Postback\Handlers\AdMantumHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $verifySignature = $ref->getMethod('verifySignature');

        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $this->assertTrue($verifySignature->invoke($handler));
    }
}
