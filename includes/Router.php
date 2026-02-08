<?php

class Router
{
    protected $routes = [];

    public function loadRoutes($file)
    {
        if (!file_exists($file)) {
            throw new Exception("Routes file not found: $file");
        }
        $this->routes = require $file;
    }

    public function dispatch($uri)
    {
        // Remove query string and trim slashes
        $path = parse_url($uri, PHP_URL_PATH);

        // Remove script name if we are not using mod_rewrite (fallback)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && strpos($path, $scriptName) === 0) {
            $path = substr($path, strlen($scriptName));
        }

        $path = trim($path, '/');

        // Default home route
        if ($path === '' || $path === 'index.php') {
            $path = '/';
        }
        else {
            $path = '/' . $path;
        }

        if (array_key_exists($path, $this->routes)) {
            return $this->routes[$path];
        }

        return null; // 404
    }
}