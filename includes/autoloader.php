<?php
// includes/autoloader.php
// Class autoloader for App\Models namespace.

spl_autoload_register(function ($class) {
    $prefix = 'App\\Models\\';
    $baseDir = __DIR__ . '/../app/Models/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
