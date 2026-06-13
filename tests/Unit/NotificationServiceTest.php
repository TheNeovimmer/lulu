<?php
namespace Tests\Unit;

use App\Repositories\NotificationRepository;
use App\Services\NotificationService;
use PHPUnit\Framework\TestCase;

class NotificationServiceTest extends TestCase {
    private NotificationRepository $repo;
    private NotificationService $service;

    protected function setUp(): void {
        $this->repo = $this->createMock(NotificationRepository::class);
        $this->service = new NotificationService($this->repo);
    }

    public function testSendReturnsId(): void {
        $this->repo->expects($this->once())
            ->method('createNotification')
            ->with(1, 'info', 'Test', 'Message', '/test')
            ->willReturn(42);

        $id = $this->service->send(1, 'info', 'Test', 'Message', '/test');
        $this->assertSame(42, $id);
    }

    public function testSendAppointmentBookedCreatesNotification(): void {
        $this->repo->expects($this->once())
            ->method('createNotification')
            ->with(2, 'info', 'Nouveau rendez-vous', $this->stringContains('consultation'), '/expert/agenda');

        $this->service->sendAppointmentBooked(2, 1);
    }

    public function testSendAppointmentUpdatedConfirmed(): void {
        $this->repo->expects($this->once())
            ->method('createNotification')
            ->with(1, 'info', $this->stringContains('confirmé'), $this->stringContains('confirmé'), '/dashboard/rendez-vous');

        $this->service->sendAppointmentUpdated(1, 'confirmed', 5);
    }

    public function testSendAppointmentUpdatedCancelled(): void {
        $this->repo->expects($this->once())
            ->method('createNotification')
            ->with(1, 'info', $this->stringContains('annulé'), $this->stringContains('annulé'), '/dashboard/rendez-vous');

        $this->service->sendAppointmentUpdated(1, 'cancelled', 5);
    }

    public function testSendNewMessageExpertToMother(): void {
        $this->repo->expects($this->once())
            ->method('createNotification')
            ->with(1, 'info', 'Nouveau message', $this->anything(), '/dashboard/messagerie?partner_id=3');

        $this->service->sendNewMessage(1, 3, 'expert');
    }

    public function testSendNewMessageMotherToExpert(): void {
        $this->repo->expects($this->once())
            ->method('createNotification')
            ->with(2, 'info', 'Nouveau message', $this->anything(), '/expert/messagerie?partner_id=1');

        $this->service->sendNewMessage(2, 1, 'maman');
    }

    public function testSendQuestionAnswered(): void {
        $this->repo->expects($this->once())
            ->method('createNotification')
            ->with(1, 'info', 'Réponse à votre question', $this->anything(), '/communaute/10');

        $this->service->sendQuestionAnswered(1, 10);
    }

    public function testSendTicketAssigned(): void {
        $this->repo->expects($this->once())
            ->method('createNotification')
            ->with(2, 'info', 'Ticket assigné', $this->anything(), '/tickets/7');

        $this->service->sendTicketAssigned(2, 7);
    }

    public function testSendTicketClosed(): void {
        $this->repo->expects($this->once())
            ->method('createNotification')
            ->with(1, 'info', 'Ticket fermé', $this->anything(), '/dashboard/tickets');

        $this->service->sendTicketClosed(1);
    }

    public function testSendTicketReplied(): void {
        $this->repo->expects($this->once())
            ->method('createNotification')
            ->with(1, 'info', 'Réponse à votre ticket', $this->anything(), '/dashboard/tickets');

        $this->service->sendTicketReplied(1);
    }

    public function testSendExpertValidated(): void {
        $this->repo->expects($this->once())
            ->method('createNotification')
            ->with(3, 'success', 'Compte validé', $this->stringContains('validé'), '/expert/dashboard');

        $this->service->sendExpertValidated(3);
    }

    public function testSendAccountSuspended(): void {
        $this->repo->expects($this->once())
            ->method('createNotification')
            ->with(1, 'warning', 'Compte suspendu', $this->stringContains('suspendu'), '/auth/login');

        $this->service->sendAccountSuspended(1);
    }

    public function testSendAccountActivated(): void {
        $this->repo->expects($this->once())
            ->method('createNotification')
            ->with(1, 'success', 'Compte réactivé', $this->stringContains('réactivé'), '/auth/login');

        $this->service->sendAccountActivated(1);
    }
}
