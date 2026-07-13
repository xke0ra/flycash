<?php
include_once("../admin/core/init.inc.php");

$_SESSION["refererCode"] = isset($_REQUEST['refer']) ? $_REQUEST['refer'] : '';

$refererCode = htmlspecialchars($_SESSION["refererCode"], ENT_QUOTES, 'UTF-8');
$regUrl = "../dashboard/register.php" . ($refererCode ? "?refer=" . urlencode($refererCode) : "");

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Join FLY CASH - You've been invited! Start earning points, gift cards, and cash rewards today.">
    <meta name="theme-color" content="#6366f1">
    <title>You're Invited! | FLY CASH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #6366f1; --primary-dark: #4f46e5; --primary-light: #a5b4fc;
            --dark: #0f172a; --gray-50: #f8fafc; --gray-100: #f1f5f9;
            --gray-200: #e2e8f0; --gray-600: #475569; --gray-700: #334155;
            --gray-800: #1e293b; --gray-900: #0f172a;
            --font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        body { font-family: var(--font); background: var(--gray-50); color: var(--gray-800); line-height: 1.6; min-height: 100vh; display: flex; flex-direction: column; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 600px; margin: 0 auto; padding: 0 24px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 14px 32px; border-radius: 12px; font-weight: 600; font-size: 16px; transition: all .2s; cursor: pointer; border: none; }
        .btn-primary { background: var(--primary); color: #fff; box-shadow: 0 4px 14px rgba(99,102,241,.35); }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(99,102,241,.4); }
        .btn-outline { background: transparent; color: var(--gray-700); border: 1.5px solid var(--gray-200); }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .navbar { position: fixed; top: 0; left: 0; right: 0; z-index: 100; background: rgba(255,255,255,.8); backdrop-filter: blur(20px); border-bottom: 1px solid var(--gray-200); padding: 16px 0; }
        .navbar .container { display: flex; align-items: center; justify-content: space-between; max-width: 1100px; }
        .navbar .logo { font-size: 22px; font-weight: 800; color: var(--gray-900); }
        .navbar .logo span { color: var(--primary); }
        .nav-links { display: flex; align-items: center; gap: 24px; list-style: none; }
        .nav-links a { font-size: 14px; font-weight: 500; color: var(--gray-600); transition: color .2s; }
        .nav-links a:hover { color: var(--primary); }
        .hero { flex: 1; display: flex; align-items: center; justify-content: center; padding: 120px 0 60px; text-align: center; position: relative; overflow: hidden; }
        .hero::before { content: ''; position: absolute; top: -30%; left: 50%; transform: translateX(-50%); width: 500px; height: 500px; background: radial-gradient(circle, rgba(99,102,241,.06) 0%, transparent 70%); border-radius: 50%; pointer-events: none; }
        .hero h1 { font-size: 40px; font-weight: 800; color: var(--gray-900); letter-spacing: -.03em; margin-bottom: 12px; }
        .hero h1 strong { color: var(--primary); }
        .hero .subtitle { font-size: 18px; color: var(--gray-600); margin-bottom: 8px; }
        .hero .reward-badge { display: inline-flex; align-items: center; gap: 8px; background: #eef2ff; border: 1px solid var(--primary-light); border-radius: 100px; padding: 8px 20px; font-size: 14px; font-weight: 600; color: var(--primary); margin-bottom: 28px; }
        .hero .reward-badge svg { width: 20px; height: 20px; }
        .hero p { font-size: 16px; color: var(--gray-600); max-width: 480px; margin: 0 auto 32px; }
        .hero-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .features { padding: 0 0 60px; }
        .features .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; max-width: 600px; margin: 0 auto; padding: 0 24px; }
        .feature-card { background: #fff; border: 1px solid var(--gray-200); border-radius: 16px; padding: 24px 16px; text-align: center; }
        .feature-card .icon { width: 44px; height: 44px; border-radius: 12px; background: #eef2ff; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
        .feature-card .icon svg { width: 22px; height: 22px; color: var(--primary); }
        .feature-card h5 { font-size: 15px; font-weight: 700; color: var(--gray-900); margin-bottom: 4px; }
        .feature-card p { font-size: 13px; color: var(--gray-600); }
        footer { padding: 24px 0; border-top: 1px solid var(--gray-200); text-align: center; }
        footer p { font-size: 13px; color: var(--gray-500); }
        @media (max-width: 480px) {
            .features .grid { grid-template-columns: 1fr; }
            .hero h1 { font-size: 28px; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="../index.html" class="logo"><svg width="28" height="28" viewBox="0 0 32 32" fill="none" style="vertical-align:middle;margin-right:8px;"><defs><linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#6366f1"/><stop offset="100%" stop-color="#8b5cf6"/></linearGradient></defs><rect width="32" height="32" rx="8" fill="url(#g)"/><path d="M10 12L16 12Q20 12 20 16Q20 20 16 20L14 20L14 28L10 28Z" fill="#fff" stroke="#fff" stroke-width="1.5"/><path d="M14 17L18 17Q20 17 20 19Q20 21 18 21L14 17" fill="none" stroke="#fff" stroke-width="1.5"/></svg><span>FLY</span> CASH</a>
            <ul class="nav-links">
                <li><a href="../index.html">Home</a></li>
                <li><a href="../dashboard/login.php">Sign In</a></li>
            </ul>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <?php if ($refererCode): ?>
            <div class="reward-badge">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                You've been referred! Get 100 bonus points when you sign up
            </div>
            <?php endif; ?>
            <h1>Join <strong>FLY CASH</strong></h1>
            <p class="subtitle">Start earning points, gift cards, and cash rewards</p>
            <p>Complete offers, watch videos, refer friends, and exchange your points for PayPal, Amazon, Google Play, and more.</p>
            <div class="hero-actions">
                <a href="<?php echo $regUrl; ?>" class="btn btn-primary">Claim Your Bonus — Sign Up Free</a>
                <a href="../index.html" class="btn btn-outline">Learn More</a>
            </div>
        </div>
    </section>

    <div class="features">
        <div class="grid">
            <div class="feature-card">
                <div class="icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622"/></svg></div>
                <h5>Earn Points</h5>
                <p>Complete offers, surveys, and tasks</p>
            </div>
            <div class="feature-card">
                <div class="icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg></div>
                <h5>Refer Friends</h5>
                <p>Earn rewards for every referral</p>
            </div>
            <div class="feature-card">
                <div class="icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg></div>
                <h5>Cash Out</h5>
                <p>PayPal, Amazon, gift cards &amp; more</p>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2026 FLY CASH. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>