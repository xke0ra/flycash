<?php

namespace FlyCash\Controller;

use FlyCash\Services\AuthService;
use PDO;

class LoginController
{
    private AuthService $auth;
    private PDO $db;

    public function __construct(PDO $pdo, ?AuthService $auth = null)
    {
        $this->db = $pdo;
        $this->auth = $auth ?? new AuthService($pdo);
    }

    /** @return array<string, mixed> */
    public function login(string $username, string $password, int $clientId): array
    {
        $result = $this->auth->signin($username, $password);

        if ($result['error'] !== false) {
            return $result;
        }

        $accountId = (int) $result['accountId'];
        $accessToken = bin2hex(random_bytes(32));

        $this->auth->setSession($accountId, $accessToken);

        return [
            'error' => false,
            'error_code' => 0,
            'accountId' => $accountId,
            'accessToken' => $accessToken,
            'clientId' => $clientId,
        ];
    }

    public function logout(int $accountId): void
    {
        $stmt = $this->db->prepare("UPDATE access_data SET removeAt = :removeAt WHERE accountId = :accountId AND removeAt = 0");
        $removeAt = time();
        $stmt->execute([':removeAt' => $removeAt, ':accountId' => $accountId]);
    }

    public function checkSession(int $accountId, string $accessToken): bool
    {
        return $this->auth->authorize($accountId, $accessToken);
    }
}
