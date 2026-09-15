<?php
// routes/web.php
// Dynamic HTTP Router supporting GET, POST, and named route parameters (e.g. /services/{id}).

namespace Routes;

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\ServiceController;
use App\Controllers\DashboardController;
use App\Controllers\MessageController;
use App\Controllers\ProfileController;

class Router {
    private array $routes = [];

    public function get(string $path, array $handler): void {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, array $handler): void {
        $normalizedPath = rtrim($path, '/') ?: '/';
        
        // Convert route pattern like '/services/{id}' into regex '~^/services/([^/]+)$~'
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $normalizedPath);
        $regex = '~^' . $pattern . '$~';

        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $normalizedPath,
            'regex'   => $regex,
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
            if ($route['method'] === $requestMethod && preg_match($route['regex'], $path, $matches)) {
                array_shift($matches); // Remove full regex match
                
                [$class, $method] = $route['handler'];
                $controller = new $class();
                
                // Call controller action passing extracted route parameters as arguments
                call_user_func_array([$controller, $method], $matches);
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        render('errors/404', ['path' => $path]);
    }
}

$router = new Router();

// -----------------------------------------------------------------------------
// Route Declarations
// -----------------------------------------------------------------------------

// Home Route
$router->get('/', [HomeController::class, 'index']);

// Auth Routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/logout', [AuthController::class, 'logout']);

// Service Routes (Static & Dynamic RESTful parameters)
$router->get('/services', [ServiceController::class, 'index']);
$router->get('/services/create', [ServiceController::class, 'showCreate']);
$router->post('/services/create', [ServiceController::class, 'create']);

$router->get('/services/{id}', [ServiceController::class, 'show']);
$router->get('/services/{id}/edit', [ServiceController::class, 'showEdit']);
$router->post('/services/{id}/edit', [ServiceController::class, 'edit']);
$router->get('/services/{id}/delete', [ServiceController::class, 'showDelete']);
$router->post('/services/{id}/delete', [ServiceController::class, 'delete']);

// Contact Routes
$router->get('/contact', [MessageController::class, 'showContact']);
$router->post('/contact', [MessageController::class, 'submitContact']);

// Dashboard Routes
$router->get('/dashboard/client', [DashboardController::class, 'client']);
$router->get('/dashboard/freelancer', [DashboardController::class, 'freelancer']);
$router->get('/dashboard/admin', [DashboardController::class, 'admin']);

// Profile Routes
$router->get('/profile/edit', [ProfileController::class, 'showEdit']);
$router->post('/profile/edit', [ProfileController::class, 'edit']);

return $router;
