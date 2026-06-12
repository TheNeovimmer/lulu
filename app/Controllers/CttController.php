<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;

class CttController extends Controller {
    public function __construct() {
        $this->layout = 'ctt';
        $this->authCheck();
        if (Session::get('user_role_slug') !== 'ctt') {
            header('Location: /auth/login');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();

        $openTickets = $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE status = 'open'")['count'];
        $resolvedToday = $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE status = 'resolved' AND DATE(updated_at) = CURDATE()")['count'];
        $faqCount = $db->fetch("SELECT COUNT(*) as count FROM faqs")['count'];

        $this->render('ctt/index', compact('openTickets', 'resolvedToday', 'faqCount'));
    }

    public function tickets() {
        $db = Database::getInstance();
        $status = Request::get('status');
        $priority = Request::get('priority');
        $type = Request::get('type');

        $sql = "SELECT t.*, u.name as user_name, u.email as user_email, assignee.name as assigned_name
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN users assignee ON t.assigned_to = assignee.id
                WHERE 1=1";
        $params = [];

        if ($status) {
            $sql .= " AND t.status = ?";
            $params[] = $status;
        }
        if ($priority) {
            $sql .= " AND t.priority = ?";
            $params[] = $priority;
        }
        $sql .= " ORDER BY t.created_at DESC";

        $tickets = $db->fetchAll($sql, $params);

        $experts = $db->fetchAll("SELECT u.id, u.name FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'expert' AND u.status = 'active' ORDER BY u.name");

        $this->render('ctt/tickets', compact('tickets', 'experts', 'status', 'priority'));
    }

    public function updateTicket($id) {
        if (!Request::isPost()) {
            Request::back();
        }

        $db = Database::getInstance();
        $status = Request::post('status');

        $db->query("UPDATE tickets SET status = ?, updated_at = NOW() WHERE id = ?", [$status, $id]);
        Session::setFlash('success', 'Statut du ticket mis à jour.');
        Request::back();
    }

    public function assignTicket($id) {
        if (!Request::isPost()) {
            Request::back();
        }

        $db = Database::getInstance();
        $expertId = Request::post('expert_id');

        $db->query("UPDATE tickets SET assigned_to = ?, status = 'assigned', updated_at = NOW() WHERE id = ?", [$expertId, $id]);
        Session::setFlash('success', 'Ticket assigné à l\'expert.');
        Request::back();
    }

    public function faq() {
        $db = Database::getInstance();

        $faqs = $db->fetchAll("SELECT * FROM faqs ORDER BY display_order ASC");
        $this->render('ctt/faq', compact('faqs'));
    }

    public function createFaq() {
        if (!Request::isPost()) {
            Request::back();
        }

        $db = Database::getInstance();
        $question = trim(Request::post('question'));
        $answer = trim(Request::post('answer'));
        $category = Request::post('category', 'Général');
        $displayOrder = (int)Request::post('display_order', 0);

        $db->insert(
            "INSERT INTO faqs (question, answer, category, display_order, created_at) VALUES (?, ?, ?, ?, NOW())",
            [$question, $answer, $category, $displayOrder]
        );
        Session::setFlash('success', 'FAQ ajoutée avec succès.');
        Request::back();
    }

    public function historique() {
        $db = Database::getInstance();

        $tickets = $db->fetchAll(
            "SELECT t.*, u.name as user_name, assignee.name as assigned_name
             FROM tickets t
             LEFT JOIN users u ON t.user_id = u.id
             LEFT JOIN users assignee ON t.assigned_to = assignee.id
             WHERE t.status IN ('resolved', 'closed')
             ORDER BY t.updated_at DESC
             LIMIT 50"
        );
        $this->render('ctt/historique', compact('tickets'));
    }

    public function rapports() {
        $db = Database::getInstance();

        $totalTickets = $db->fetch("SELECT COUNT(*) as count FROM tickets")['count'];
        $openTickets = $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE status = 'open'")['count'];
        $resolvedTickets = $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE status = 'resolved'")['count'];

        $monthlyStats = $db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count FROM tickets GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month DESC LIMIT 12"
        );

        $this->render('ctt/rapports', compact('totalTickets', 'openTickets', 'resolvedTickets', 'monthlyStats'));
    }

    public function notifications() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $db->query("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0", [$userId]);
        $notifications = $db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
        $this->render('ctt/notifications', compact('notifications'));
    }
}
