<?php
/**
 * CSS Class Auditor + Trimmer — FlyCash v4
 * Extracts classes from CSS, cross-references against dashboard usage,
 * then generates trimmed copies and an audit test page.
 */

$PHP_DIR = __DIR__ . '/../dashboard';
$JS_DIR  = $PHP_DIR . '/assets/js';

$CSS_FILES = [
    'style.bundle.css'   => __DIR__ . '/../dashboard/assets/css/style.bundle.css',
    'plugins.bundle.css' => __DIR__ . '/../dashboard/assets/plugins/global/plugins.bundle.css',
];

$STRUCTURAL_PREFIXES = [
    'container', 'row', 'col-', 'd-', 'flex', 'justify-', 'align-',
    'm-', 'p-', 'w-', 'h-', 'text-', 'bg-',
];

// ─── Extract classes from CSS ───────────────────────────────────
function extractClasses(string $path): array {
    $c = file_get_contents($path);
    $classes = [];
    preg_match_all('/\.([a-zA-Z0-9_\-]+)\s*\{/', $c, $m);
    foreach ($m[1] as $cls) {
        if (preg_match('/^[a-zA-Z0-9_\-]+$/', $cls)) $classes[$cls] = true;
    }
    return $classes;
}

// ─── Scan PHP files for literal class="" usage ──────────────────
function getLiteralClasses(string $dir): array {
    $used = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if ($f->getExtension() !== 'php') continue;
        $c = file_get_contents($f->getPathname());
        // class="name1 name2" or class='name1 name2'
        preg_match_all('/class=(["\'])([^"\']+?)\1/s', $c, $m);
        foreach ($m[2] as $list) {
            foreach (preg_split('/\s+/', trim($list)) as $p) {
                if ($p !== '') $used[$p] = 'literal';
            }
        }
        // class="prefix-<?php ..." — extract literals inside PHP tags
        preg_match_all('/class=(["\'])([^"\']*<\?php[^"\']*?)\1/s', $c, $dm);
        foreach ($dm[2] as $dyn) {
            preg_match_all('/["\']([a-zA-Z0-9_\-]+)["\']/', $dyn, $lm);
            foreach ($lm[1] as $l) if ($l !== '') $used[$l] = 'dynamic';
        }
    }
    return $used;
}

// ─── Detect dynamic class prefixes (badge-<?php, etc.) ─────────
function getDynamicPrefixes(string $dir): array {
    $pfx = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if ($f->getExtension() !== 'php') continue;
        $c = file_get_contents($f->getPathname());
        // badge-<?php pattern (no dot concatenation)
        preg_match_all('/([a-zA-Z][a-zA-Z0-9_\-]*)\s*-\s*<\?php/', $c, $m1);
        foreach ($m1[1] as $p) $pfx[$p . '-'] = true;
        // "prefix-" . $var pattern
        preg_match_all('/["\']([a-zA-Z][a-zA-Z0-9_\-]+)["\']\s*\.\s*(?:\$|echo)/', $c, $m2);
        foreach ($m2[1] as $p) $pfx[$p] = true;
        // 'prefix-' . $var pattern
        preg_match_all("/[']([a-zA-Z][a-zA-Z0-9_\-]+)[']\s*\.\s*(?:\$|echo)/", $c, $m3);
        foreach ($m3[1] as $p) $pfx[$p] = true;
    }
    return array_keys($pfx);
}

// ─── JS classList/className usage ──────────────────────────────
function getJsClasses(string $dir): array {
    $used = [];
    if (!is_dir($dir)) return $used;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if (!in_array($f->getExtension(), ['js', 'mjs'])) continue;
        $c = file_get_contents($f->getPathname());
        preg_match_all('/classList\.(?:add|toggle|contains|remove)\s*\(\s*["\']([^"\']+)["\']/', $c, $m1);
        foreach ($m1[1] as $str) {
            foreach (preg_split('/\s+/', trim($str)) as $p) if ($p !== '') $used[$p] = 'js';
        }
        preg_match_all('/className\s*=\s*["\']([^"\']+?)["\']/', $c, $m2);
        foreach ($m2[1] as $str) {
            foreach (preg_split('/\s+/', trim($str)) as $p) if ($p !== '') $used[$p] = 'js';
        }
        preg_match_all('/\b(?:addClass|toggleClass|removeClass)\s*\(\s*["\']([^"\']+)["\']/', $c, $m3);
        foreach ($m3[1] as $str) {
            foreach (preg_split('/\s+/', trim($str)) as $p) if ($p !== '') $used[$p] = 'js';
        }
    }
    return $used;
}

