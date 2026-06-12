<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Request;

class RoleMiddleware {
    public static function check($roleSlug) {
        if (!Session::has('user_id') || Session::get('user_role_slug') !== $roleSlug) {
            Request::redirect('/auth/login');
        }
    }
}
