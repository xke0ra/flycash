<?php

namespace FlyCash\Postback\Handlers;

use FlyCash\Postback\Handler;
use FlyCash\Logger;

class AdScendMediaHandler extends Handler
{
    protected string $providerName = 'AdScendMedia';

    protected function parseRequest(): void
    {
        $this->userId = $this->getString('sub1');
        $this->amount = $this->getInt('rate');
        $this->transactionId = $this->getString('offerid');
    }

    protected function shouldProcess(): bool
    {
        $maxAmount = (int)$this->getConfig('ADSCENDMEDIA_MAX_AMOUNT');
        if ($maxAmount <= 0) {
            $maxAmount = 99999;
        }

        if ($this->amount >= $maxAmount) {
            Logger::warning("AdScendMedia: amount {$this->amount} exceeds max {$maxAmount}", [
                'user_id' => $this->userId,
                'amount' => $this->amount,
                'max' => $maxAmount,
            ]);
            return false;
        }

        return $this->amount > 0;
    }

    protected function verifySignature(): bool
    {
        $secret = $this->getConfig('ADSCENDMEDIA_SECRET');
        if ($secret !== '') {
            $signature = $this->getString('signature');
            $payload = $this->transactionId . $this->userId . $this->amount . $secret;
            $expected = hash_hmac('sha256', $payload, $secret);
            return hash_equals($expected, $signature);
        }

        Logger::warning('AdScendMedia: no secret configured, enforcing IP whitelist');
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
            "You earned {$this->amount} points from {$type}"
        );
    }
}
