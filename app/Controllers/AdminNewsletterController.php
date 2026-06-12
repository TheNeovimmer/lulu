<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminNewsletterController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $subscribers = $db->fetchAll("SELECT * FROM newsletters ORDER BY created_at DESC");
        View::render('admin/newsletters', compact('subscribers'), 'admin');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM newsletters WHERE id = ?", [$id]);
        Session::setFlash('success', 'Abonné supprimé.');
        Request::redirect('/admin/newsletters');
    }
}
