<?php
// scripts/db.php
// Database CLI for Tandem. Runs schema.sql / seed.sql against the database
// configured in .env (DATABASE_URL for Neon).
//
// Usage:
//   php scripts/db.php status    Test the connection and list tables with row counts
//   php scripts/db.php migrate   Drop & recreate all tables from schema.sql
//   php scripts/db.php seed      Wipe all data and load seed.sql
//   php scripts/db.php fresh     migrate + seed

if (PHP_SAPI !== 'cli') {
    exit("This script can only be run from the command line.\n");
}

require_once dirname(__DIR__) . '/includes/Database.php';

function runSqlFile(PDO $pdo, string $file): void {
    $path = dirname(__DIR__) . '/' . $file;
    echo "→ Running {$file} ... ";
    $pdo->exec(file_get_contents($path));
    echo "done\n";
}

function printStatus(PDO $pdo): void {
    $version = $pdo->query("SHOW server_version")->fetchColumn();
    echo "✓ Connected to PostgreSQL {$version}\n";

    $tables = $pdo->query("
        SELECT table_name FROM information_schema.tables
        WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
        ORDER BY table_name
    ")->fetchAll(PDO::FETCH_COLUMN);

    if (!$tables) {
        echo "  (no tables yet — run: php scripts/db.php fresh)\n";
        return;
    }

    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM \"{$table}\"")->fetchColumn();
        printf("  %-18s %5d rows\n", $table, $count);
    }
}

$command = $argv[1] ?? 'status';

try {
    $pdo = Database::getConnection();

    match ($command) {
        'status'  => printStatus($pdo),
        'migrate' => runSqlFile($pdo, 'schema.sql'),
        'seed'    => runSqlFile($pdo, 'seed.sql'),
        'fresh'   => (function () use ($pdo) {
            runSqlFile($pdo, 'schema.sql');
            runSqlFile($pdo, 'seed.sql');
            printStatus($pdo);
        })(),
        default   => exit("Unknown command '{$command}'. Use: status | migrate | seed | fresh\n"),
    };
} catch (PDOException $e) {
    fwrite(STDERR, "✗ Database error: " . $e->getMessage() . "\n");
    exit(1);
}