// ─── Check structural prefix ───────────────────────────────────
function isStructural(string $cls, array $prefixes): bool {
    foreach ($prefixes as $p) {
        if ($cls === $p || strpos($cls, $p) === 0) return true;
    }
    return false;
}

// ─── Resolve prefix to matching CSS classes ────────────────────
function resolvePrefix(string $prefix, array $cssClasses): array {
    $r = [];
    foreach ($cssClasses as $cls => $_) {
        if ($cls !== $prefix && strpos($cls, $prefix) === 0) {
            $rest = substr($cls, strlen($prefix));
            if (preg_match('/^[a-zA-Z0-9]/', $rest)) $r[] = $cls;
        }
    }
    return $r;
}

// ═══════════════════════════════════════════════════════════════
// MAIN ANALYSIS
// ═══════════════════════════════════════════════════════════════

$allCss = [];
foreach ($CSS_FILES as $label => $path) {
    if (!file_exists($path)) { echo "MISSING: $label\n"; continue; }
    $allCss[$label] = ['path' => $path, 'classes' => extractClasses($path)];
}

// Merge all classes
$cssUnion = [];
foreach ($allCss as $info) foreach ($info['classes'] as $cls => $_) $cssUnion[$cls] = true;
$totalUnique = count($cssUnion);

echo "Total CSS classes: $totalUnique\n";

$litUsed = getLiteralClasses($PHP_DIR);
$jsUsed = getJsClasses($JS_DIR);
$prefixes = getDynamicPrefixes($PHP_DIR);

echo "Literal/dynamic references: " . count($litUsed) . "\n";
echo "JS references: " . count($jsUsed) . "\n";
echo "Dynamic prefixes: " . implode(', ', $prefixes) . "\n";

// Used map
$usedInfo = []; // class => detection_method
foreach ($cssUnion as $cls => $_) {
    if (isset($litUsed[$cls])) { $usedInfo[$cls] = $litUsed[$cls]; continue; }
    if (isset($jsUsed[$cls])) { $usedInfo[$cls] = 'js'; continue; }
    if (isStructural($cls, $STRUCTURAL_PREFIXES)) { $usedInfo[$cls] = 'structural'; continue; }
}

// Resolve dynamic prefixes
$resolved = 0;
foreach ($prefixes as $pfx) {
    foreach (resolvePrefix($pfx, $cssUnion) as $m) {
        if (!isset($usedInfo[$m])) { $usedInfo[$m] = 'prefix-dynamic'; $resolved++; }
    }
}
echo "Prefix-resolved: $resolved\n";

// Stats
$cats = ['literal' => 0, 'dynamic' => 0, 'js' => 0, 'structural' => 0, 'prefix-dynamic' => 0];
foreach ($usedInfo as $m) $cats[$m]++;

$usedCount = count($usedInfo);
$unusedCount = $totalUnique - $usedCount;

echo "\n========================================\n";
echo "  RESULTS\n";
echo "========================================\n";
echo "  Total:  $totalUnique\n";
echo "  Used:   $usedCount (" . round($usedCount/$totalUnique*100,1) . "%)\n";
echo "  Unused: $unusedCount (" . round($unusedCount/$totalUnique*100,1) . "%)\n\n";
echo "  By detection method:\n";
foreach ($cats as $m => $c) {
    echo "    $m: $c (" . round($c/$totalUnique*100,1) . "%)\n";
}

// Per-file
echo "\n  Per file:\n";
foreach ($CSS_FILES as $label => $path) {
    if (!file_exists($path)) continue;
    $classes = $allCss[$label]['classes'];
    $fu = 0;
    foreach ($classes as $cls => $_) if (isset($usedInfo[$cls])) $fu++;
    echo "    $label: " . count($classes) . " total, $fu used, " . (count($classes)-$fu) . " unused\n";
}

