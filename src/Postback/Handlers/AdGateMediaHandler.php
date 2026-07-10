<?php

namespace FlyCash\Postback\Handlers;

use FlyCash\Postback\Handler;
use FlyCash\Logger;

class AdGateMediaHandler extends Handler
{
    protected string $providerName = 'AdGateMedia';

    protected function parseRequest(): void
    {
        $this->userId = $this->getString('user_id');
        $this->amount = $this->getInt('point_value');
        $this->transactionId = $this->getString('tx_id');
    }

    protected function verifySignature(): bool
    {
        if ($this->isWhitelisted()) {
            return true;
        }

        $secret = $this->getConfig('ADGATEMEDIA_SECRET');
        if ($secret === '') {
            Logger::warning('AdGateMedia: no secret configured, falling back to IP whitelist');
            return $this->isWhitelisted();
        }

        $signature = $this->getString('signature');
        $payload = $this->userId . $this->amount . $secret;
        $expected = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $signature);
    }

    protected function validateUser(): bool
    {
        if (empty($this->userId) || $this->amount <= 0) {
            return false;
        }

        $userData = $this->getUserData($this->userId);
        return $userData !== null && ($userData['username'] ?? '') === $this->userId;
    }

    protected function creditUser(): bool
    {
        $type = "{$this->providerName} offerwall Credit";
        $configs = $this->getConfigs();
        return $configs->creditUserPoints(
            $this->userId, $this->amount, $type,
            "You earned {$this->amount} points from {$type}"
        );
    }
}
