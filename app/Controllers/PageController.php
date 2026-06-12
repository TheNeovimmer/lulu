<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Database;

class PageController {
    public function home() {
        $db = Database::getInstance();
        $featured_articles = $db->fetchAll("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.status = 'published' AND a.featured = 1 ORDER BY a.created_at DESC LIMIT 3");
        $testimonials = $db->fetchAll("SELECT t.*, u.name as user_name, u.email as user_email FROM testimonials t LEFT JOIN users u ON t.user_id = u.id WHERE t.status = 'approved' ORDER BY t.created_at DESC LIMIT 3");
        $faqs = $db->fetchAll("SELECT * FROM faqs ORDER BY display_order ASC LIMIT 4");
        $stats = [
            'mamans' => $db->fetch("SELECT COUNT(*) as count FROM users")['count'],
            'articles' => $db->fetch("SELECT COUNT(*) as count FROM articles WHERE status='published'")['count'],
            'experts' => 200,
            'satisfaction' => '98%'
        ];
        View::render('pages/home', compact('featured_articles', 'testimonials', 'faqs', 'stats'), 'front');
    }

    public function about() {
        View::render('pages/about', [], 'front');
    }
}
