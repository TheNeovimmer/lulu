<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminContactController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $contacts = $db->fetchAll("SELECT * FROM contacts ORDER BY is_read ASC, created_at DESC");
        View::render('admin/contacts', compact('contacts'), 'admin');
    }

    public function markRead($id) {
        $db = Database::getInstance();
        $db->query("UPDATE contacts SET is_read = 1 WHERE id = ?", [$id]);
        Request::back();
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM contacts WHERE id = ?", [$id]);
        Session::setFlash('success', 'Message supprimé.');
        Request::redirect('/admin/contacts');
    }
}
