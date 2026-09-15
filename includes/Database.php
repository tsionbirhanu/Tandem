<?php
// includes/Database.php
// Singleton PDO database connection wrapper class.

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
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                $config['host'],
                $config['port'],
                $config['dbname'],
                $config['charset']
            );
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            self::$instance = new PDO($dsn, $config['username'], $config['password'], $options);
        }

        return self::$instance;
    }
}
