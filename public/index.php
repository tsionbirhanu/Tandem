<?php
// public/index.php
// Single Entry Point for the Tandem MVC Application.

// PHP Built-in Server static file fallback
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . $path;
    if ($path !== '/' && file_exists($file) && !is_dir($file)) {
        return false;
    }
}

// Standardize directory separator & base path
define('BASE_PATH', dirname(__DIR__));

// Require Core Dependencies & Helpers
require_once BASE_PATH . '/includes/Database.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/app/Helpers/view.php';

// Require Models
require_once BASE_PATH . '/app/Models/User.php';
require_once BASE_PATH . '/app/Models/Client.php';
require_once BASE_PATH . '/app/Models/Freelancer.php';
require_once BASE_PATH . '/app/Models/Admin.php';
require_once BASE_PATH . '/app/Models/UserFactory.php';
require_once BASE_PATH . '/app/Models/Service.php';
require_once BASE_PATH . '/app/Models/Category.php';
require_once BASE_PATH . '/app/Models/ProjectRequest.php';
require_once BASE_PATH . '/app/Models/Message.php';
require_once BASE_PATH . '/app/Models/Review.php';

// Require Controllers
require_once BASE_PATH . '/app/Controllers/HomeController.php';
require_once BASE_PATH . '/app/Controllers/AuthController.php';
require_once BASE_PATH . '/app/Controllers/ServiceController.php';
require_once BASE_PATH . '/app/Controllers/DashboardController.php';
require_once BASE_PATH . '/app/Controllers/MessageController.php';

// Load Routes & Dispatch HTTP Request
/** @var \Routes\Router $router */
$router = require_once BASE_PATH . '/routes/web.php';

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri    = $_SERVER['REQUEST_URI'] ?? '/';

$router->dispatch($requestMethod, $requestUri);
