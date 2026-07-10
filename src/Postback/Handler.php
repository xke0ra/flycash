<?php

namespace FlyCash\Postback;

use FlyCash\Api;
use FlyCash\Logger;
use PDO;

abstract class Handler
{
    protected PDO $db;
    protected string $providerName;
    protected string $userId = '';
    protected int $amount = 0;
    protected string $transactionId = '';
    protected bool $success = false;
    protected string $message = '';
    protected bool $claimed = false;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    abstract protected function parseRequest(): void;
    abstract protected function validateUser(): bool;
    abstract protected function creditUser(): bool;
    abstract protected function verifySignature(): bool;

    public function handle(): never
    {
        try {
            $this->parseRequest();

            if ($this->hasTransactionId() && $this->isDuplicate()) {
                Logger::info("Duplicate postback skipped [{$this->providerName}]", [
                    'tx_id' => $this->transactionId,
                    'user_id' => $this->userId,
                    'amount' => $this->amount,
                ]);
                $this->respondOk();
            }

            if (!$this->shouldProcess()) {
                $this->logAndRespondOk('Skipped (shouldProcess=false)');
            }

            if (!$this->verifySignature()) {
                Logger::warning("Signature verification failed [{$this->providerName}]", [
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                ]);
                $this->respondError();
            }

            if (!$this->validateUser()) {
                $this->fail('User validation failed');
            }

            // Atomic claim: insert postback_log BEFORE crediting
            // If UNIQUE(provider, transaction_id) violation, this is a duplicate
            if ($this->hasTransactionId() && !$this->claimPostback()) {
                Logger::info("Duplicate postback (atomic claim rejected) [{$this->providerName}]", [
                    'tx_id' => $this->transactionId,
                    'user_id' => $this->userId,
                    'amount' => $this->amount,
                ]);
                $this->respondOk();
            }

            if (!$this->creditUser()) {
                $this->failAfterClaim('Failed to credit points');
            }

            $this->success('Points credited successfully');
        } catch (\Throwable $e) {
            Logger::error("Postback error [{$this->providerName}]", [
                'error' => $e->getMessage(),
                'user_id' => $this->userId,
                'amount' => $this->amount,
            ]);
            if ($this->claimed) {
                $this->logPostback();
            }
            $this->respondError();
        }
    }

    protected function shouldProcess(): bool
    {
        return true;
    }

    protected function hasTransactionId(): bool
    {
        return $this->transactionId !== '';
    }

    protected function isDuplicate(): bool
    {
        if ($this->transactionId === '') {
            return false;
        }

        $stmt = $this->db->prepare(
            "SELECT id FROM postback_log WHERE transaction_id = :tx LIMIT 1"
        );
        $stmt->execute([':tx' => $this->transactionId]);

        return $stmt->fetch() !== false;
    }

    protected function claimPostback(): bool
    {
        if ($this->transactionId === '') {
            return true;
        }

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO postback_log (provider, transaction_id, user_id, amount, status, ip_addr, created_at) 
                 VALUES (:provider, :tx, :user_id, :amount, 'pending', :ip, NOW())"
            );
            $stmt->execute([
                ':provider' => $this->providerName,
                ':tx' => $this->transactionId,
                ':user_id' => $this->userId,
                ':amount' => $this->amount,
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ]);
            $this->claimed = true;
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function logPostback(): void
    {
        if ($this->transactionId === '') {
            return;
        }

        try {
            if ($this->claimed) {
                $stmt = $this->db->prepare(
                    "UPDATE postback_log SET status = :status WHERE provider = :provider AND transaction_id = :tx"
                );
                $stmt->execute([
                    ':status' => $this->success ? 'success' : 'failed',
                    ':provider' => $this->providerName,
                    ':tx' => $this->transactionId,
                ]);
            } else {
                $stmt = $this->db->prepare(
                    "INSERT INTO postback_log (provider, transaction_id, user_id, amount, status, ip_addr, created_at) 
                     VALUES (:provider, :tx, :user_id, :amount, :status, :ip, NOW())"
                );
                $stmt->execute([
                    ':provider' => $this->providerName,
                    ':tx' => $this->transactionId,
                    ':user_id' => $this->userId,
                    ':amount' => $this->amount,
                    ':status' => $this->success ? 'success' : 'failed',
                    ':ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                ]);
            }
        } catch (\Throwable $e) {
            Logger::warning('Failed to log postback', ['error' => $e->getMessage()]);
        }
    }

    protected function logAndRespondOk(string $msg): never
    {
        Logger::info("Postback {$msg} [{$this->providerName}]", [
            'user_id' => $this->userId,
            'amount' => $this->amount,
        ]);
        $this->respondOk();
    }

    protected function success(string $msg): never
    {
        $this->success = true;
        $this->message = $msg;
        $this->logPostback();
        Api::logPostback($this->providerName, $this->userId, $this->amount, true, $msg);
        $this->respondOk();
    }

    protected function fail(string $msg): never
    {
        $this->success = false;
        $this->message = $msg;
        $this->logPostback();
        Api::logPostback($this->providerName, $this->userId, $this->amount, false, $msg);
        $this->respondError();
    }

    private function failAfterClaim(string $msg): never
    {
        $this->success = false;
        $this->message = $msg;
        Logger::error("Postback failed after claim [{$this->providerName}]", [
            'user_id' => $this->userId,
            'amount' => $this->amount,
            'message' => $msg,
        ]);
        $this->logPostback();
        Api::logPostback($this->providerName, $this->userId, $this->amount, false, $msg);
        $this->respondError();
    }

    protected function respondOk(): never
    {
        echo 'OK';
        exit;
    }

    protected function respondError(): never
    {
        header('HTTP/1.0 400 Bad Request');
        echo 'FAIL';
        exit;
    }

    protected function getConfigs(): \functions
    {
        return new \functions($this->db);
    }

    protected function getAccount(): \account
    {
        return new \account($this->db);
    }

    /** @return ?array<string, mixed> */
    protected function getUserData(string $userId): ?array
    {
        $account = $this->getAccount();
        $data = $account->getuserdata($userId);
        return is_array($data) ? $data : null;
    }

    protected function getConfig(string $key): string
    {
        $configs = $this->getConfigs();
        $val = $configs->getConfig($key);
        return $val !== false && $val !== null ? (string)$val : '';
    }

    protected function isWhitelisted(): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $configs = $this->getConfigs();
        return $configs->isWhitelisted($ip);
    }

    protected function getInt(string $key, int $default = 0): int
    {
        return isset($_GET[$key]) ? (int)$_GET[$key] : $default;
    }

    protected function getString(string $key, string $default = ''): string
    {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }
}
