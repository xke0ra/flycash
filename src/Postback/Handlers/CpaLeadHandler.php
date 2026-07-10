<?php

namespace FlyCash\Postback\Handlers;

use FlyCash\Postback\Handler;

class CpaLeadHandler extends Handler
{
    protected string $providerName = 'CpaLead';

    protected function parseRequest(): void
    {
        $this->userId = $this->getString('subid2');
        $this->amount = $this->getInt('virtual_currency');
    }

    protected function verifySignature(): bool
    {
        if ($this->isWhitelisted()) {
            return true;
        }

        return false;
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
