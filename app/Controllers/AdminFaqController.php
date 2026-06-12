<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminFaqController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $faqs = $db->fetchAll("SELECT * FROM faqs ORDER BY category ASC, display_order ASC");
        $grouped = [];
        foreach ($faqs as $faq) {
            $grouped[$faq['category']][] = $faq;
        }
        View::render('admin/faqs', compact('faqs', 'grouped'), 'admin');
    }

    public function store() {
        $db = Database::getInstance();
        $db->insert(
            "INSERT INTO faqs (category, question, answer, display_order) VALUES (?, ?, ?, ?)",
            [
                Request::post('category'),
                Request::post('question'),
                Request::post('answer'),
                (int) Request::post('display_order', 0),
            ]
        );
        Session::setFlash('success', 'FAQ ajoutée.');
        Request::redirect('/admin/faqs');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM faqs WHERE id = ?", [$id]);
        Session::setFlash('success', 'FAQ supprimée.');
        Request::redirect('/admin/faqs');
    }
}
