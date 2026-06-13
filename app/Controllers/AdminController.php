<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Database;
use App\Repositories\UserRepository;
use App\Repositories\TicketRepository;
use App\Repositories\ArticleRepository;

class AdminController {
    private UserRepository $userRepo;
    private TicketRepository $ticketRepo;
    private ArticleRepository $articleRepo;

    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
        $this->userRepo = new UserRepository();
        $this->ticketRepo = new TicketRepository();
        $this->articleRepo = new ArticleRepository();
    }

    public function index() {
        $db = Database::getInstance();

        $stats = [
            'users_total' => $db->fetch("SELECT COUNT(*) as count FROM users")['count'] ?? 0,
            'users_active' => $db->fetch("SELECT COUNT(*) as count FROM users WHERE status = 'active'")['count'] ?? 0,
            'users_suspended' => $db->fetch("SELECT COUNT(*) as count FROM users WHERE status = 'suspended'")['count'] ?? 0,
            'articles' => $db->fetch("SELECT COUNT(*) as count FROM articles")['count'] ?? 0,
            'articles_published' => $db->fetch("SELECT COUNT(*) as count FROM articles WHERE status = 'published'")['count'] ?? 0,
            'tickets_open' => $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE status = 'open'")['count'] ?? 0,
            'tickets_total' => $db->fetch("SELECT COUNT(*) as count FROM tickets")['count'] ?? 0,
            'contacts_unread' => $db->fetch("SELECT COUNT(*) as count FROM contacts WHERE is_read = 0")['count'] ?? 0,
            'appointments_total' => $db->fetch("SELECT COUNT(*) as count FROM appointments")['count'] ?? 0,
            'appointments_pending' => $db->fetch("SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'")['count'] ?? 0,
            'posts_total' => $db->fetch("SELECT COUNT(*) as count FROM community_posts")['count'] ?? 0,
            'mamans' => $db->fetch("SELECT COUNT(*) as count FROM mothers")['count'] ?? 0,
            'mamans_pregnant' => $db->fetch("SELECT COUNT(*) as count FROM pregnancies WHERE status = 'active'")['count'] ?? 0,
            'registrations_this_month' => $db->fetch(
                "SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')"
            )['count'] ?? 0,
        ];

        $recentUsers = $this->userRepo->raw(
            "SELECT u.*, r.name as role_name
             FROM users u
             LEFT JOIN roles r ON u.role_id = r.id
             ORDER BY u.created_at DESC LIMIT 10"
        );

        $monthlyRegistrations = $db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count
             FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY month ORDER BY month ASC"
        );

        View::render('admin/dashboard', compact('stats', 'recentUsers', 'monthlyRegistrations'), 'admin');
    }
}
