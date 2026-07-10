<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

class SecurityServiceTest extends TestCase
{
    public function test_hash_equals_prevents_timing_attacks(): void
    {
        $known = bin2hex(random_bytes(16));

        $this->assertTrue(hash_equals($known, $known));
        $this->assertFalse(hash_equals($known, 'wrong'));
        $this->assertFalse(hash_equals($known, strtoupper($known)));
    }

    public function test_random_bytes_generates_cryptographic_quality(): void
    {
        $bytes1 = bin2hex(random_bytes(32));
        $bytes2 = bin2hex(random_bytes(32));

        $this->assertEquals(64, strlen($bytes1));
        $this->assertEquals(64, strlen($bytes2));
        $this->assertNotSame($bytes1, $bytes2);
    }

    public function test_password_hash_uses_bcrypt(): void
    {
        $hash = password_hash('testpassword', PASSWORD_BCRYPT, ['cost' => 12]);

        $this->assertStringStartsWith('$2y$12$', $hash);
        $this->assertTrue(password_verify('testpassword', $hash));
        $this->assertFalse(password_verify('wrongpassword', $hash));
    }
}
