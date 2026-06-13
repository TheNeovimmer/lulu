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
        $faqs = $db->fetchAll("SELECT * FROM faqs ORDER BY display_order ASC");
        $grouped = [];
        foreach ($faqs as $faq) {
            $cat = $faq['category'] ?? 'Général';
            $grouped[$cat][] = $faq;
        }
        View::render('admin/faqs', compact('grouped'), 'admin');
    }

    public function store() {
        $db = Database::getInstance();
        $db->query("INSERT INTO faqs (question, answer, category, display_order) VALUES (?, ?, ?, ?)", [
            Request::post('question'), Request::post('answer'), Request::post('category', 'Général'), (int)Request::post('display_order', 0)
        ]);
        Session::setFlash('success', 'FAQ ajoutée.');
        Request::redirect('/admin/faqs');
    }

    public function edit($id) {
        $db = Database::getInstance();
        $faq = $db->fetch("SELECT * FROM faqs WHERE id = ?", [$id]);
        if (!$faq) { Request::redirect('/admin/faqs'); }
        $allFaqs = $db->fetchAll("SELECT * FROM faqs ORDER BY display_order ASC");
        $grouped = [];
        foreach ($allFaqs as $f) {
            $cat = $f['category'] ?? 'Général';
            $grouped[$cat][] = $f;
        }
        View::render('admin/faqs', ['grouped' => $grouped, 'editFaq' => $faq], 'admin');
    }

    public function update($id) {
        $db = Database::getInstance();
        $db->query("UPDATE faqs SET question = ?, answer = ?, category = ?, display_order = ? WHERE id = ?", [
            Request::post('question'), Request::post('answer'), Request::post('category', 'Général'), (int)Request::post('display_order', 0), $id
        ]);
        Session::setFlash('success', 'FAQ mise à jour.');
        Request::redirect('/admin/faqs');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM faqs WHERE id = ?", [$id]);
        Session::setFlash('success', 'FAQ supprimée.');
        Request::redirect('/admin/faqs');
    }
}
