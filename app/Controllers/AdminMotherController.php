<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminMotherController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $mamans = $db->fetchAll(
            "SELECT u.*, p.due_date, p.weeks_gestation, p.created_at as pregnancy_since
             FROM users u
             LEFT JOIN pregnancies p ON u.id = p.user_id
             JOIN roles r ON u.role_id = r.id
             WHERE r.slug = 'maman'
             ORDER BY u.created_at DESC"
        );
        View::render('admin/mamans', compact('mamans'), 'admin');
    }
}
