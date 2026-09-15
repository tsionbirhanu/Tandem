<?php
// routes/web.php
// Web Route definitions and lightweight Router matching for Tandem MVC.

namespace Routes;

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\ServiceController;
use App\Controllers\DashboardController;
use App\Controllers\MessageController;

class Router {
    private array $routes = [];

    public function get(string $path, array $handler): void {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, array $handler): void {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => rtrim($path, '/') ?: '/',
            'handler' => $handler,
        ];
    }

    public function dispatch(string $requestMethod, string $requestUri): void {
        $requestMethod = strtoupper($requestMethod);
        $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';
        $path = rtrim($path, '/') ?: '/';

        // Support optional script subfolder if needed
        $scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptName !== '/' && $scriptName !== '\\' && strpos($path, $scriptName) === 0) {
            $path = substr($path, strlen($scriptName));
            $path = rtrim($path, '/') ?: '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $path) {
                [$class, $method] = $route['handler'];
                $controller = new $class();
                $controller->$method();
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        render('errors/404', ['path' => $path]);
    }
}

$router = new Router();

// Route Declarations
$router->get('/', [HomeController::class, 'index']);

// Auth Routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/logout', [AuthController::class, 'logout']);

// Service Routes
$router->get('/services', [ServiceController::class, 'index']);
$router->get('/service/details', [ServiceController::class, 'show']);
$router->get('/service/create', [ServiceController::class, 'showCreate']);
$router->post('/service/create', [ServiceController::class, 'create']);
$router->get('/service/edit', [ServiceController::class, 'showEdit']);
$router->post('/service/edit', [ServiceController::class, 'edit']);
$router->get('/service/delete', [ServiceController::class, 'showDelete']);
$router->post('/service/delete', [ServiceController::class, 'delete']);

// Contact Routes
$router->get('/contact', [MessageController::class, 'showContact']);
$router->post('/contact', [MessageController::class, 'submitContact']);

// Dashboard Routes
$router->get('/dashboard/client', [DashboardController::class, 'client']);
$router->get('/dashboard/freelancer', [DashboardController::class, 'freelancer']);
$router->get('/dashboard/admin', [DashboardController::class, 'admin']);

return $router;
