<?php
namespace App\Core;

class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!self::has('_csrf_token')) {
            self::set('_csrf_token', bin2hex(random_bytes(32)));
        }
    }
    public static function set($key, $value) { $_SESSION[$key] = $value; }
    public static function get($key, $default = null) { return $_SESSION[$key] ?? $default; }
    public static function has($key) { return isset($_SESSION[$key]); }
    public static function remove($key) { unset($_SESSION[$key]); }
    public static function destroy() { session_destroy(); }

    public static function csrf_token() {
        return self::get('_csrf_token');
    }

    public static function csrf_field() {
        return '<input type="hidden" name="_csrf_token" value="' . self::csrf_token() . '">';
    }

    public static function validate_csrf($token = null) {
        $token = $token ?? ($_POST['_csrf_token'] ?? '');
        if (!hash_equals(self::csrf_token(), $token)) {
            self::setFlash('error', 'Session expirée. Veuillez réessayer.');
            Request::back();
            exit;
        }
    }

    public static function setFlash($key, $value) { $_SESSION['_flash'][$key] = $value; }
    public static function getFlash($key, $default = null) {
        $val = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }
}
