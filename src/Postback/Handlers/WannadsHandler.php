<?php

namespace FlyCash\Postback\Handlers;

use FlyCash\Postback\Handler;
use FlyCash\Logger;

class WannadsHandler extends Handler
{
    protected string $providerName = 'Wannads';

    protected function parseRequest(): void
    {
        $this->userId = $this->getString('subId');
        $this->amount = $this->getInt('reward');
        $this->transactionId = $this->getString('transactionId', $this->getString('txId'));
    }

    protected function verifySignature(): bool
    {
        $secret = $this->getConfig('WANNADS_SECRET');
        if ($secret !== '') {
            $signature = $this->getString('signature');
            $payload = $this->userId . $this->amount . $this->transactionId . $secret;
            $expected = hash_hmac('sha256', $payload, $secret);
            return hash_equals($expected, $signature);
        }

        Logger::warning('Wannads: no secret configured, enforcing IP whitelist');
        return $this->isWhitelisted();
    }

    protected function shouldProcess(): bool
    {
        return $this->getInt('status') === 1;
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
