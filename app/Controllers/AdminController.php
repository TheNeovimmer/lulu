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
            'tickets_open' => $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE status='open'")['count'],
            'contacts_unread' => $db->fetch("SELECT COUNT(*) as count FROM contacts WHERE is_read=0")['count'],
        ];
        
        $recentUsers = $db->fetchAll(
            "SELECT u.name, u.email, r.name as role, u.created_at 
             FROM users u 
             LEFT JOIN roles r ON u.role_id = r.id 
             ORDER BY u.created_at DESC 
             LIMIT 5"
        );

        View::render('admin/dashboard', compact('stats', 'recentUsers'), 'admin');
    }
}