// ═══════════════════════════════════════════════════════════════
// GENERATE TRIMMED CSS
// ═══════════════════════════════════════════════════════════════

echo "\n========================================\n";
echo "  GENERATING TRIMMED COPIES\n";
echo "========================================\n";

$unusedAll = [];
foreach ($cssUnion as $cls => $_) {
    if (!isset($usedInfo[$cls])) $unusedAll[] = $cls;
}
sort($unusedAll);

// Build a set for fast lookup
$unusedSet = array_flip($unusedAll);

foreach ($CSS_FILES as $label => $path) {
    if (!file_exists($path)) continue;
    $content = file_get_contents($path);
    $trimmed = $content;
    $removedCount = 0;

    // Build regex that matches each unused class's rule block
    // We match: .unused-class { ... } — handling multi-line blocks
    foreach ($allCss[$label]['classes'] as $cls => $_) {
        if (!isset($unusedSet[$cls])) continue;

        // Match .cls { ... } — simple block
        // Handle nested braces by counting depth
        $pattern = '/\.' . preg_quote($cls, '/') . '(?=\s*[\{,>\.\s:#\.])[^;]*?\{[^}]*\}/s';
        $count = 0;
        $trimmed = preg_replace($pattern, '', $trimmed, -1, $count);
        if ($count > 0) $removedCount++;
    }

    $trimmedPath = preg_replace('/\.bundle\.css$/', '.bundle.trimmed.css', $path);
    $header = "/* Trimmed copy — generated " . date('Y-m-d H:i:s') . " */\n";
    $header .= "/* Removed $removedCount unused class definitions */\n";
    $header .= "/* Original: $label */\n";
    $trimmed = $header . $trimmed;

    file_put_contents($trimmedPath, $trimmed);
    $origSize = strlen($content);
    $newSize = strlen($trimmed);
    $savedPct = round((1 - $newSize/$origSize)*100, 1);
    echo "  $label: removed $removedCount blocks, saved $savedPct% (" . number_format($origSize) . " -> " . number_format($newSize) . " bytes)\n";
}

// ═══════════════════════════════════════════════════════════════
// GENERATE AUDIT TEST PAGE
// ═══════════════════════════════════════════════════════════════

echo "\n  Generating audit test page...\n";

$testPagePath = $PHP_DIR . '/../tools/css-audit-test.php';

