<?php

namespace Tests\Integration\Api;

use PHPUnit\Framework\TestCase;

class V4AuthTest extends TestCase
{
    public function test_validate_client_accepts_correct_id(): void
    {
        \FlyCash\Api::validateClient(['clientId' => CLIENT_ID]);
        $this->assertTrue(true);
    }

    public function test_require_params_passes_with_all_params(): void
    {
        \FlyCash\Api::requireParams(['name', 'email'], ['name' => 'John', 'email' => 'john@test.com']);
        $this->assertTrue(true);
    }

    public function test_get_json_body_returns_array(): void
    {
        $body = \FlyCash\Api::getJsonBody();
        $this->assertIsArray($body);
    }
}
