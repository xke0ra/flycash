<?php
$handlers = [
    ['amount' => '2.50', 'expected' => 750],
    ['amount' => '1.00', 'expected' => 300],
    ['amount' => '0', 'expected' => 0],
    ['amount' => '0.50', 'expected' => 150],
    ['amount' => '10.00', 'expected' => 3000],
];
$allPass = true;
foreach ($handlers as $h) {
    $usdAmount = (float)$h['amount'];
    $result = (int)($usdAmount * 300);
    $status = $result === $h['expected'] ? 'PASS' : 'FAIL';
    if ($status === 'FAIL') $allPass = false;
    echo "$status: amount={$h['amount']} -> $result (expected {$h['expected']})\n";
}
echo "\n" . ($allPass ? 'ALL PASS' : 'SOME FAILED') . "\n";
