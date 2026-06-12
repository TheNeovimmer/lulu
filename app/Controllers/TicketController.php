<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;

class TicketController extends Controller {
    public function __construct() {
        $this->layout = 'front';
        $this->authCheck();
    }

    public function index() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $role = Session::get('user_role_slug');

        switch ($role) {
            case 'expert':
                $tickets = $db->fetchAll(
                    "SELECT t.*, u.name as user_name, u.email as user_email FROM tickets t LEFT JOIN users u ON t.user_id = u.id WHERE t.assigned_to = ? ORDER BY t.created_at DESC",
                    [$userId]
                );
                break;
            case 'ctt':
                $tickets = $db->fetchAll(
                    "SELECT t.*, u.name as user_name, u.email as user_email FROM tickets t LEFT JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC"
                );
                break;
            default:
                $tickets = $db->fetchAll(
                    "SELECT t.*, (SELECT COUNT(*) FROM ticket_messages WHERE ticket_id = t.id) as message_count FROM tickets t WHERE t.user_id = ? ORDER BY t.created_at DESC",
                    [$userId]
                );
        }

        $this->render('tickets/index', compact('tickets'));
    }

    public function show($id) {
        $db = Database::getInstance();
        $role = Session::get('user_role_slug');
        $userId = Session::get('user_id');

        $ticket = $db->fetch(
            "SELECT t.*, u.name as user_name, u.email as user_email FROM tickets t LEFT JOIN users u ON t.user_id = u.id WHERE t.id = ?",
            [$id]
        );
        if (!$ticket) {
            $this->render('errors/404');
            return;
        }

        if ($role === 'maman' && $ticket['user_id'] != $userId) {
            header('Location: /auth/login');
            exit;
        }
        if ($role === 'expert' && $ticket['assigned_to'] != $userId) {
            header('Location: /auth/login');
            exit;
        }

        $messages = $db->fetchAll(
            "SELECT tm.*, u.name as user_name FROM ticket_messages tm LEFT JOIN users u ON tm.user_id = u.id WHERE tm.ticket_id = ? ORDER BY tm.created_at ASC",
            [$id]
        );

        $this->render('tickets/show', compact('ticket', 'messages'));
    }

    public function create() {
        if (!Request::isPost()) {
            Request::back();
        }

        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $subject = trim(Request::post('subject'));
        $message = trim(Request::post('message'));
        $priority = Request::post('priority', 'normal');

        $ticketId = $db->insert(
            "INSERT INTO tickets (user_id, subject, status, priority, created_at) VALUES (?, ?, 'open', ?, NOW())",
            [$userId, $subject, $priority]
        );
        $db->insert(
            "INSERT INTO ticket_messages (ticket_id, user_id, message, created_at) VALUES (?, ?, ?, NOW())",
            [$ticketId, $userId, $message]
        );

        Session::setFlash('success', 'Ticket créé avec succès.');
        Request::redirect('/tickets');
    }

    public function reply($id) {
        if (!Request::isPost()) {
            Request::back();
        }

        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $message = trim(Request::post('message'));

        $db->insert(
            "INSERT INTO ticket_messages (ticket_id, user_id, message, created_at) VALUES (?, ?, ?, NOW())",
            [$id, $userId, $message]
        );
        $db->query("UPDATE tickets SET updated_at = NOW() WHERE id = ?", [$id]);

        Session::setFlash('success', 'Réponse ajoutée.');
        Request::back();
    }
}
