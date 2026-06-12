<?php
require_once __DIR__ . '/../env.php';
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require $file;
});

use App\Core\Router;
use App\Core\Session;

Session::start();
$router = new Router();
require_once __DIR__ . '/../routes.php';
$url = $_GET['url'] ?? '';
if (empty($url)) {
    $url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
}
$router->dispatch($_SERVER['REQUEST_METHOD'], $url);
