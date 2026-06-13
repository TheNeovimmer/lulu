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
            "SELECT u.*, u.specialty as specialty, (CASE WHEN u.status = 'active' THEN 1 ELSE 0 END) as is_verified, u.bio as biography
                         FROM users u
                         JOIN roles r ON u.role_id = r.id
                         WHERE r.slug = 'expert'
                         ORDER BY u.created_at DESC"
        );
        View::render('admin/experts', compact('experts'), 'admin');
    }

    public function validate($id) {
        $db = Database::getInstance();
        $db->query("UPDATE users u JOIN roles r ON u.role_id = r.id SET u.status = 'active' WHERE u.id = ? AND r.slug = 'expert'", [$id]);
        $db->insert(
            "INSERT INTO notifications (user_id, type, title, message, link) VALUES (?, 'success', 'Compte validé', 'Votre compte expert a été validé par l\\'administrateur. Vous pouvez maintenant publier des articles et répondre aux mamans.', '/expert/dashboard')",
            [$id]
        );
        Session::setFlash('success', 'Expert validé avec succès.');
        Request::redirect('/admin/experts');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM users WHERE id = ?", [$id]);
        Session::setFlash('success', 'Expert supprimé.');
        Request::redirect('/admin/experts');
    }
}
