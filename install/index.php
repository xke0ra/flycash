<?php
include_once("../admin/core/config.php");
	
	if($INSTALL_STATUS == "SUCCESS"){
		header("Location: summary.php?success=1");
		exit;
	}

	$constants_file = '../admin/core/config.php';
    $errors = 0;
    $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'].= $_SERVER['REQUEST_URI'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#6366f1">
    <title>FLY CASH - Admin Panel Installation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --primary:#6366f1; --primary-dark:#4f46e5; --primary-light:#a5b4fc;
            --dark:#0f172a; --gray-50:#f8fafc; --gray-100:#f1f5f9;
            --gray-200:#e2e8f0; --gray-600:#475569; --gray-800:#1e293b; --gray-900:#0f172a;
            --font:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
            --radius:12px; --shadow:0 1px 3px rgba(0,0,0,.06); --shadow-lg:0 10px 40px rgba(0,0,0,.08);
        }
        body { font-family:var(--font); background:#f8fafc; color:var(--gray-800); line-height:1.6; padding:40px 24px; }
        a { text-decoration:none; color:var(--primary); }
        .install-header { text-align:center; margin-bottom:32px; }
        .install-header h1 { font-size:28px; font-weight:800; color:var(--gray-900); letter-spacing:-.03em; }
        .install-header h1 span { color:var(--primary); }
        .install-header p { color:var(--gray-600); font-size:15px; }
        .install-card { max-width:700px; margin:0 auto; background:#fff; border:1px solid var(--gray-200); border-radius:var(--radius); box-shadow:var(--shadow-lg); overflow:hidden; }
        .install-body { padding:32px; }
        .form-row { margin-bottom:20px; }
        .form-row label { display:block; font-size:14px; font-weight:600; color:var(--gray-900); margin-bottom:6px; }
        .form-control { width:100%; padding:12px 16px; border:1.5px solid var(--gray-200); border-radius:10px; font-size:14px; font-family:var(--font); transition:border-color .2s; background:#fff; }
        .form-control:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(99,102,241,.1); }
        .form-holder { display:flex; align-items:center; gap:12px; }
        .form-holder .label-text { min-width:160px; font-weight:600; font-size:14px; color:var(--gray-900); }
        .badge { display:inline-block; padding:4px 12px; border-radius:100px; font-size:13px; font-weight:600; }
        .badge-success { background:#dcfce7; color:#16a34a; }
        .badge-error { background:#fef2f2; color:#dc2626; }
        .badge-warning { background:#fef9c3; color:#ca8a04; }
        .btn { display:inline-flex; align-items:center; gap:8px; padding:12px 28px; border-radius:10px; font-weight:600; font-size:14px; border:none; cursor:pointer; transition:all .2s; font-family:var(--font); }
        .btn-primary { background:var(--primary); color:#fff; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-secondary { background:#fff; color:var(--gray-700); border:1.5px solid var(--gray-200); }
        .btn-secondary:hover { border-color:var(--primary); color:var(--primary); }
        .wizard-actions { display:flex; justify-content:space-between; margin-top:24px; padding-top:20px; border-top:1px solid var(--gray-100); }
        .step-indicator { display:flex; justify-content:center; gap:8px; margin-bottom:24px; }
        .step-indicator .step { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; border:2px solid var(--gray-200); color:var(--gray-600); transition:all .2s; }
        .step-indicator .step.active { border-color:var(--primary); background:var(--primary); color:#fff; }
        .step-indicator .step.done { border-color:#16a34a; background:#dcfce7; color:#16a34a; }
        .image-holder { display:none; }
        @media(min-width:768px){ .form-row-inline { display:grid; grid-template-columns:1fr 1fr; gap:20px; } }
    </style>
</head>
<body>
    <div class="install-header">
        <h1><span>FLY</span> CASH</h1>
        <p>Admin Panel Installation</p>
    </div>

    <div class="install-card">
        <div class="install-body">
            <div class="step-indicator">
                <div class="step active" id="step1">1</div>
                <div class="step" id="step2">2</div>
                <div class="step" id="step3">3</div>
            </div>

            <form action="process.php" method="POST" id="insform">
                <div id="wizard">
                    <!-- SECTION 1: Requirements -->
                    <h4></h4>
                    <section>
                        <h3 style="font-size:18px;font-weight:700;margin-bottom:24px;color:var(--gray-900);">System Requirements</h3>
                        <div class="form-row">
                            <div class="form-holder">
                                <span class="label-text">PHP Version</span>
                                <?php
                                if (floatval(phpversion()) < 5.1) {
                                    echo '<span class="badge badge-error">' . floatval(phpversion()) . ' — Upgrade required</span>';
                                    $errors = 1;
                                } else {
                                    echo '<span class="badge badge-success">' . floatval(phpversion()) . ' — OK</span>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-holder">
                                <span class="label-text">PDO Enabled</span>
                                <?php
                                if (class_exists('PDO')) {
                                    echo '<span class="badge badge-success">Yes — OK</span>';
                                } else {
                                    echo '<span class="badge badge-error">No — Enable PDO</span>';
                                    $errors++;
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-holder">
                                <span class="label-text">Config File Writable</span>
                                <?php
                                if (is_writable($constants_file)) {
                                    echo '<span class="badge badge-success">Yes — OK</span>';
                                } else {
                                    echo '<span class="badge badge-error">Not writable</span>';
                                    $errors++;
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-row" style="margin-top:16px;">
                            <?php if ($errors > 0) { echo '<span class="badge badge-error">Fix the issues above to continue</span>'; } else { echo '<span class="badge badge-success">All good — continue to next step</span>'; $errors++; } ?>
                        </div>
                    </section>

                    <!-- SECTION 2: Database -->
                    <h4></h4>
                    <section>
                        <h3 style="font-size:18px;font-weight:700;margin-bottom:24px;color:var(--gray-900);">Database Configuration</h3>
                        <div class="form-row">
                            <label>Database Host</label>
                            <input type="text" name="host" placeholder="localhost" class="form-control required" value="localhost" required>
                        </div>
                        <div class="form-row">
                            <label>Database Name</label>
                            <input type="text" name="dbname" placeholder="flycash" class="form-control" value="flycash">
                        </div>
                        <div class="form-row">
                            <label>Database Username</label>
                            <input type="text" name="dbuser" placeholder="root" class="form-control" value="root">
                        </div>
                        <div class="form-row">
                            <label>Database Password</label>
                            <input type="text" name="dbpass" placeholder="Password" class="form-control">
                        </div>
                    </section>

                    <!-- SECTION 3: Admin -->
                    <h4></h4>
                    <section>
                        <h3 style="font-size:18px;font-weight:700;margin-bottom:24px;color:var(--gray-900);">Admin Account</h3>
                        <div class="form-row-inline">
                            <div class="form-row">
                                <label>First Name</label>
                                <input type="text" name="fname" placeholder="First Name" class="form-control">
                            </div>
                            <div class="form-row">
                                <label>Last Name</label>
                                <input type="text" name="lname" placeholder="Last Name" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Username</label>
                            <input type="text" name="uname" placeholder="Username" id="admin_username" class="form-control">
                        </div>
                        <div class="form-row">
                            <label>Password</label>
                            <input type="password" name="upass" placeholder="Password" class="form-control">
                        </div>
                    </section>
                    <input type="hidden" name="installsbt" value="summary">
                </div>
            </form>
        </div>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/jquery.steps.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
