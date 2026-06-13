<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminTicketController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $status = Request::get('status', '');

        $where = '';
        $params = [];
        if ($status) {
            $where = "WHERE t.status = ?";
            $params[] = $status;
        }

        $tickets = $db->fetchAll(
            "SELECT t.*, u.name as user_name, e.name as expert_name
             FROM tickets t
             LEFT JOIN users u ON t.user_id = u.id
             LEFT JOIN users e ON t.assigned_to = e.id
             $where
             ORDER BY t.created_at DESC",
            $params
        );
        View::render('admin/tickets', compact('tickets', 'status'), 'admin');
    }

    public function show($id) {
        $db = Database::getInstance();
        $ticket = $db->fetch(
            "SELECT t.*, u.name as user_name, u.email as user_email, e.name as expert_name
             FROM tickets t
             LEFT JOIN users u ON t.user_id = u.id
             LEFT JOIN users e ON t.assigned_to = e.id
             WHERE t.id = ?",
            [$id]
        );
        if (!$ticket) { Request::redirect('/admin/tickets'); }
        $messages = $db->fetchAll(
            "SELECT tm.*, u.name as user_name FROM ticket_messages tm LEFT JOIN users u ON tm.user_id = u.id WHERE tm.ticket_id = ? ORDER BY tm.created_at ASC",
            [$id]
        );
        View::render('admin/ticket-show', compact('ticket', 'messages'), 'admin');
    }

    public function assign($id) {
        $db = Database::getInstance();
        $expertId = Request::post('expert_id');
        $db->query("UPDATE tickets SET assigned_to = ?, status = 'in_progress' WHERE id = ?", [$expertId, $id]);
        Session::setFlash('success', 'Ticket assigné.');
        Request::redirect('/admin/tickets');
    }

    public function close($id) {
        $db = Database::getInstance();
        $db->query("UPDATE tickets SET status = 'closed' WHERE id = ?", [$id]);
        Session::setFlash('success', 'Ticket fermé.');
        Request::redirect('/admin/tickets');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM tickets WHERE id = ?", [$id]);
        Session::setFlash('success', 'Ticket supprimé.');
        Request::redirect('/admin/tickets');
    }

    public function reply($id) {
        $db = Database::getInstance();
        $message = Request::post('message');
        $userId = Session::get('user_id');
        if ($message) {
            $db->query("INSERT INTO ticket_messages (ticket_id, user_id, message, created_at) VALUES (?, ?, ?, NOW())", [$id, $userId, $message]);
        }
        Session::setFlash('success', 'Réponse envoyée.');
        Request::redirect('/admin/tickets/' . $id);
    }
}
