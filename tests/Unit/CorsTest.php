<?php

namespace Tests\Unit;

use FlyCash\Api;
use PHPUnit\Framework\TestCase;

class CorsTest extends TestCase
{
    public function test_empty_allowlist_does_not_reflect_arbitrary_origin(): void
    {
        $this->assertNull(Api::resolveCorsOrigin('https://evil-attacker.example', []));
    }

    public function test_matching_origin_in_allowlist_is_returned(): void
    {
        $result = Api::resolveCorsOrigin('https://app.flycash.com', ['https://app.flycash.com']);
        $this->assertSame('https://app.flycash.com', $result);
    }

    public function test_non_matching_origin_with_populated_allowlist_returns_null(): void
    {
        $result = Api::resolveCorsOrigin('https://evil-attacker.example', ['https://app.flycash.com']);
        $this->assertNull($result);
    }

    public function test_empty_origin_returns_null(): void
    {
        $result = Api::resolveCorsOrigin('', ['https://app.flycash.com']);
        $this->assertNull($result);
    }
}
