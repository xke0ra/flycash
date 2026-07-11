<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$dsn = sprintf('mysql:host=%s;dbname=%s', $_ENV['DB_HOST'] ?? 'localhost', $_ENV['DB_NAME'] ?? 'pocket_db');
$pdo = new PDO($dsn, $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASS'] ?? '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function execSqlFile(PDO $pdo, string $path): void
{
    $sql = file_get_contents($path);
    $buffer = '';
    foreach (explode("\n", $sql) as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#') || str_starts_with($trimmed, '=')) {
            continue;
        }
        if (preg_match('/^CREATE DATABASE/i', $trimmed) || preg_match('/^USE /i', $trimmed)) {
            continue;
        }
        if (preg_match('/^\/\*!/', $trimmed)) {
            continue;
        }
        $clean = preg_replace('/\s*--.*$/', '', $line);
        $buffer .= $clean;
        if (str_ends_with(trim($clean), ';')) {
            $stmt = trim($buffer);
            if ($stmt !== '') {
                $pdo->exec($stmt);
            }
            $buffer = '';
        }
    }
}

$schemaPath = __DIR__ . '/../docs/schema_reference.sql';
$legacyPath = __DIR__ . '/../install/pocket_db.sql';

if (file_exists($schemaPath)) {
    $source = $schemaPath;
} elseif (file_exists($legacyPath)) {
    $source = $legacyPath;
} else {
    fwrite(STDERR, "No schema file found (tried: $schemaPath, $legacyPath)\n");
    exit(1);
}

echo "Applying schema from $source...\n";
execSqlFile($pdo, $source);
echo "Schema applied successfully.\n";

echo "Running phinx migrations...\n";
passthru('php ' . __DIR__ . '/../vendor/bin/phinx migrate -e development', $exitCode);

if ($exitCode === 0) {
    echo "Database initialized successfully.\n";
} else {
    fwrite(STDERR, "Phinx migrations failed (exit code: $exitCode).\n");
    exit($exitCode);
}
