<?php
require_once __DIR__ . '/../env.php';
require_once __DIR__ . '/../app/autoload.php';

use App\Core\Router;
use App\Core\Session;

Session::start();
$router = new Router();
require_once __DIR__ . '/../routes.php';
$url = $_GET['url'] ?? '';
if (empty($url)) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($basePath !== '/' && $basePath !== '.' && strpos($uri, $basePath) === 0) {
        $uri = substr($uri, strlen($basePath));
    }
    $url = trim($uri, '/');
}
$router->dispatch($_SERVER['REQUEST_METHOD'], $url);
