<?php

namespace FlyCash\Postback\Handlers;

use FlyCash\Postback\Handler;
use FlyCash\Logger;

class AdMantumHandler extends Handler
{
    protected string $providerName = 'AdMantum';

    protected function parseRequest(): void
    {
        $this->transactionId = $this->getString('click_id');
        $this->amount = $this->getInt('amount');
        $this->userId = $this->getString('user');
    }

    protected function verifySignature(): bool
    {
        $secret = $this->getConfig('ADMANTUM_SECRET');
        if ($secret !== '') {
            $signature = $this->getString('signature');
            $payload = $this->transactionId . $this->amount . $this->userId . $secret;
            $expected = hash_hmac('sha256', $payload, $secret);
            return hash_equals($expected, $signature);
        }

        Logger::warning('AdMantum: no secret configured, enforcing IP whitelist');
        return $this->isWhitelisted();
    }

    protected function shouldProcess(): bool
    {
        return $this->amount > 0;
    }

    protected function validateUser(): bool
    {
        $click_id = $this->transactionId;
        if (empty($click_id)) {
            return false;
        }

        $account = $this->getAccount();
        $userdata = $account->getuserdata($click_id);
        $username = isset($userdata['username']) ? $userdata['username'] : 'none';

        if ($click_id === $username) {
            $this->userId = $username;
            return true;
        }

        // Android App User — check offer_status table
        $configs = $this->getConfigs();
        $offerData = $configs->getofferStatusData($click_id);

        $offerClickId = isset($offerData['cid']) ? $offerData['cid'] : '';
        $offerStatus = isset($offerData['status']) ? $offerData['status'] : '';
        $offerUser = isset($offerData['user']) ? $offerData['user'] : '';

        if ($offerClickId === $click_id && $offerStatus == 0) {
            $this->userId = $offerUser;
            $userdata = $account->getuserdata($this->userId);
            return isset($userdata['username']) && $userdata['username'] === $this->userId;
        }

        if ($offerStatus == 1) {
            Logger::info("AdMantum: offer already rewarded", ['click_id' => $click_id]);
            $this->respondOkAndExit();
        }

        return false;
    }

    private function respondOkAndExit(): never
    {
        header("HTTP/1.1 200");
        echo "OK - Postback Success";
        exit;
    }

    protected function creditUser(): bool
    {
        $type = "{$this->providerName} offerwall Credit";
        $configs = $this->getConfigs();

        $configs->completeofferStatusData($this->transactionId);

        return $configs->creditUserPoints(
            $this->userId, $this->amount, $type,
            "You earned {$this->amount} points from {$type}"
        );
    }
}
