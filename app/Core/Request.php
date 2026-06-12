<?php
namespace App\Core;

class Request {
    public static function method() { return $_SERVER['REQUEST_METHOD']; }
    public static function post($key, $default = null) { return $_POST[$key] ?? $default; }
    public static function get($key, $default = null) { return $_GET[$key] ?? $default; }
    public static function file($key) { return $_FILES[$key] ?? null; }
    public static function all() { return $_POST; }
    public static function isPost() { return self::method() === 'POST'; }
    public static function redirect($url) { header('Location: ' . BASE_URL . $url); exit; }
    public static function back() { header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/')); exit; }
}
