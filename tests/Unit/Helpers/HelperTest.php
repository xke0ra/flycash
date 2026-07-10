<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function test_clear_int_returns_integer(): void
    {
        if (!method_exists('helper', 'clearInt')) {
            $this->markTestSkipped('clearInt not available');
        }

        $this->assertSame(42, \helper::clearInt(42));
        $this->assertSame(0, \helper::clearInt('abc'));
    }

    public function test_clear_text_removes_tags(): void
    {
        if (!method_exists('helper', 'clearText')) {
            $this->markTestSkipped('clearText not available');
        }

        $this->assertSame('hello', \helper::clearText('hello'));
        $this->assertSame('hello', \helper::clearText('<b>hello</b>'));
    }

    public function test_escape_text_strips_tags(): void
    {
        if (!method_exists('helper', 'escapeText')) {
            $this->markTestSkipped('escapeText not available');
        }

        $result = \helper::escapeText("<b>O'Brien</b>");
        $this->assertStringNotContainsString('<b>', $result);
        $this->assertStringContainsString('O', $result);
    }

    public function test_new_authenticity_token_sets_session(): void
    {
        if (!method_exists('helper', 'newAuthenticityToken')) {
            $this->markTestSkipped('newAuthenticityToken not available');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION)) {
            $this->markTestSkipped('Session not available in CLI');
        }

        \helper::newAuthenticityToken();

        $this->assertArrayHasKey('authenticity_token', $_SESSION);
        $this->assertArrayHasKey('csrf_token', $_SESSION);
        $this->assertSame($_SESSION['authenticity_token'], $_SESSION['csrf_token']);
        $this->assertEquals(64, strlen($_SESSION['authenticity_token']));
    }

    public function test_get_authenticity_token_returns_token(): void
    {
        if (!method_exists('helper', 'getAuthenticityToken')) {
            $this->markTestSkipped('getAuthenticityToken not available');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION)) {
            $this->markTestSkipped('Session not available in CLI');
        }

        \helper::newAuthenticityToken();
        $token = \helper::getAuthenticityToken();

        $this->assertSame($_SESSION['authenticity_token'], $token);
    }

    public function test_verify_csrf_token(): void
    {
        if (!method_exists('helper', 'verifyCsrfToken')) {
            $this->markTestSkipped('verifyCsrfToken not available');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION)) {
            $this->markTestSkipped('Session not available in CLI');
        }

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $validToken = $_SESSION['csrf_token'];

        $this->assertTrue(\helper::verifyCsrfToken($validToken));
        $this->assertFalse(\helper::verifyCsrfToken('invalid-token'));
        $this->assertFalse(\helper::verifyCsrfToken(''));
    }
}
