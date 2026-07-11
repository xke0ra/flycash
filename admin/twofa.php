<?php

require_once "core/init.inc.php";

if (!admin::isSession()) {
    header("Location: login.php");
    exit;
}

$configs = new functions($dbo);
$action = isset($_GET['action']) ? $_GET['action'] : '';
$message = '';
$messageType = '';

$stmt = $dbo->prepare("SELECT twofa_secret, twofa_enabled FROM admins WHERE id = :id LIMIT 1");
$stmt->execute(array(':id' => admin::getAdminID()));
$adminData = $stmt->fetch(PDO::FETCH_ASSOC);
$twofaEnabled = $adminData ? intval($adminData['twofa_enabled']) : 0;
$twofaSecret = $adminData ? $adminData['twofa_secret'] : '';

if ($action === 'enable' && !empty($_POST['twofa_code'])) {
    $code = trim($_POST['twofa_code']);
    if (totp::verify($code, $twofaSecret)) {
        $upd = $dbo->prepare("UPDATE admins SET twofa_enabled = 1 WHERE id = :id");
        $upd->execute(array(':id' => admin::getAdminID()));
        $message = '2FA enabled successfully.';
        $messageType = 'success';
        $twofaEnabled = 1;
    } else {
        $message = 'Invalid code. Please try again.';
        $messageType = 'danger';
    }
}

if ($action === 'disable') {
    $upd = $dbo->prepare("UPDATE admins SET twofa_enabled = 0, twofa_secret = '' WHERE id = :id");
    $upd->execute(array(':id' => admin::getAdminID()));
    $message = '2FA disabled.';
    $messageType = 'success';
    $twofaEnabled = 0;
    $twofaSecret = '';
}

if ($action === 'generate' || (empty($twofaSecret) && !$twofaEnabled)) {
    $twofaSecret = totp::generateSecret();
    $upd = $dbo->prepare("UPDATE admins SET twofa_secret = :secret WHERE id = :id");
    $upd->execute(array(':secret' => $twofaSecret, ':id' => admin::getAdminID()));
    $message = 'New secret generated. Scan the QR code with Google Authenticator, then enter the code below to enable.';
    $messageType = 'info';
}

$APP_NAME = $configs->getConfig('APP_NAME');
$provisioningUri = '';
if (!empty($twofaSecret)) {
    $provisioningUri = totp::getProvisioningUri(admin::getAdminUsername(), $twofaSecret, $APP_NAME . ' Admin');
    $qrData = $provisioningUri;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>2FA Settings | <?php echo htmlspecialchars($APP_NAME); ?></title>
    <meta name="theme-color" content="#6366f1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-card wide">
        <div class="auth-title">Two-Factor Authentication</div>
        <div class="auth-subtitle">Add an extra layer of security to your admin account using Google Authenticator, Authy, or any TOTP-compatible app.</div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <p style="margin-bottom:20px;font-size:14px;color:var(--gray-600);">
            Status: <span class="status-badge <?php echo $twofaEnabled ? 'on' : 'off'; ?>"><?php echo $twofaEnabled ? 'Enabled' : 'Disabled'; ?></span>
        </p>

        <?php if (!empty($twofaSecret)): ?>
            <div class="qr-wrapper">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($provisioningUri); ?>" alt="QR Code">
            </div>
            <div class="secret-box"><?php echo htmlspecialchars($twofaSecret); ?></div>
        <?php endif; ?>

        <?php if ($twofaEnabled): ?>
            <div class="auth-actions">
                <a href="twofa.php?action=disable" class="btn btn-danger btn-sm" onclick="return confirm('Disable 2FA?')">Disable 2FA</a>
                <a href="admin.php" class="btn btn-outline btn-sm">Back to Dashboard</a>
            </div>
        <?php else: ?>
            <?php if (empty($twofaSecret)): ?>
                <div class="auth-actions">
                    <a href="twofa.php?action=generate" class="btn btn-primary btn-sm">Generate Secret</a>
                    <a href="admin.php" class="btn btn-outline btn-sm">Back to Dashboard</a>
                </div>
            <?php else: ?>
                <form method="post" action="twofa.php?action=enable">
                    <div class="form-group">
                        <label>Enter the 6-digit code from your authenticator app:</label>
                        <input class="form-control" type="text" name="twofa_code" maxlength="6" pattern="[0-9]{6}" required placeholder="000000">
                    </div>
                    <div class="auth-actions">
                        <button type="submit" class="btn btn-primary btn-sm">Enable 2FA</button>
                        <a href="twofa.php" class="btn btn-outline btn-sm">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
