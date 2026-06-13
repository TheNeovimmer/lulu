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

    public function edit($id) {
        $db = Database::getInstance();
        $editFaq = $db->fetch("SELECT * FROM faqs WHERE id = ?", [$id]);
        if (!$editFaq) { Request::redirect('/admin/faqs'); }
        $faqs = $db->fetchAll("SELECT * FROM faqs ORDER BY category ASC, display_order ASC");
        $grouped = [];
        foreach ($faqs as $faq) {
            $grouped[$faq['category']][] = $faq;
        }
        View::render('admin/faqs', compact('faqs', 'grouped', 'editFaq'), 'admin');
    }

    public function update($id) {
        $db = Database::getInstance();
        $db->query("UPDATE faqs SET category = ?, question = ?, answer = ?, display_order = ? WHERE id = ?", [
            Request::post('category'), Request::post('question'), Request::post('answer'), (int) Request::post('display_order', 0), $id
        ]);
        Session::setFlash('success', 'FAQ mise à jour.');
        Request::redirect('/admin/faqs');
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
