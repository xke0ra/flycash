<?php

namespace FlyCash\Postback\Handlers;

use FlyCash\Postback\Handler;
use FlyCash\Logger;

class OgAdsHandler extends Handler
{
    protected string $providerName = 'OgAds';

    protected function parseRequest(): void
    {
        $this->userId = $this->getString('user');
        $this->amount = $this->getInt('amount');

        // OgAds does not provide a unique transaction ID;
        // leave empty so we rely on IP whitelist + daily cap instead
        $this->transactionId = '';

        // Convert USD to points ($1 USD = 300 Points)
        $this->amount = (int)($this->amount * 300);
    }

    protected function shouldProcess(): bool
    {
        return $this->amount > 0 && !empty($this->userId);
    }

    protected function verifySignature(): bool
    {
        $secret = $this->getConfig('OGADS_SECRET');
        if ($secret !== '') {
            $signature = $this->getString('signature');
            $payload = $this->userId . $this->amount . $secret;
            $expected = hash_hmac('sha256', $payload, $secret);
            return hash_equals($expected, $signature);
        }

        Logger::warning('OgAds: no secret configured, enforcing IP whitelist');
        return $this->isWhitelisted();
    }

    protected function validateUser(): bool
    {
        if (empty($this->userId)) {
            return false;
        }

        $stmt = $this->db->prepare("SELECT id, login FROM users WHERE login = :user_id LIMIT 1");
        $stmt->execute([':user_id' => $this->userId]);

        $user = $stmt->fetch();
        if ($user === false) {
            return false;
        }

        $uid = (int)$user['id'];

        // Enforce daily cap per user (max 5000 points/day)
        $today = strtotime(date("Y-m-d", time()));
        $capStmt = $this->db->prepare(
            "SELECT COALESCE(SUM(points), 0) FROM tracker WHERE user_id = :uid AND date >= :today AND type LIKE 'OgAds%'"
        );
        $capStmt->execute([':uid' => $uid, ':today' => $today]);
        $dailyTotal = (int)$capStmt->fetchColumn();

        if ($dailyTotal + $this->amount > 5000) {
            Logger::warning("OgAds: daily cap exceeded for {$this->userId}", [
                'daily_total' => $dailyTotal,
                'attempted' => $this->amount,
            ]);
            return false;
        }

        return true;
    }

    protected function creditUser(): bool
    {
        $type = "{$this->providerName} : " . $this->getString('of_name', 'Offerwall');
        $configs = $this->getConfigs();

        $result = $configs->creditUserPoints(
            $this->userId, $this->amount, $type,
            "You earned {$this->amount} points from {$type}",
            true, false
        );

        // Log to ogadspostback table for revenue tracking
        try {
            $offerName = $this->getString('of_name', '');
            $payout = $this->getString('amount', '0');

            $stmt = $this->db->prepare(
                "INSERT INTO ogadspostback (offer_id, offer_name, aff_sub3, payout, time) 
                 VALUES (:offer_id, :offer_name, :aff_sub3, :payout, :today)"
            );
            $stmt->execute([
                ':offer_id' => $this->getString('of_id', ''),
                ':offer_name' => $offerName,
                ':aff_sub3' => $this->userId,
                ':payout' => $payout,
                ':today' => date('Y-m-d'),
            ]);
        } catch (\Throwable $e) {
            // Non-critical: just log
        }

        return $result;
    }
}
