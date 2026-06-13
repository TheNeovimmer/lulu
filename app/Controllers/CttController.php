<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Services\TicketService;
use App\Repositories\TicketRepository;
use App\Repositories\FaqRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\NewsletterRepository;

class CttController extends Controller {
    private TicketService $ticketService;
    private TicketRepository $ticketRepo;
    private FaqRepository $faqRepo;
    private NotificationRepository $notifRepo;

    public function __construct() {
        $this->layout = 'ctt';
        $this->authCheck();
        if (Session::get('user_role_slug') !== 'ctt') {
            header('Location: /auth/login');
            exit;
        }
        $this->ticketService = new TicketService();
        $this->ticketRepo = new TicketRepository();
        $this->faqRepo = new FaqRepository();
        $this->notifRepo = new NotificationRepository();
    }

    public function index() {
        $db = \App\Core\Database::getInstance();
        $stats = $this->ticketRepo->getStats();
        $stats['open_tickets'] = $stats['open'] ?? $this->ticketRepo->count(['status' => 'open']);
        $stats['faq_entries'] = $this->faqRepo->count();
        $stats['high_priority'] = $this->ticketRepo->count(['priority' => 'high', 'status' => 'open']);
        $stats['medium_priority'] = $this->ticketRepo->count(['priority' => 'medium', 'status' => 'open']);
        $stats['low_priority'] = $this->ticketRepo->count(['priority' => 'low', 'status' => 'open']);
        $stats['in_progress'] = $this->ticketRepo->count(['status' => 'in_progress']);
        $stats['resolved_today'] = $db->fetch(
            "SELECT COUNT(*) as count FROM tickets WHERE status = 'closed' AND DATE(updated_at) = CURDATE()"
        )['count'] ?? 0;
        $stats['resolved_this_month'] = $db->fetch(
            "SELECT COUNT(*) as count FROM tickets WHERE status = 'closed' AND updated_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')"
        )['count'] ?? 0;
        $stats['avg_response_time'] = $this->ticketRepo->getAvgResponseTime();

        $recentTickets = $this->ticketRepo->allWithDetails('', [], 't.created_at DESC', 5);

        $monthlyTickets = $db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count
             FROM tickets WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY month ORDER BY month ASC"
        );

        $this->render('ctt/index', compact('stats', 'recentTickets', 'monthlyTickets'));
    }

    public function tickets() {
        $status = Request::get('status');
        $priority = Request::get('priority');
        $type = Request::get('type');
        $where = '';
        $params = [];
        $conditions = [];
        if ($status) { $conditions[] = 't.status = ?'; $params[] = $status; }
        if ($priority) { $conditions[] = 't.priority = ?'; $params[] = $priority; }
        if ($type) { $conditions[] = 'ru.slug = ?'; $params[] = $type; }
        if ($conditions) $where = implode(' AND ', $conditions);
        $tickets = $this->ticketRepo->allWithDetails($where, $params);
        $agents = $this->ticketRepo->getAgents();
        $this->render('ctt/tickets', compact('tickets', 'agents', 'status', 'priority'));
    }

    public function updateTicket($id) {
        if (!Request::isPost()) { Request::back(); }
        $this->ticketService->updateStatus($id, Request::post('status'));
        Session::setFlash('success', 'Statut du ticket mis à jour.');
        Request::back();
    }

    public function assignTicket($id) {
        if (!Request::isPost()) { Request::back(); }
        $this->ticketService->assign($id, Request::post('expert_id'));
        Session::setFlash('success', 'Ticket assigné à l\'expert.');
        Request::back();
    }

    public function faq() {
        $faqs = $this->faqRepo->findAllOrdered();
        $grouped = [];
        foreach ($faqs as $faq) {
            $cat = $faq['category'] ?? 'Général';
            $grouped[$cat][] = $faq;
        }
        $this->render('ctt/faq', compact('grouped'));
    }

    public function createFaq() {
        if (!Request::isPost()) { Request::back(); }
        $this->faqRepo->create([
            'question' => trim(Request::post('question')),
            'answer' => trim(Request::post('answer')),
            'category' => Request::post('category', 'Général'),
            'display_order' => (int)Request::post('display_order', 0),
        ]);
        Session::setFlash('success', 'FAQ ajoutée avec succès.');
        Request::back();
    }

    public function deleteFaq($id) {
        $this->faqRepo->delete($id);
        Session::setFlash('success', 'FAQ supprimée.');
        Request::back();
    }

    public function historique() {
        $tickets = $this->ticketRepo->raw(
            "SELECT t.*, u.name as user_name, assignee.name as assigned_name
             FROM tickets t
             LEFT JOIN users u ON t.user_id = u.id
             LEFT JOIN users assignee ON t.assigned_to = assignee.id
             WHERE t.status = 'closed'
             ORDER BY t.updated_at DESC LIMIT 50"
        );
        $this->render('ctt/historique', compact('tickets'));
    }

    public function rapports() {
        $db = \App\Core\Database::getInstance();
        $stats = $this->ticketRepo->getStats();
        $stats['total_tickets'] = $this->ticketRepo->count();
        $stats['resolved'] = $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE status = 'closed'")['count'] ?? 0;
        $stats['avg_response_time'] = $this->ticketRepo->getAvgResponseTime();
        $stats['total_agents'] = $this->ticketRepo->rawOne(
            "SELECT COUNT(DISTINCT u.id) as count FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug IN ('ctt', 'expert')"
        )['count'] ?? 0;
        $this->render('ctt/rapports', compact('stats'));
    }

    public function respondTicket($id) {
        $message = trim(Request::post('message'));
        if ($message) {
            $this->ticketService->reply($id, Session::get('user_id'), $message);
        }
        Session::setFlash('success', 'Réponse envoyée.');
        Request::back();
    }

    public function notifications() {
        $userId = Session::get('user_id');
        $this->notifRepo->markAllRead($userId);
        $notifications = $this->notifRepo->findByUser($userId);
        $this->render('ctt/notifications', compact('notifications'));
    }

    public function readAllNotifications() {
        $this->notifRepo->markAllRead(Session::get('user_id'));
        Session::setFlash('success', 'Notifications marquées comme lues.');
        Request::back();
    }

    public function readNotification($id) {
        $this->notifRepo->markRead($id, Session::get('user_id'));
        Request::back();
    }
}
