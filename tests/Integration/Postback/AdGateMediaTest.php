<?php

namespace Tests\Integration\Postback;

use PHPUnit\Framework\TestCase;
use PDO;

class AdGateMediaTest extends TestCase
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

    public function test_parse_request_extracts_params(): void
    {
        $handler = new \FlyCash\Postback\Handlers\AdGateMediaHandler($this->getPDO());

        $ref = new \ReflectionClass($handler);
        $parseRequest = $ref->getMethod('parseRequest');
        $userIdProp = $ref->getProperty('userId');
        $amountProp = $ref->getProperty('amount');

        $_GET['user_id'] = 'adgatemedia_test';
        $_GET['point_value'] = '150';
        $_GET['tx_id'] = 'tx-999';

        $parseRequest->invoke($handler);

        $this->assertSame('adgatemedia_test', $userIdProp->getValue($handler));
        $this->assertSame(150, $amountProp->getValue($handler));
    }
}
