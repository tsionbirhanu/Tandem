<?php
// config/database.php
// Database credentials and configuration parameters.
// Prefers a single DATABASE_URL (as provided by Neon); otherwise reads the
// individual DB_* variables with getenv() and falls back to local defaults.

$url = getenv('DATABASE_URL') ?: null;

if ($url) {
    $parts = parse_url($url);
    parse_str($parts['query'] ?? '', $query);

    return [
        'host'     => $parts['host'] ?? '127.0.0.1',
        'port'     => (string)($parts['port'] ?? '5432'),
        'dbname'   => ltrim($parts['path'] ?? '/neondb', '/'),
        'username' => urldecode($parts['user'] ?? ''),
        'password' => urldecode($parts['pass'] ?? ''),
        'sslmode'  => $query['sslmode'] ?? 'require',
    ];
}

return [
    'host'     => getenv('DB_HOST') ?: '127.0.0.1',
    'port'     => getenv('DB_PORT') ?: '5432',
    'dbname'   => getenv('DB_NAME') ?: 'tandem_db',
    'username' => getenv('DB_USER') ?: 'postgres',
    'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    'sslmode'  => getenv('DB_SSLMODE') ?: 'prefer',
];
