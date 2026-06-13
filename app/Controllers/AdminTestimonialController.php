<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminTestimonialController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $testimonials = $db->fetchAll(
            "SELECT t.*, u.name as user_name FROM testimonials t LEFT JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC"
        );
        View::render('admin/testimonials', compact('testimonials'), 'admin');
    }

    public function approve($id) {
        $db = Database::getInstance();
        $db->query("UPDATE testimonials SET status = 'approved' WHERE id = ?", [$id]);
        Session::setFlash('success', 'Témoignage approuvé.');
        Request::redirect('/admin/testimonials');
    }

    public function reject($id) {
        $db = Database::getInstance();
        $db->query("UPDATE testimonials SET status = 'rejected' WHERE id = ?", [$id]);
        Session::setFlash('success', 'Témoignage rejeté.');
        Request::redirect('/admin/testimonials');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM testimonials WHERE id = ?", [$id]);
        Session::setFlash('success', 'Témoignage supprimé.');
        Request::redirect('/admin/testimonials');
    }
}
