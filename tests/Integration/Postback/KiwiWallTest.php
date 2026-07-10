<?php

namespace Tests\Integration\Postback;

use PHPUnit\Framework\TestCase;
use PDO;

class KiwiWallTest extends TestCase
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

    public function test_should_process_returns_true_when_status_is_1(): void
    {
        $handler = new \FlyCash\Postback\Handlers\KiwiWallHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $shouldProcess = $ref->getMethod('shouldProcess');

        $_GET['status'] = '1';
        $this->assertTrue($shouldProcess->invoke($handler));
    }

    public function test_should_process_returns_false_when_status_is_0(): void
    {
        $handler = new \FlyCash\Postback\Handlers\KiwiWallHandler($this->getPDO());
        $ref = new \ReflectionClass($handler);
        $shouldProcess = $ref->getMethod('shouldProcess');

        $_GET['status'] = '0';
        $this->assertFalse($shouldProcess->invoke($handler));
    }
}
