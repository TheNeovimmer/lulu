<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Database;

class FaqController {
    public function index() {
        $db = Database::getInstance();
        $faqs = $db->fetchAll("SELECT * FROM faqs ORDER BY display_order ASC");
        $grouped = [];
        foreach ($faqs as $faq) {
            $cat = $faq['category'] ?? 'Général';
            $grouped[$cat][] = $faq;
        }
        View::render('pages/faq', compact('grouped'), 'front');
    }
}
