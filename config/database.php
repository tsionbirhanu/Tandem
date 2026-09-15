<?php
// config/database.php
// Database credentials and configuration parameters.
// Reads environment variables with getenv() and falls back to default values.

return [
    'host'     => getenv('DB_HOST') ?: '127.0.0.1',
    'port'     => getenv('DB_PORT') ?: '3306',
    'dbname'   => getenv('DB_NAME') ?: 'tandem_db',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    'charset'  => getenv('DB_CHARSET') ?: 'utf8mb4',
];
