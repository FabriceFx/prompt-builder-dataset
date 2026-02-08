<?php




// Define Base Path for file includes
define('BASE_PATH', __DIR__);

// Load Core
require_once BASE_PATH . '/includes/init.php';
require_once BASE_PATH . '/includes/Router.php';

// Instantiate Router
$router = new Router();

// Load Routes
$router->loadRoutes(BASE_PATH . '/includes/routes.php');

// Dispatch
$view = $router->dispatch($_SERVER['REQUEST_URI']);

if ($view) {
    // Check if view file exists
    $viewFile = BASE_PATH . '/' . $view;
    if (file_exists($viewFile)) {
        include $viewFile;
    }
    else {
        // View not found handler
        http_response_code(404);
        echo "404 - View not found: " . htmlspecialchars($view);
    }
}
else {
    // 404 Handler
    http_response_code(404);
    echo "404 - Page not found";
}