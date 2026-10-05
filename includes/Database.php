<?php
// includes/Database.php
// Singleton PDO database connection wrapper class.

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/autoloader.php';

class Database {
    /**
     * @var PDO|null Shared singleton instance.
     */
    private static ?PDO $instance = null;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct() {}

    /**
     * Private clone method to prevent cloning singleton instance.
     */
    private function __clone() {}

    /**
     * Returns a shared PDO database connection instance.
     *
     * @return PDO
     * @throws RuntimeException|PDOException
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $configPath = __DIR__ . '/../config/database.php';
            
            if (!file_exists($configPath)) {
                throw new RuntimeException("Database configuration file not found at: {$configPath}");
            }
            
            $config = require $configPath;
            
            $dsn = sprintf(
                "pgsql:host=%s;port=%s;dbname=%s;sslmode=%s",
                $config['host'],
                $config['port'],
                $config['dbname'],
                $config['sslmode']
            );

            // Neon routes connections by SNI hostname, which libpq < 14 (bundled with
            // XAMPP) does not send. Passing the endpoint ID explicitly works on any version.
            if (str_ends_with($config['host'], '.neon.tech')) {
                $endpointId = preg_replace('/-pooler$/', '', explode('.', $config['host'])[0]);
                $dsn .= ";options='endpoint={$endpointId}'";
            }

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Emulated prepares send each query in one round trip instead of two
                // (prepare + execute), which matters for a remote serverless database.
                PDO::ATTR_EMULATE_PREPARES   => true,
                // Reuse the connection across requests: a fresh TLS + auth handshake
                // to a remote database costs far more than any single query.
                PDO::ATTR_PERSISTENT         => true,
            ];

            self::$instance = self::connect($dsn, $config, $options);
        }

        return self::$instance;
    }

    /**
     * Opens (or reuses) a persistent connection and makes sure it is still alive.
     * Neon suspends idle databases and drops their connections, so a reused handle
     * may be dead; one cheap ping detects that and we reconnect once.
     */
    private static function connect(string $dsn, array $config, array $options): PDO {
        $pdo = new PDO($dsn, $config['username'], $config['password'], $options);
        try {
            $pdo->query('SELECT 1');
            return $pdo;
        } catch (PDOException $e) {
            // The failed query marks the handle as bad, so PDO opens a new one here
            return new PDO($dsn, $config['username'], $config['password'], $options);
        }
    }
}
