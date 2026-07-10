<?php

namespace FlyCash\Services;

use PDO;

class EmailVerificationService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function isEmailVerified(int $accountId): bool
    {
        $stmt = $this->db->prepare("SELECT email_verified FROM users WHERE id = :id LIMIT 1");
        $stmt->bindParam(":id", $accountId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && intval($row['email_verified']) === 1;
    }

    public function sendVerificationEmail(int $accountId): bool
    {
        $stmt = $this->db->prepare("SELECT id, login, email, email_verified FROM users WHERE id = :id LIMIT 1");
        $stmt->bindParam(":id", $accountId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || intval($user['email_verified']) === 1) {
            return false;
        }

        $hash = bin2hex(random_bytes(32));
        $currentTime = time();
        $expiresAt = $currentTime + 86400;

        $stmt = $this->db->prepare("INSERT INTO email_verify (accountId, email, hash, createAt, expiresAt) VALUES (:aid, :email, :hash, :ca, :ea)");
        $stmt->execute(array(':aid' => $accountId, ':email' => $user['email'], ':hash' => $hash, ':ca' => $currentTime, ':ea' => $expiresAt));

        $configService = new ConfigService($this->db);
        $appName = $configService->get('APP_NAME');
        $webRoot = $configService->get('WEB_ROOT');
        $verifyLink = $webRoot . 'admin/api/v4/auth/verify?hash=' . $hash;

        $subject = $appName . ' | Email Verification';

        $message_html = '<html><body>Hi ' . $user['login'] . ',<br><br>Please verify your email by clicking this link:<br><a href="' . $verifyLink . '">' . $verifyLink . '</a><br><br>This link expires in 24 hours.</body></html>';

        $message_text = 'Hi ' . $user['login'] . ', Please verify your email by clicking this link: ' . $verifyLink;

        $SMTP_AUTH = $configService->get('SMTP_AUTH');
        $SMTP_HOST = $configService->get('SMTP_HOST');
        $SMTP_USERNAME = $configService->get('SMTP_USERNAME');
        $SMTP_EMAIL = $configService->get('SMTP_EMAIL');
        $SMTP_PASSWORD = $configService->get('SMTP_PASSWORD');
        $SMTP_SECURE = $configService->get('SMTP_SECURE');
        $SMTP_PORT = $configService->get('SMTP_PORT');

        if ($SMTP_SECURE === 'TLS') {
            $SMTP_SECURE = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            if (filter_var($SMTP_AUTH, FILTER_VALIDATE_BOOLEAN)) {
                $mail->isSMTP();
            }
            $mail->Host = $SMTP_HOST;
            $mail->SMTPAuth = filter_var($SMTP_AUTH, FILTER_VALIDATE_BOOLEAN);
            $mail->Username = $SMTP_USERNAME;
            $mail->Password = $SMTP_PASSWORD;
            $mail->SMTPSecure = $SMTP_SECURE;
            $mail->Port = intval($SMTP_PORT);

            $mail->setFrom($SMTP_EMAIL, $appName);
            $mail->addAddress($user['email']);
            $mail->addReplyTo($SMTP_EMAIL, $appName);

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

    public function verifyEmail(string $hash): bool
    {
        $hash = trim($hash);
        $hash = strip_tags($hash);
        $hash = htmlspecialchars($hash);

        $stmt = $this->db->prepare("SELECT id, accountId, expiresAt FROM email_verify WHERE hash = :hash AND used = 0 LIMIT 1");
        $stmt->execute(array(':hash' => $hash));

        if ($stmt->rowCount() === 0) {
            return false;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (time() > intval($row['expiresAt'])) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE email_verify SET used = 1 WHERE id = :id");
        $stmt->execute(array(':id' => $row['id']));

        $stmt = $this->db->prepare("UPDATE users SET email_verified = 1 WHERE id = :id");
        $stmt->execute(array(':id' => $row['accountId']));

        return true;
    }
}
