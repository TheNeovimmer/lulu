<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;

abstract class Controller {
    protected $layout = 'front';

    protected function render($view, $data = []) {
        View::render($view, $data, $this->layout);
    }

    protected function authCheck() {
        if (!Session::has('user_id')) {
            header('Location: /auth/login');
            exit;
        }
    }
}
