<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Request;
use App\Repositories\PermissionRepository;

class PermissionMiddleware {
    public static function check($permissionSlug) {
        if (!Session::has('user_id')) {
            Request::redirect('/auth/login');
        }
        $repo = new PermissionRepository();
        if (!$repo->hasPermission(Session::get('user_id'), $permissionSlug)) {
            http_response_code(403);
            die("Accès refusé.");
        }
    }
}
