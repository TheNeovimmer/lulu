<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Services\TicketService;
use App\Repositories\TicketRepository;

class AdminTicketController {
    private TicketService $ticketService;
    private TicketRepository $ticketRepo;

    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
        $this->ticketService = new TicketService();
        $this->ticketRepo = new TicketRepository();
    }

    public function index() {
        $status = Request::get('status', '');
        $where = $status ? 't.status = ?' : '';
        $params = $status ? [$status] : [];
        $tickets = $this->ticketRepo->allWithDetails($where, $params);
        View::render('admin/tickets', compact('tickets', 'status'), 'admin');
    }

    public function show($id) {
        $ticket = $this->ticketRepo->findWithMessages($id);
        if (!$ticket) { Request::redirect('/admin/tickets'); }
        $messages = $this->ticketRepo->findMessages($id);
        View::render('admin/ticket-show', compact('ticket', 'messages'), 'admin');
    }

    public function assign($id) {
        $this->ticketService->assign($id, Request::post('expert_id'));
        Session::setFlash('success', 'Ticket assigné.');
        Request::redirect('/admin/tickets');
    }

    public function close($id) {
        $this->ticketService->close($id);
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
        $message = Request::post('message');
        if ($message) {
            $this->ticketService->reply($id, Session::get('user_id'), $message);
        }
        Session::setFlash('success', 'Réponse envoyée.');
        Request::redirect('/admin/tickets/' . $id);
    }
}
