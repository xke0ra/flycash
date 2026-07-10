<?php
/**
 * CSS Class Usage Auditor — FlyCash v4
 */
$cssFiles = [
    'style.bundle.css'   => __DIR__ . '/../dashboard/assets/css/style.bundle.css',
    'plugins.bundle.css' => __DIR__ . '/../dashboard/assets/plugins/global/plugins.bundle.css',
];
$phpDir = __DIR__ . '/../dashboard';
$jsDir  = $phpDir . '/assets/js';

$structuralPrefixes = [
    'container', 'row', 'col-', 'd-', 'flex', 'justify-', 'align-',
    'm-', 'p-', 'w-', 'h-', 'text-', 'bg-',
];

// Step 1: Extract classes from CSS
function extractClasses(string $path): array {
    $content = file_get_contents($path);
    $classes = [];
    preg_match_all('/\.([a-zA-Z0-9_\-]+)\s*\{/', $content, $m);
    foreach ($m[1] as $cls) {
        if (preg_match('/^[a-zA-Z0-9_\-]+$/', $cls)) {
            $classes[$cls] = true;
        }
    }
    return $classes;
}

// Step 2: Literal class usage in PHP files
function literalClasses(string $dir): array {
    $used = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if ($f->getExtension() !== 'php') continue;
        $c = file_get_contents($f->getPathname());
        preg_match_all('/class=(["\'])([^"\']+?)\1/s', $c, $m);
        foreach ($m[2] as $list) {
            foreach (preg_split('/\s+/', trim($list)) as $p) {
                if ($p !== '') $used[$p] = 'literal';
            }
        }
        preg_match_all('/class=(["\'])([^"\']*<\?php[^"\']*?)\1/s', $c, $dm);
        foreach ($dm[2] as $dyn) {
            preg_match_all('/["\']([a-zA-Z0-9_\-]+)["\']/', $dyn, $lm);
            foreach ($lm[1] as $l) {
                if ($l !== '') $used[$l] = 'dynamic';
            }
        }
    }
    return $used;
}

// Step 3: Dynamic prefixes in PHP
function dynamicPrefixes(string $dir): array {
    $prefixes = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if ($f->getExtension() !== 'php') continue;
        $c = file_get_contents($f->getPathname());
        preg_match_all('/([a-zA-Z][a-zA-Z0-9_\-]*)\s*\.\s*["\']?\s*\.\s*(?:\$|echo)/', $c, $m1);
        foreach ($m1[1] as $p) $prefixes[$p] = true;
        preg_match_all('/["\']([a-zA-Z][a-zA-Z0-9_\-]*)["\']\s*\.\s*(?:\$|echo)/', $c, $m2);
        foreach ($m2[1] as $p) $prefixes[$p] = true;
    }
    return array_keys($prefixes);
}

// Step 4: JS class usage
function jsClasses(string $dir): array {
    $used = [];
    if (!is_dir($dir)) return $used;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if (!in_array($f->getExtension(), ['js', 'mjs'])) continue;
        $c = file_get_contents($f->getPathname());
        preg_match_all('/classList\.(?:add|toggle|contains|remove)\s*\(\s*["\']([^"\']+)["\']/', $c, $m1);
        foreach ($m1[1] as $str) {
            foreach (preg_split('/\s+/', trim($str)) as $p) {
                if ($p !== '') $used[$p] = 'js';
            }
        }
        preg_match_all('/className\s*=\s*["\']([^"\']+?)["\']/', $c, $m2);
        foreach ($m2[1] as $str) {
            foreach (preg_split('/\s+/', trim($str)) as $p) {
                if ($p !== '') $used[$p] = 'js';
            }
        }
        preg_match_all('/\b(?:addClass|toggleClass|removeClass)\s*\(\s*["\']([^"\']+)["\']/', $c, $m3);
        foreach ($m3[1] as $str) {
            foreach (preg_split('/\s+/', trim($str)) as $p) {
                if ($p !== '') $used[$p] = 'js';
            }
        }
    }
    return $used;
}

// Step 5: Check structural prefix
function isStructural(string $cls, array $prefixes): bool {
    foreach ($prefixes as $p) {
        if ($cls === $p || strpos($cls, $p) === 0) return true;
    }
    return false;
}

