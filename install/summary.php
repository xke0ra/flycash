<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#6366f1">
    <title>FLY CASH - Installation Complete</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --primary:#6366f1; --primary-dark:#4f46e5;
            --dark:#0f172a; --gray-50:#f8fafc; --gray-100:#f1f5f9;
            --gray-200:#e2e8f0; --gray-600:#475569; --gray-800:#1e293b; --gray-900:#0f172a;
            --font:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
            --radius:12px; --shadow-lg:0 10px 40px rgba(0,0,0,.08);
        }
        body { font-family:var(--font); background:#f8fafc; color:var(--gray-800); line-height:1.6; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:24px; }
        a { text-decoration:none; color:var(--primary); font-weight:600; }
        a:hover { text-decoration:underline; }
        .card { max-width:520px; width:100%; background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius); box-shadow:var(--shadow-lg); padding:48px 40px; text-align:center; }
        .icon-wrap { width:64px; height:64px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; }
        .icon-success { background:#dcfce7; }
        .icon-error { background:#fef2f2; }
        .card h1 { font-size:24px; font-weight:800; color:var(--gray-900); margin-bottom:8px; }
        .card .subtitle { font-size:15px; color:var(--gray-600); margin-bottom:24px; }
        .btn { display:inline-flex; align-items:center; gap:8px; padding:12px 28px; border-radius:10px; font-weight:600; font-size:14px; border:none; cursor:pointer; transition:all .2s; font-family:var(--font); }
        .btn-primary { background:var(--primary); color:#fff; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-outline { background:#fff; color:var(--gray-700); border:1.5px solid var(--gray-200); }
        .btn-outline:hover { border-color:var(--primary); color:var(--primary); }
        .mt-24 { margin-top:24px; }
        .help-text { font-size:13px; color:var(--gray-600); margin-top:24px; padding-top:20px; border-top:1px solid var(--gray-100); }
    </style>
</head>
<body>
    <div class="card">
        <?php if (isset($_GET['error'])) { ?>
            <div class="icon-wrap icon-error">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </div>
            <h1 style="color:#dc2626;">Installation Failed</h1>
            <p class="subtitle">
                <?php
                if ($_GET['error'] == 1) echo 'Could not connect to the database. Please check the connection details and try again.';
                elseif ($_GET['error'] == 2) echo 'Cannot edit config.php. Check file permissions on your web server.';
                elseif ($_GET['error'] == 3) echo 'Cannot process the pocket_db.sql file.';
                else echo 'An unexpected error occurred.';
                ?>
            </p>
            <a href="index.php" class="btn btn-primary mt-24">Try Again</a>
        <?php } elseif (isset($_GET['success'])) { ?>
            <div class="icon-wrap icon-success">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <h1 style="color:#16a34a;">Installation Successful</h1>
            <p class="subtitle">Congratulations, you've successfully installed the WebPanel.</p>
            <div class="mt-24">
                <span style="font-size:14px;color:var(--gray-600);">For security, delete the <code style="background:var(--gray-100);padding:2px 8px;border-radius:4px;font-size:13px;">install</code> folder.</span>
            </div>
            <a href="../admin/login.php" class="btn btn-primary mt-24">Go to Admin Panel</a>
        <?php } else { ?>
            <?php header("Location: index.php"); exit; ?>
        <?php } ?>

        <div class="help-text">
            Need help? Open a ticket on <a href="https://www.aym.com/support" target="_blank">AYM Support</a>
        </div>
    </div>
</body>
</html>