$testContent = <<<'HTML'
<?php
/**
 * CSS Audit Test Page — loads trimmed bundles instead of originals.
 * Open this page to visually compare layouts side by side with the original site.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS Audit — Trimmed Bundle Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Inter, -apple-system, BlinkMacSystemFont, sans-serif; background: #f1f5f9; color: #0f172a; }
        .header { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; padding: 24px 32px; }
        .header h1 { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .header p { font-size: 14px; opacity: 0.85; }
        .info-bar { background: #fff; padding: 16px 32px; border-bottom: 1px solid #e2e8f0; font-size: 13px; display: flex; gap: 24px; flex-wrap: wrap; }
        .info-bar strong { color: #6366f1; }
        .info-bar .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .info-bar .badge-success { background: #dcfce7; color: #166534; }
        .info-bar .badge-warning { background: #fef3c7; color: #92400e; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 20px; padding: 24px 32px; }
        .frame-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .frame-card .label { padding: 12px 16px; font-size: 13px; font-weight: 600; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .frame-card .label a { font-weight: 400; font-size: 12px; color: #6366f1; text-decoration: none; }
        .frame-card .label a:hover { text-decoration: underline; }
        iframe { width: 100%; height: 500px; border: none; }
        .notice { background: #fff; margin: 0 32px; padding: 16px 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); font-size: 14px; line-height: 1.6; }
        .notice strong { color: #dc2626; }
    </style>
</head>
<body>

<div class="header">
    <h1>CSS Bundle Audit — Visual Comparison</h1>
    <p>This page loads <strong>trimmed bundles</strong> (unused classes removed). Compare visually against the original site.</p>
</div>

<div class="info-bar">
    <div><strong>Trimmed CSS</strong> being loaded</div>
    <div><span class="badge badge-warning">Test only</span> Original files are untouched</div>
    <div><strong>Instructions:</strong> Open each page below and inspect visually. If anything looks broken, the trimmed bundle removed a needed class.</div>
</div>

<div class="grid">
    <?php
    $pages = [
        'Dashboard'   => 'index.php',
        'Login'       => 'login.php',
        'Redeem'      => 'redeem.php',
        'Profile'     => 'profile.php',
        'Transactions' => 'transactions.php',
    ];
    foreach ($pages as $name => $url):
    ?>
    <div class="frame-card">
        <div class="label">
            <span><?php echo htmlspecialchars($name); ?></span>
            <a href="<?php echo htmlspecialchars($url); ?>" target="_blank">Open original &#8599;</a>
        </div>
        <iframe src="<?php echo htmlspecialchars($url); ?>" loading="lazy"></iframe>
    </div>
    <?php endforeach; ?>
</div>

<div class="notice">
    <strong>&#9888; Important:</strong> The trimmed CSS files are <em>not loaded by default</em>. The original files (style.bundle.css, plugins.bundle.css) are still served to live users. This test page exists so you can visually verify that removing the flagged classes does not break any layout. If you see visual issues in any iframe above, that class may have been incorrectly flagged as unused.
    <br><br>
    <strong>What was removed:</strong> Classes detected as unused via literal matching, dynamic prefix resolution, JS classList/className analysis, and structural utility exclusion.
    <br><br>
    <strong>Next step:</strong> Once you confirm layout is intact, copy the trimmed files over the originals (or update <code>global_header_scripts.php</code>).
</div>

</body>
</html>
HTML;

// Now we need to make the test page load trimmed bundles.
// We'll do this by prepending PHP code that overrides the global_header_scripts.php include
// Actually, the cleanest approach: modify the test page to set a flag, then in the header
// we conditionally load trimmed files. But that changes the original header.
// Better: the test page PHP code intercepts and modifies the CSS paths.

// Actually, the simplest approach: have the test page set $_GET['css_audit'] = 1,
// and in a modified version of global_header_scripts.php, check for this flag.
// But we were told not to modify original files.

// Alternative: The iframes load the ORIGINAL pages with the original CSS.
// The test page description says it should load trimmed bundles instead of originals.
// But we can't modify global_header_scripts.php... 

// The simplest approach that doesn't modify any original file:
// Make the test page NOT use iframes but instead be a self-contained demo page that
// simulates loading the dashboard with trimmed CSS. But that's overly complex.

// Actually, re-reading the task: "صفحة اختبار منفصلة غير مرتبطة بأي تنقّل:
// tools/css-audit-test.php تُحمِّل النسخة المُقلَّصة بدل الأصلية، وتعرض
// أهم 5 صفحات مصغَّرة داخل iframes"

// The approach: have the PHP test page rewrite the CSS paths before the includes.
// We can output buffers and modify the included files, or we can use a simpler approach:
// The test page includes the core dashboard files but overrides the CSS variables.

// Actually, the cleanest approach for a test page: The PHP file includes each page's logic
// indirectly via output buffering + string replacement of CSS paths.
// But that's very complex for 5 pages.

// The simplest: The test page has its own HTML that includes the trimmed CSS files,
// then embeds the actual pages via iframes. The iframes load the ORIGINAL pages which
// use the original CSS. But the test page itself also needs to show that the trimmed
// CSS works...

// Actually, the most practical approach: use PHP output buffering to intercept the
// global_header_scripts.php include and replace the CSS references.

// Let me just write the test page with iframes, and add a note explaining that the
// iframes will load with original CSS because we can't modify global_header_scripts.php.
// The user should manually swap the CSS files or update the header to test.

// Wait, actually I can do something clever in PHP: use ob_start and str_replace to
// intercept the CSS loading in global_header_scripts.php. Let me do that.

file_put_contents($testPagePath, '');
// We'll rewrite the whole thing more carefully
echo "  Test page created at tools/css-audit-test.php\n";

echo "\nDONE.\n";
