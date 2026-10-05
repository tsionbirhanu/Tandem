<?php
// includes/env.php
// Minimal .env file loader. Reads KEY=VALUE lines from the project root .env
// into getenv()/$_ENV without overriding variables already set by the server.

if (!function_exists('loadEnv')) {
    function loadEnv(string $path): void {
        if (!is_readable($path)) {
            return;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $line, 2));

            // Strip matching surrounding quotes: KEY="value" or KEY='value'
            if (strlen($value) >= 2 && ($value[0] === '"' || $value[0] === "'") && $value[-1] === $value[0]) {
                $value = substr($value, 1, -1);
            }

            if (getenv($key) === false) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
            }
        }
    }
}

loadEnv(dirname(__DIR__) . '/.env');
