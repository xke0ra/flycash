<?php

/**
 * FLY CASH v4 — Unified Postback Router
 *
 * Single entry point for all offerwall postbacks.
 * Rewritten via .htaccess: /postbacks/{provider} -> index.php?provider={provider}
 */

require_once __DIR__ . '/../admin/core/init.inc.php';

$provider = isset($_GET['provider']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['provider']) : '';

if (empty($provider)) {
    http_response_code(400);
    echo 'No provider specified.';
    exit;
}

try {
    $router = new FlyCash\Postback\Router($dbo);
    $router->dispatch($provider);
} catch (Throwable $e) {
    FlyCash\Logger::error('Postback router error', [
        'provider' => $provider,
        'error' => $e->getMessage(),
    ]);
    http_response_code(500);
    echo 'Internal error.';
    exit;
}
