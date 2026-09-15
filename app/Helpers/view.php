<?php
// app/Helpers/view.php
// View rendering helper for Tandem MVC application.

if (!function_exists('render')) {
    /**
     * Safely renders a view template from app/Views with passed data.
     *
     * @param string $view Name of view template relative to app/Views/ without extension (e.g. 'auth/login')
     * @param array $data Associative array of variables to inject into the view template
     * @return void
     */
    function render(string $view, array $data = []): void {
        // Extract variables into local template scope safely without overwriting existing variables
        extract($data, EXTR_SKIP);

        $viewFile = __DIR__ . '/../Views/' . ltrim($view, '/') . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1><p>View file [{$view}] not found.</p>";
            exit;
        }

        require $viewFile;
    }
}
