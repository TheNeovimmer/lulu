<?php
namespace Tests\Unit;

use App\Repositories\TicketRepository;
use App\Services\EmailService;
use App\Services\NotificationService;
use App\Services\TicketService;
use PHPUnit\Framework\TestCase;

class TicketServiceTest extends TestCase {
    private TicketRepository $ticketRepo;
    private NotificationService $notifService;
    private EmailService $emailService;
    private TicketService $service;

    protected function setUp(): void {
        $this->ticketRepo = $this->createMock(TicketRepository::class);
        $this->notifService = $this->createMock(NotificationService::class);
        $this->emailService = $this->createMock(EmailService::class);
        $this->service = new TicketService($this->ticketRepo, $this->notifService, $this->emailService);
    }

    public function testCreateCreatesTicketAndAddsFirstMessage(): void {
        $this->ticketRepo->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['user_id'] === 1
                    && $data['subject'] === 'Problème'
                    && $data['status'] === 'open'
                    && $data['priority'] === 'high';
            }))
            ->willReturn(5);

        $this->ticketRepo->expects($this->once())
            ->method('addMessage')
            ->with(5, 1, 'Détails du problème');

        $id = $this->service->create(1, 'Problème', 'Détails du problème', 'high');
        $this->assertSame(5, $id);
    }

    public function testCreateUsesDefaultPriority(): void {
        $this->ticketRepo->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['priority'] === 'medium';
            }))
            ->willReturn(6);

        $this->ticketRepo->expects($this->once())
            ->method('addMessage');

        $this->service->create(1, 'Test', 'Message');
    }

    public function testAssignUpdatesTicketAndSendsNotification(): void {
        $this->ticketRepo->expects($this->once())
            ->method('assign')
            ->with(1, 3);

        $this->notifService->expects($this->once())
            ->method('sendTicketAssigned')
            ->with(3, 1);

        $this->service->assign(1, 3);
    }

    public function testCloseUpdatesTicketAndNotifiesCreator(): void {
        $this->ticketRepo->expects($this->once())
            ->method('getTicketCreatorId')
            ->with(1)
            ->willReturn(5);

        $this->ticketRepo->expects($this->once())
            ->method('close')
            ->with(1);

        $this->notifService->expects($this->once())
            ->method('sendTicketClosed')
            ->with(5);

        $this->service->close(1);
    }

    public function testCloseSkipsNotificationWhenNoCreator(): void {
        $this->ticketRepo->expects($this->once())
            ->method('getTicketCreatorId')
            ->with(1)
            ->willReturn(null);

        $this->ticketRepo->expects($this->once())
            ->method('close')
            ->with(1);

        $this->notifService->expects($this->never())
            ->method('sendTicketClosed');

        $this->service->close(1);
    }

    public function testReplyByDifferentUserSendsNotification(): void {
        $this->ticketRepo->expects($this->once())
            ->method('addMessage')
            ->with(1, 3, 'Réponse');

        $this->ticketRepo->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn(['id' => 1, 'user_id' => 1, 'status' => 'open']);

        $this->notifService->expects($this->once())
            ->method('sendTicketReplied')
            ->with(1);

        $this->service->reply(1, 3, 'Réponse');
    }

    public function testReplyByCreatorDoesNotSendNotification(): void {
        $this->ticketRepo->expects($this->once())
            ->method('addMessage')
            ->with(1, 1, 'Suivi');

        $this->ticketRepo->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn(['id' => 1, 'user_id' => 1, 'status' => 'open']);

        $this->notifService->expects($this->never())
            ->method('sendTicketReplied');

        $this->service->reply(1, 1, 'Suivi');
    }

    public function testUpdateStatusDelegates(): void {
        $this->ticketRepo->expects($this->once())
            ->method('update')
            ->with(1, ['status' => 'in_progress']);

        $this->service->updateStatus(1, 'in_progress');
    }
}
