<?php

namespace Tests\Integration\Postback;

use PHPUnit\Framework\TestCase;
use PDO;

class AdGemTest extends TestCase
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
        $this->createTestUser('adgemtest');
    }

    protected function tearDown(): void
    {
        $this->removeTestUser('adgemtest');
        $this->removeWhitelistIp();
        parent::tearDown();
    }

    public function test_handler_instantiation(): void
    {
        $handler = new \FlyCash\Postback\Handlers\AdGemHandler($this->getPDO());
        $this->assertInstanceOf(\FlyCash\Postback\Handlers\AdGemHandler::class, $handler);
    }

    public function test_handler_rejects_invalid_user(): void
    {
        $handler = new \FlyCash\Postback\Handlers\AdGemHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $parseRequest = $ref->getMethod('parseRequest');
        $validateUser = $ref->getMethod('validateUser');

        $_GET['user_id'] = 'nonexistent';
        $_GET['amount'] = '100';

        $parseRequest->invoke($handler);
        $this->assertFalse($validateUser->invoke($handler));
    }

    public function test_handler_accepts_valid_user(): void
    {
        $handler = new \FlyCash\Postback\Handlers\AdGemHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $parseRequest = $ref->getMethod('parseRequest');
        $validateUser = $ref->getMethod('validateUser');

        $_GET['user_id'] = 'adgemtest';
        $_GET['amount'] = '200';

        $parseRequest->invoke($handler);
        $this->assertTrue($validateUser->invoke($handler));
    }
}
