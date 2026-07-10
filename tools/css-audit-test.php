<?php
/**
 * CSS Audit Test Page — loads trimmed bundles for visual comparison.
 *
 * ═════════════════════════════════════════════════════════════════
 * USAGE: Open this page in your browser while logged in to the
 * dashboard. Each iframe below renders the corresponding page
 * WITH trimmed CSS bundles. Compare against the live site (open
 * in another tab) to verify no styles are broken.
 * ═════════════════════════════════════════════════════════════════
 *
 * NOTE: This tool references dashboard pages via relative path.
 * It has NOT been deployed to the live site — the *.trimmed.css
 * files exist only as preparatory work for a future frontend
 * optimization phase. Do NOT activate them in production until
 * a thorough visual audit has been completed.
 */

$requested = $_GET['page'] ?? '';
$allowed   = ['index', 'login', 'redeem', 'profile', 'transactions', 'refer', 'change-password'];

// ── Iframe rendering mode: render a page with trimmed CSS ───────
if ($requested !== '') {
    $pageMap = [
        'index'          => 'index.php',
        'login'          => 'login.php',
        'redeem'         => 'redeem.php',
        'profile'        => 'profile.php',
        'transactions'   => 'transactions.php',
        'refer'          => 'refer.php',
        'change-password'=> 'change-password.php',
    ];
    $pageFile = $pageMap[$requested] ?? 'index.php';
    $pagePath = __DIR__ . '/../dashboard/' . $pageFile;

    if (!file_exists($pagePath)) {
        echo "Page not found.";
        exit;
    }

    ob_start(function(string $buffer): string {
        $buffer = str_replace(
            'plugins/global/plugins.bundle.css',
            'plugins/global/plugins.bundle.trimmed.css',
            $buffer
        );
        $buffer = str_replace(
            'css/style.bundle.css',
            'css/style.bundle.trimmed.css',
            $buffer
        );
        return $buffer;
    });

    require $pagePath;
    ob_end_flush();
    exit;
}

// ── Main audit dashboard ────────────────────────────────────────
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CSS Audit — Trimmed Bundle Test</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Inter, -apple-system, BlinkMacSystemFont, sans-serif; background: #f1f5f9; color: #0f172a; padding: 0; }
.header { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; padding: 24px 32px; }
.header h1 { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
.header p { font-size: 14px; opacity: 0.85; }
.info-bar { background: #fff; padding: 16px 32px; border-bottom: 1px solid #e2e8f0; font-size: 13px; display: flex; gap: 24px; flex-wrap: wrap; align-items: center; }
.info-bar strong { color: #6366f1; }
.badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-success { background: #dcfce7; color: #166534; }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(500px, 1fr)); gap: 20px; padding: 24px 32px; }
.frame-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.frame-card .label { padding: 12px 16px; font-size: 13px; font-weight: 600; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
.frame-card .label a { font-weight: 400; font-size: 12px; color: #6366f1; text-decoration: none; }
.frame-card .label a:hover { text-decoration: underline; }
iframe { width: 100%; height: 600px; border: none; }
.notice { background: #fff; margin: 0 32px 24px; padding: 16px 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); font-size: 14px; line-height: 1.6; }
.notice strong.danger { color: #dc2626; }
code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 13px; }
.stats { display: flex; gap: 12px; flex-wrap: wrap; margin: 0 32px 24px; }
.stat-card { background: #fff; padding: 16px 20px; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.06); flex: 1; min-width: 140px; }
.stat-card .num { font-size: 24px; font-weight: 700; color: #6366f1; }
.stat-card .lbl { font-size: 12px; color: #64748b; margin-top: 2px; }
.btn { display: inline-block; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; border: none; }
.btn-primary { background: #6366f1; color: #fff; }
.btn-primary:hover { background: #4f46e5; }
</style>
</head>
<body>

<div class="header">
    <h1>CSS Bundle Audit — Visual Comparison</h1>
    <p>Each iframe below loads the page with <strong>trimmed bundles</strong> (unused CSS classes removed). Compare against the live site.</p>
</div>

<div class="info-bar">
    <div><span class="badge badge-warning">TEST MODE</span></div>
    <div><strong>Trimmed CSS</strong> active in iframes</div>
    <div>Original files <strong>untouched</strong></div>
    <div><a href="../dashboard/index.php" target="_blank" style="color:#6366f1;font-weight:600;">Open live site &rarr;</a></div>
</div>

<div class="stats">
    <div class="stat-card"><div class="num">4,778</div><div class="lbl">Unused classes removed (89.8%)</div></div>
    <div class="stat-card"><div class="num">540</div><div class="lbl">Classes kept (10.2%)</div></div>
    <div class="stat-card"><div class="num">88%</div><div class="lbl">style.bundle.css reduction</div></div>
    <div class="stat-card"><div class="num">47.7%</div><div class="lbl">plugins.bundle.css reduction</div></div>
</div>

<div class="notice">
    <strong class="danger">&#9888; Important:</strong> You must be <strong>logged in</strong> to the dashboard for authenticated pages (Dashboard, Redeem, Profile, Transactions, Refer) to render properly in the iframes. Login page is public and should always render.
    <br><br>
    <strong>Detection methods:</strong>
    Literal class matching (78 classes) &bull;
    Dynamic prefix resolution: <code>badge-</code> (9 classes) &bull;
    JS classList/className (1 class) &bull;
    Structural/Bootstrap utilities excluded (452 classes &mdash; <code>container, row, col-, d-, flex, justify-, align-, m-, p-, w-, h-, text-, bg-</code>)
    <br><br>
    <strong>Trimmed files created:</strong>
    <code>dashboard/assets/css/style.bundle.trimmed.css</code> (155 KB, down from 1.29 MB) &bull;
    <code>dashboard/assets/plugins/global/plugins.bundle.trimmed.css</code> (219 KB, down from 419 KB)
    <br>
    <strong>Not yet deployed to live site.</strong> To deploy, rename the <code>*.trimmed.css</code> files to replace the originals, or update <code>dashboard/includes/global_header_scripts.php</code> paths.
</div>

<div class="grid">
<?php
$pages = [
    'Login'        => ['login', 'Public — always visible'],
    'Dashboard'    => ['index', 'Requires login'],
    'Redeem'       => ['redeem', 'Requires login'],
    'Profile'      => ['profile', 'Requires login'],
    'Transactions' => ['transactions', 'Requires login'],
    'Refer & Earn' => ['refer', 'Requires login'],
];
foreach ($pages as $name => [$url, $note]):
?>
    <div class="frame-card">
        <div class="label">
            <span><?php echo htmlspecialchars($name); ?></span>
            <span style="font-weight:400;font-size:11px;color:#64748b;"><?php echo htmlspecialchars($note); ?></span>
        </div>
        <iframe src="css-audit-test.php?page=<?php echo urlencode($url); ?>" loading="lazy"></iframe>
    </div>
<?php endforeach; ?>
</div>

</body>
</html>
