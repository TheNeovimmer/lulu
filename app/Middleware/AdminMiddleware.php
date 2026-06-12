<?php
namespace App\Middleware;

class AdminMiddleware {
    public static function check() {
        PermissionMiddleware::check('admin.access');
    }
}
