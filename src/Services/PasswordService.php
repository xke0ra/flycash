<?php

namespace FlyCash\Services;

use PDO;
use PHPMailer\PHPMailer\PHPMailer;

class PasswordService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function newPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function changePassword(int $accountId, string $password): bool
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $this->db->prepare("UPDATE users SET passw = :hash, salt = '' WHERE id = :id");
        $stmt->bindParam(":id", $accountId, PDO::PARAM_INT);
        $stmt->bindParam(":hash", $hash, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /** @return array<string, mixed> */
    public function restorePointCreate(string $email, int $clientId): array
    {
        $result = [
            "error" => true,
            "error_code" => defined('ERROR_UNKNOWN') ? ERROR_UNKNOWN : -1
        ];

        $stmt = $this->db->prepare("SELECT id, fullname, email, state FROM users WHERE email = :email LIMIT 1");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return $result;
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $blockedState = defined('ACCOUNT_STATE_BLOCKED') ? ACCOUNT_STATE_BLOCKED : 2;

        if ((int)$user['state'] === $blockedState) {
            $result['error_code'] = 420;
            return $result;
        }

        $accountId = (int)$user['id'];

        $existingStmt = $this->db->prepare("SELECT * FROM restore_data WHERE accountId = :accountId AND removeAt = 0 LIMIT 1");
        $existingStmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $existingStmt->execute();

        if ($existingStmt->rowCount() > 0) {
            $row = $existingStmt->fetch();

            return [
                'error' => false,
                'error_code' => defined('ERROR_SUCCESS') ? ERROR_SUCCESS : 0,
                'accountId' => $row['accountId'],
                'hash' => $row['hash'],
                'email' => $row['email']
            ];
        }

        $currentTime = time();
        $expiresAt = $currentTime + 3600;
        $hash = bin2hex(random_bytes(32));

        $insertStmt = $this->db->prepare("INSERT INTO restore_data (accountId, hash, email, removeAt, expiresAt) VALUES (:accountId, :hash, :email, 0, :expiresAt)");
        $insertStmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $insertStmt->bindParam(":hash", $hash, PDO::PARAM_STR);
        $insertStmt->bindParam(":email", $email, PDO::PARAM_STR);
        $insertStmt->bindParam(":expiresAt", $expiresAt, PDO::PARAM_INT);
        $insertStmt->execute();

        $emailSent = $this->sendResetEmail($user['fullname'], $user['email'], $hash);

        if ($emailSent) {
            $result = [
                'error' => false,
                'error_code' => defined('ERROR_SUCCESS') ? ERROR_SUCCESS : 0,
                'accountId' => $accountId,
                'hash' => $hash,
                'email' => $email
            ];
        }

        return $result;
    }

    /** @return ?array<string, mixed> */
    public function restorePointInfo(): ?array
    {
        $hash = isset($_GET['hash']) ? $_GET['hash'] : '';

        if (empty($hash)) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM restore_data WHERE hash = :hash AND removeAt = 0 AND (expiresAt = 0 OR expiresAt > :now) LIMIT 1");
        $stmt->bindParam(":hash", $hash, PDO::PARAM_STR);
        $now = time();
        $stmt->bindParam(":now", $now, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();

            return [
                'accountId' => (int)$row['accountId'],
                'hash' => $row['hash'],
                'email' => $row['email']
            ];
        }

        return null;
    }

    public function restorePointRemove(): bool
    {
        $hash = isset($_GET['hash']) ? $_GET['hash'] : '';

        if (empty($hash)) {
            return false;
        }

        $removeAt = time();

        $stmt = $this->db->prepare("UPDATE restore_data SET removeAt = :removeAt WHERE hash = :hash");
        $stmt->bindParam(":hash", $hash, PDO::PARAM_STR);
        $stmt->bindParam(":removeAt", $removeAt, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function sendPasswordResetEmail(string $email, int $clientId): bool
    {
        $result = $this->restorePointCreate($email, $clientId);

        return isset($result['error']) && $result['error'] === false;
    }

    private function sendResetEmail(string $name, string $email, string $hash): bool
    {
        $APP_NAME = $this->getConfig('APP_NAME');
        $APP_URL = $this->getConfig('WEB_ROOT');

        $reset_link = $APP_URL . 'admin/restore/?hash=' . $hash;

        $SMTP_AUTH = $this->getConfig('SMTP_AUTH');
        $SMTP_HOST = $this->getConfig('SMTP_HOST');
        $SMTP_USERNAME = $this->getConfig('SMTP_USERNAME');
        $SMTP_EMAIL = $this->getConfig('SMTP_EMAIL');
        $SMTP_PASSWORD = $this->getConfig('SMTP_PASSWORD');
        $SMTP_SECURE = $this->getConfig('SMTP_SECURE');
        $SMTP_PORT = $this->getConfig('SMTP_PORT');

        if ($SMTP_SECURE === 'TLS') {
            $SMTP_SECURE = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $subject = $APP_NAME . ' | Password reset';

        $message_html = '<html><body>Hi ' . $name . ',<br><br>This is the link <a href="' . $reset_link . '">' . $reset_link . '</a> to reset your ' . $APP_NAME . ' account password.</body></html>';

        $message_text = 'Hi ' . $name . ', This is the link to reset your ' . $APP_NAME . ' account password : ' . $reset_link;

        $mail = new PHPMailer(true);

        try {
            if ($SMTP_AUTH) {
                $mail->isSMTP();
            }

            $mail->Host = $SMTP_HOST;
            $mail->SMTPAuth = (bool)$SMTP_AUTH;
            $mail->Username = $SMTP_USERNAME;
            $mail->Password = $SMTP_PASSWORD;
            $mail->SMTPSecure = $SMTP_SECURE;
            $mail->Port = (int)$SMTP_PORT;

            $mail->setFrom($SMTP_EMAIL, $APP_NAME);
            $mail->addAddress($email);
            $mail->addReplyTo($SMTP_EMAIL, $APP_NAME);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message_html;
            $mail->AltBody = $message_text;

            $mail->send();

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    private function getConfig(string $configName): string
    {
        $stmt = $this->db->prepare("SELECT config_value FROM configuration WHERE config_name = :configName LIMIT 1");
        $stmt->bindParam(":configName", $configName, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();

        return $row ? $row['config_value'] : '';
    }
}
