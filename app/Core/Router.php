<?php
namespace App\Core;

class Router {
    private $routes = [];

    public function get($path, $handler) { $this->routes['GET'][$path] = $handler; }
    public function post($path, $handler) { $this->routes['POST'][$path] = $handler; }

    public function dispatch($method, $url) {
        $url = $url ? trim($url, '/') : '';
        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
            if (preg_match('#^' . $pattern . '$#', $url, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$controller, $action] = explode('@', $handler);
                $controller = "App\\Controllers\\{$controller}";
                $instance = new $controller();
                call_user_func_array([$instance, $action], $params);
                return;
            }
        }
        http_response_code(404);
        View::render('errors/404', [], 'front');
    }
}
