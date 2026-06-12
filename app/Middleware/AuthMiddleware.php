<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Request;

class AuthMiddleware {
    public static function check() {
        if (!Session::has('user_id')) {
            Request::redirect('/auth/login');
        }
    }
}
