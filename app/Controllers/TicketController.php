<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Services\TicketService;
use App\Repositories\TicketRepository;

class TicketController extends Controller {
    private TicketService $ticketService;
    private TicketRepository $ticketRepo;

    public function __construct() {
        $this->layout = 'front';
        $this->authCheck();
        $this->ticketService = new TicketService();
        $this->ticketRepo = new TicketRepository();
    }

    public function index() {
        $role = Session::get('user_role_slug');
        $userId = Session::get('user_id');
        if ($role === 'maman') {
            $tickets = $this->ticketRepo->findByUser($userId);
        } elseif (in_array($role, ['expert', 'ctt'])) {
            $tickets = $this->ticketRepo->allWithDetails('t.assigned_to = ?', [$userId]);
        } else {
            $tickets = $this->ticketRepo->allWithDetails();
        }
        $this->render('tickets/index', compact('tickets'));
    }

    public function show($id) {
        $ticket = $this->ticketRepo->findWithMessages($id);
        if (!$ticket) { $this->render('errors/404'); return; }
        $role = Session::get('user_role_slug');
        if ($role === 'maman' && $ticket['user_id'] != Session::get('user_id')) {
            $this->render('errors/404'); return;
        }
        if ($role === 'expert' && $ticket['assigned_to'] != Session::get('user_id')) {
            $this->render('errors/404'); return;
        }
        $messages = $this->ticketRepo->findMessages($id);
        $this->render('tickets/show', compact('ticket', 'messages'));
    }

    public function create() {
        $ticketId = $this->ticketService->create(
            Session::get('user_id'),
            trim(Request::post('subject')),
            trim(Request::post('message')),
            Request::post('priority') ?: 'medium'
        );
        Session::setFlash('success', 'Ticket créé.');
        Request::redirect('/tickets/' . $ticketId);
    }

    public function reply($id) {
        $message = trim(Request::post('message'));
        if ($message) {
            $this->ticketService->reply($id, Session::get('user_id'), $message);
        }
        Request::redirect('/tickets/' . $id);
    }
}