// Step 6: Match dynamic prefix to CSS classes
function resolvePrefix(string $prefix, array $cssClasses): array {
    $r = [];
    foreach ($cssClasses as $cls => $_) {
        if ($cls !== $prefix && strpos($cls, $prefix) === 0) {
            $rest = substr($cls, strlen($prefix));
            if (preg_match('/^[^a-zA-Z0-9]/', $rest)) {
                $r[] = $cls;
            }
        }
    }
    return $r;
}

// ═══════════════════════ MAIN ═══════════════════════

$allCss = [];
$fileCounts = [];
foreach ($cssFiles as $label => $path) {
    if (!file_exists($path)) {
        echo "MISSING: $label\n";
        continue;
    }
    $classes = extractClasses($path);
    $allCss = array_merge($allCss, $classes);
    $fileCounts[$label] = count($classes);
    echo "  $label: " . count($classes) . " classes\n";
}

$total = count($allCss);
echo "\nTotal unique CSS classes: $total\n\n";

$litUsed = literalClasses($phpDir);
echo "PHP literal/dynamic: " . count($litUsed) . " references\n";

$jsUsed = jsClasses($jsDir);
echo "JS classList/className: " . count($jsUsed) . " references\n";

$prefixes = dynamicPrefixes($phpDir);
echo "Dynamic prefixes: " . count($prefixes) . "\n";
if ($prefixes) echo '  -> ' . implode(', ', $prefixes) . "\n";

// Used map
$usedInfo = [];
foreach ($allCss as $cls => $_) {
    if (isset($litUsed[$cls])) { $usedInfo[$cls] = $litUsed[$cls]; continue; }
    if (isset($jsUsed[$cls])) { $usedInfo[$cls] = 'js'; continue; }
    if (isStructural($cls, $structuralPrefixes)) { $usedInfo[$cls] = 'structural'; continue; }
}

// Resolve dynamic prefixes
$dynResolved = 0;
foreach ($prefixes as $pfx) {
    foreach (resolvePrefix($pfx, $allCss) as $m) {
        if (!isset($usedInfo[$m])) {
            $usedInfo[$m] = 'prefix-dynamic';
            $dynResolved++;
        }
    }
}
echo "Dynamic-prefix resolved: $dynResolved\n\n";

// Categories
$cats = ['literal' => 0, 'dynamic' => 0, 'js' => 0, 'structural' => 0, 'prefix-dynamic' => 0];
foreach ($usedInfo as $m) $cats[$m]++;

$usedCount = count($usedInfo);
$unusedCount = $total - $usedCount;

echo "========================================\n";
echo "  RESULTS\n";
echo "========================================\n";
echo "  Total      : $total\n";
echo "  Used       : $usedCount (" . round($usedCount/$total*100, 1) . "%)\n";
echo "  Unused     : $unusedCount (" . round($unusedCount/$total*100, 1) . "%)\n\n";

echo "  By detection:\n";
foreach ($cats as $method => $cnt) {
    echo "    $method: $cnt (" . round($cnt/$total*100, 1) . "%)\n";
}

echo "\n  Unused classes:\n";
$unused = [];
foreach ($allCss as $cls => $_) {
    if (!isset($usedInfo[$cls])) $unused[] = $cls;
}
sort($unused);
foreach ($unused as $cls) echo "    $cls\n";

// Per-file
echo "\n========================================\n";
echo "  PER FILE\n";
echo "========================================\n";
foreach ($cssFiles as $label => $path) {
    if (!file_exists($path)) { echo "  $label: MISSING\n"; continue; }
    $classes = extractClasses($path);
    $fu = 0;
    foreach ($classes as $cls => $_) {
        if (isset($usedInfo[$cls])) $fu++;
    }
    $ft = count($classes);
    echo "  $label: $ft total, $fu used, " . ($ft - $fu) . " unused (" . round(($ft-$fu)/$ft*100, 1) . "%)\n";
}

echo "\n  Old methodology baseline: ~2.6-3.3% unused\n";
echo "  New methodology: " . round($unusedCount/$total*100, 1) . "% unused\n";
