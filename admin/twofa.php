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
        $messageType = 'error';
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
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: -apple-system, system-ui, sans-serif; background:#f1f5f9; padding:40px 24px; color:#1e293b; }
        .card { max-width:520px; margin:0 auto; background:#fff; border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,.08); padding:32px; }
        h1 { font-size:22px; margin-bottom:8px; }
        p { color:#64748b; font-size:14px; margin-bottom:24px; line-height:1.6; }
        .secret-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 16px; font-family:monospace; font-size:14px; word-break:break-all; margin-bottom:20px; }
        .qr-placeholder { width:200px; height:200px; background:#f1f5f9; border-radius:12px; margin:0 auto 20px; display:flex; align-items:center; justify-content:center; font-size:13px; color:#94a3b8; text-align:center; padding:10px; }
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; font-size:14px; font-weight:500; margin-bottom:6px; color:#334155; }
        .form-control { width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; outline:none; }
        .form-control:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12); }
        .btn { display:inline-block; padding:10px 20px; border:none; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; }
        .btn-primary { background:#6366f1; color:#fff; }
        .btn-primary:hover { background:#4f46e5; }
        .btn-danger { background:#ef4444; color:#fff; }
        .btn-danger:hover { background:#dc2626; }
        .btn-outline { background:transparent; border:1.5px solid #e2e8f0; color:#475569; }
        .btn-outline:hover { background:#f8fafc; }
        .alert { padding:12px 16px; border-radius:10px; font-size:14px; margin-bottom:20px; }
        .alert-success { background:#f0fdf4; color:#166534; border-left:4px solid #22c55e; }
        .alert-error { background:#fef2f2; color:#991b1b; border-left:4px solid #ef4444; }
        .alert-info { background:#eff6ff; color:#1e40af; border-left:4px solid #3b82f6; }
        .status { display:inline-block; padding:4px 12px; border-radius:20px; font-size:13px; font-weight:600; }
        .status-enabled { background:#dcfce7; color:#166534; }
        .status-disabled { background:#f1f5f9; color:#64748b; }
        .actions { margin-top:24px; display:flex; gap:12px; flex-wrap:wrap; }
        .qr-img { text-align:center; margin-bottom:20px; }
        .qr-img img { width:200px; height:200px; border-radius:12px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Two-Factor Authentication</h1>
        <p>Add an extra layer of security to your admin account using Google Authenticator, Authy, or any TOTP-compatible app.</p>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <p>Status: <span class="status <?php echo $twofaEnabled ? 'status-enabled' : 'status-disabled'; ?>"><?php echo $twofaEnabled ? 'Enabled' : 'Disabled'; ?></span></p>

        <?php if (!empty($twofaSecret)): ?>
            <div class="qr-img">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($provisioningUri); ?>" alt="QR Code">
            </div>
            <div class="secret-box"><?php echo htmlspecialchars($twofaSecret); ?></div>
        <?php endif; ?>

        <?php if ($twofaEnabled): ?>
            <div class="actions">
                <a href="twofa.php?action=disable" class="btn btn-danger" onclick="return confirm('Disable 2FA?')">Disable 2FA</a>
                <a href="admin.php" class="btn btn-outline">Back to Dashboard</a>
            </div>
        <?php else: ?>
            <?php if (empty($twofaSecret)): ?>
                <div class="actions">
                    <a href="twofa.php?action=generate" class="btn btn-primary">Generate Secret</a>
                    <a href="admin.php" class="btn btn-outline">Back to Dashboard</a>
                </div>
            <?php else: ?>
                <form method="post" action="twofa.php?action=enable">
                    <div class="form-group">
                        <label>Enter the 6-digit code from your authenticator app:</label>
                        <input class="form-control" type="text" name="twofa_code" maxlength="6" pattern="[0-9]{6}" required placeholder="000000">
                    </div>
                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Enable 2FA</button>
                        <a href="twofa.php" class="btn btn-outline">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
