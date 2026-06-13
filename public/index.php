<?php
require_once __DIR__ . '/../env.php';
require_once __DIR__ . '/../vendor/autoload.php';

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
