<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Database;

class AdminController {
    public function __construct() { \App\Middleware\AdminMiddleware::check(); }

    public function index() {
        $db = Database::getInstance();
        $stats = [
            'users' => $db->fetch("SELECT COUNT(*) as count FROM users")['count'],
            'articles' => $db->fetch("SELECT COUNT(*) as count FROM articles")['count'],
            'comments' => $db->fetch("SELECT COUNT(*) as count FROM comments WHERE status='pending'")['count'],
            'testimonials' => $db->fetch("SELECT COUNT(*) as count FROM testimonials WHERE status='pending'")['count'],
            'contacts' => $db->fetch("SELECT COUNT(*) as count FROM contacts WHERE is_read=0")['count'],
            'newsletters' => $db->fetch("SELECT COUNT(*) as count FROM newsletters WHERE is_active=1")['count'],
        ];
        View::render('admin/dashboard', compact('stats'), 'admin');
    }
}
