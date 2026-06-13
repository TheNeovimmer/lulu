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

        $stats = [
            'open_tickets' => $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE status = 'open'")['count'],
            'resolved_today' => $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE status = 'closed' AND DATE(updated_at) = CURDATE()")['count'],
            'faq_entries' => $db->fetch("SELECT COUNT(*) as count FROM faqs")['count'],
        ];

        $recent_tickets = $db->fetchAll("SELECT t.*, u.name as user_name FROM tickets t LEFT JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC LIMIT 5");

        $this->render('ctt/index', compact('stats', 'recent_tickets'));
    }

    public function tickets() {
        $db = Database::getInstance();
        $status = Request::get('status');
        $priority = Request::get('priority');
        $type = Request::get('type');

        $sql = "SELECT t.*, u.name as user_name, u.email as user_email, assignee.name as assigned_name, ru.slug as user_role_slug
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN roles ru ON u.role_id = ru.id
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
        if ($type) {
            $sql .= " AND ru.slug = ?";
            $params[] = $type;
        }
        $sql .= " ORDER BY t.created_at DESC";

        $tickets = $db->fetchAll($sql, $params);

        $agents = $db->fetchAll("SELECT u.id, u.name FROM users u JOIN roles r ON u.role_id = r.id WHERE (r.slug = 'ctt' OR r.slug = 'expert') ORDER BY u.name");

        $this->render('ctt/tickets', compact('tickets', 'agents', 'status', 'priority'));
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

        $db->query("UPDATE tickets SET assigned_to = ?, status = 'in_progress', updated_at = NOW() WHERE id = ?", [$expertId, $id]);
        $db->insert(
            "INSERT INTO notifications (user_id, type, title, message, link) VALUES (?, 'info', 'Ticket assigné', 'Un nouveau ticket vous a été assigné.', '/tickets/{$id}')",
            [$expertId]
        );
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
            "INSERT INTO faqs (question, answer, category, display_order) VALUES (?, ?, ?, ?)",
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
             WHERE t.status = 'closed'
             ORDER BY t.updated_at DESC
             LIMIT 50"
        );
        $this->render('ctt/historique', compact('tickets'));
    }

    public function rapports() {
        $db = Database::getInstance();

        $totalTickets = $db->fetch("SELECT COUNT(*) as count FROM tickets")['count'];
        $resolved = $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE status = 'closed'")['count'];
        $agentsCount = $db->fetch("SELECT COUNT(DISTINCT u.id) as count FROM users u JOIN roles r ON u.role_id = r.id WHERE (r.slug = 'ctt' OR r.slug = 'expert')")['count'];

        // Calculate average response time (minutes between ticket creation and first response)
        $avgResponse = $db->fetch(
            "SELECT AVG(TIMESTAMPDIFF(MINUTE, t.created_at, tm.created_at)) as avg_time
             FROM tickets t
             JOIN ticket_messages tm ON tm.ticket_id = t.id AND tm.id = (
                 SELECT MIN(id) FROM ticket_messages WHERE ticket_id = t.id AND user_id != t.user_id
             )
             WHERE tm.id IS NOT NULL"
        );
        $avgResponseTime = $avgResponse && $avgResponse['avg_time'] ? round($avgResponse['avg_time']) . ' min' : 'N/A';

        $stats = [
            'total_tickets' => $totalTickets,
            'resolved' => $resolved,
            'avg_response_time' => $avgResponseTime,
            'total_agents' => $agentsCount,
        ];

        $this->render('ctt/rapports', compact('stats'));
    }

    public function deleteFaq($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM faqs WHERE id = ?", [$id]);
        Session::setFlash('success', 'FAQ supprimée.');
        Request::back();
    }

    public function readAllNotifications() {
        $db = Database::getInstance();
        $db->query("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0", [Session::get('user_id')]);
        Session::setFlash('success', 'Notifications marquées comme lues.');
        Request::back();
    }

    public function readNotification($id) {
        $db = Database::getInstance();
        $db->query("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?", [$id, Session::get('user_id')]);
        Request::back();
    }

    public function respondTicket($id) {
        $db = Database::getInstance();
        $message = trim(Request::post('message'));
        if ($message) {
            $db->query("INSERT INTO ticket_messages (ticket_id, user_id, message, created_at) VALUES (?, ?, ?, NOW())", [$id, Session::get('user_id'), $message]);
            $ticket = $db->fetch("SELECT user_id FROM tickets WHERE id = ?", [$id]);
            if ($ticket && $ticket['user_id'] != Session::get('user_id')) {
                $db->insert(
                    "INSERT INTO notifications (user_id, type, title, message, link) VALUES (?, 'info', 'Réponse à votre ticket', 'Le CTT a répondu à votre ticket de support.', '/dashboard/tickets')",
                    [$ticket['user_id']]
                );
            }
        }
        Session::setFlash('success', 'Réponse envoyée.');
        Request::back();
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
