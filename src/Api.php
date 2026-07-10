<?php

namespace FlyCash;

class Api
{
    public static function jsonResponse(mixed $data, int $httpCode = 200): never
    {
        http_response_code($httpCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function jsonError(int $errorCode, string $message = 'Unknown error', int $httpCode = 400, ?\Throwable $previous = null): never
    {
        if ($previous !== null) {
            Logger::error('API error', [
                'error_code' => $errorCode,
                'message' => $message,
                'exception' => $previous->getMessage(),
            ]);
        }

        http_response_code($httpCode);
        echo json_encode([
            'error' => true,
            'error_code' => $errorCode,
            'error_description' => $message,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** @return array<string, mixed> */
    public static function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (stripos($contentType, 'application/json') !== false) {
            $data = json_decode($raw, true);
            if (is_array($data)) {
                return $data;
            }
        }

        if (!empty($_POST['data'])) {
            $decoded = json_decode($_POST['data'], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        if (!empty($_POST)) {
            return $_POST;
        }

        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    /**
     * @param array<int, string> $params
     * @param array<string, mixed> $data
     */
    public static function requireParams(array $params, array $data): void
    {
        foreach ($params as $p) {
            if (!isset($data[$p]) || (is_string($data[$p]) && trim($data[$p]) === '')) {
                self::jsonError(ERROR_UNKNOWN, "Missing required parameter: {$p}", 400);
            }
        }
    }

    /** @param array<string, mixed> $data */
    public static function validateClient(array $data): void
    {
        $clientId = isset($data['clientId']) ? (int)$data['clientId'] : 0;
        if ($clientId !== (int)CLIENT_ID) {
            self::jsonError(ERROR_UNKNOWN, 'Invalid client Id.', 401);
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function authorizeRequest(array $data, \PDO $dbo): array
    {
        $accountId = isset($data['accountId']) ? (int)$data['accountId'] : 0;
        $accessToken = $data['accessToken'] ?? '';

        if ($accountId === 0 || $accessToken === '') {
            self::jsonError(ERROR_ACCESS_TOKEN, 'Missing authorization credentials.', 401);
        }

        $auth = new \auth($dbo);
        if (!$auth->authorize($accountId, $accessToken)) {
            Logger::warning('API auth failure', ['accountId' => $accountId]);
            self::jsonError(ERROR_ACCESS_TOKEN, 'Invalid or expired token.', 401);
        }

        return ['accountId' => $accountId, 'accessToken' => $accessToken];
    }

    public static function logRequest(): void
    {
        Logger::info('API request', [
            'method' => $_SERVER['REQUEST_METHOD'],
            'endpoint' => $_SERVER['REQUEST_URI'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
    }

    /**
     * @param array<int, string> $allowedOrigins
     */
    public static function resolveCorsOrigin(string $origin, array $allowedOrigins): ?string
    {
        if ($origin === '') {
            return null;
        }
        if (in_array($origin, $allowedOrigins, true)) {
            return $origin;
        }
        return null;
    }

    public static function logPostback(string $provider, string $userId, int $amount, bool $success, string $message = ''): void
    {
        $level = $success ? 'info' : 'warning';
        Logger::$level('Postback received', [
            'provider' => $provider,
            'user_id' => $userId,
            'amount' => $amount,
            'success' => $success,
            'message' => $message,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    }
}
