<?php
namespace App\Services;

use App\Repositories\TicketRepository;

class TicketService {
    private TicketRepository $ticketRepo;
    private NotificationService $notifService;
    private EmailService $emailService;

    public function __construct(
        ?TicketRepository $ticketRepo = null,
        ?NotificationService $notifService = null,
        ?EmailService $emailService = null
    ) {
        $this->ticketRepo = $ticketRepo ?? new TicketRepository();
        $this->notifService = $notifService ?? new NotificationService();
        $this->emailService = $emailService ?? new EmailService();
    }

    public function create(int $userId, string $subject, string $message, string $priority = 'medium'): int {
        $ticketId = $this->ticketRepo->create([
            'user_id' => $userId,
            'subject' => $subject,
            'message' => $message,
            'priority' => $priority,
            'status' => 'open',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $this->ticketRepo->addMessage($ticketId, $userId, $message);
        return $ticketId;
    }

    public function assign(int $ticketId, int $expertId): void {
        $ticket = $this->ticketRepo->findById($ticketId);
        $this->ticketRepo->assign($ticketId, $expertId);
        $this->notifService->sendTicketAssigned($expertId, $ticketId);
        $this->emailService->sendTicketAssigned($expertId, $ticketId, $ticket['subject'] ?? '');
    }

    public function close(int $ticketId): void {
        $creatorId = $this->ticketRepo->getTicketCreatorId($ticketId);
        $this->ticketRepo->close($ticketId);
        if ($creatorId) {
            $this->notifService->sendTicketClosed($creatorId);
            $this->emailService->sendTicketClosed($creatorId, $ticketId);
        }
    }

    public function reply(int $ticketId, int $userId, string $message): void {
        $this->ticketRepo->addMessage($ticketId, $userId, $message);
        $ticket = $this->ticketRepo->findById($ticketId);
        if ($ticket && $ticket['user_id'] != $userId) {
            $this->notifService->sendTicketReplied($ticket['user_id']);
            $this->emailService->sendTicketReplied($ticket['user_id'], $ticketId);
        }
    }

    public function updateStatus(int $ticketId, string $status): void {
        $this->ticketRepo->update($ticketId, ['status' => $status]);
    }
}
