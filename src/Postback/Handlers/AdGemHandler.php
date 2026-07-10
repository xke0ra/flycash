<?php

namespace FlyCash\Postback\Handlers;

use FlyCash\Postback\Handler;
use FlyCash\Logger;

class AdGemHandler extends Handler
{
    protected string $providerName = 'AdGem';

    protected function parseRequest(): void
    {
        $this->userId = $this->getString('user_id');
        $this->amount = $this->getInt('amount');
        $this->transactionId = $this->getString('transaction_id', $this->getString('tx_id'));
    }

    protected function verifySignature(): bool
    {
        $secret = $this->getConfig('ADGEM_SECRET');
        if ($secret !== '') {
            $signature = $this->getString('signature');
            $payload = $this->userId . $this->amount . $this->transactionId . $secret;
            $expected = hash_hmac('sha256', $payload, $secret);
            return hash_equals($expected, $signature);
        }

        Logger::warning('AdGem: no secret configured, enforcing IP whitelist');
        return $this->isWhitelisted();
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
            "You earned {$this->amount} points from {$type}",
            true, false
        );
    }
}
