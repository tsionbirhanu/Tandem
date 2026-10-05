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

            $isNeon = str_ends_with($config['host'], '.neon.tech');
            if ($isNeon && self::libpqLacksSni()) {
                $dsn = self::withNeonEndpoint($dsn, $config['host']);
            }

            try {
                self::$instance = self::connect($dsn, $config, $options);
            } catch (PDOException $e) {
                // Safety net: an old libpq we could not detect. Neon then asks for the
                // endpoint ID explicitly, so retry once with it.
                if (!$isNeon || !str_contains($e->getMessage(), 'Endpoint ID is not specified')) {
                    throw $e;
                }
                self::$instance = self::connect(self::withNeonEndpoint($dsn, $config['host']), $config, $options);
            }
        }

        return self::$instance;
    }

    /**
     * Neon routes connections by the SNI hostname, which libpq only sends from
     * version 14 on. XAMPP bundles libpq 11; Docker/Linux images ship a modern one.
     * Sending the endpoint option to a modern libpq is rejected by Neon (it then sees
     * two different names), so it is only added when libpq is too old for SNI.
     */
    private static function libpqLacksSni(): bool {
        if (!defined('PGSQL_LIBPQ_VERSION')) {
            return false; // pgsql extension not loaded (e.g. Docker image) — assume a modern libpq
        }
        return version_compare(PGSQL_LIBPQ_VERSION, '14', '<');
    }

    /** Appends Neon's endpoint option (the host's first label, minus "-pooler") to a DSN. */
    private static function withNeonEndpoint(string $dsn, string $host): string {
        $endpointId = preg_replace('/-pooler$/', '', explode('.', $host)[0]);
        return $dsn . ";options='endpoint={$endpointId}'";
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
