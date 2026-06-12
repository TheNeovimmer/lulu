<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminExpertController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $experts = $db->fetchAll(
            "SELECT u.*, e.specialty, e.is_verified, e.biography
             FROM users u
             LEFT JOIN expert_profiles e ON u.id = e.user_id
             JOIN roles r ON u.role_id = r.id
             WHERE r.slug = 'expert'
             ORDER BY u.created_at DESC"
        );
        View::render('admin/experts', compact('experts'), 'admin');
    }

    public function validate($id) {
        $db = Database::getInstance();
        $db->query("UPDATE users u JOIN roles r ON u.role_id = r.id SET u.status = 'active' WHERE u.id = ? AND r.slug = 'expert'", [$id]);
        Session::setFlash('success', 'Expert validé avec succès.');
        Request::redirect('/admin/experts');
    }
}
