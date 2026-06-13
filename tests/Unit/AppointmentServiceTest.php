<?php
namespace Tests\Unit;

use App\Repositories\AppointmentRepository;
use App\Repositories\UserRepository;
use App\Services\AppointmentService;
use App\Services\EmailService;
use App\Services\NotificationService;
use PHPUnit\Framework\TestCase;

class AppointmentServiceTest extends TestCase {
    private AppointmentRepository $appointmentRepo;
    private NotificationService $notifService;
    private EmailService $emailService;
    private UserRepository $userRepo;
    private AppointmentService $service;

    protected function setUp(): void {
        $this->appointmentRepo = $this->createMock(AppointmentRepository::class);
        $this->notifService = $this->createMock(NotificationService::class);
        $this->emailService = $this->createMock(EmailService::class);
        $this->userRepo = $this->createMock(UserRepository::class);
        $this->service = new AppointmentService($this->appointmentRepo, $this->notifService, $this->emailService, $this->userRepo);
    }

    public function testBookCreatesAppointmentAndSendsNotification(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['mother_id'] === 1
                    && $data['expert_id'] === 2
                    && $data['status'] === 'pending'
                    && $data['type'] === 'online';
            }))
            ->willReturn(5);

        $this->notifService->expects($this->once())
            ->method('sendAppointmentBooked')
            ->with(2, 5);

        $id = $this->service->book(1, 2, '2026-06-15 10:00', 'online', 'Test');
        $this->assertSame(5, $id);
    }

    public function testCancelByMotherReturnsTrueWhenOwned(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findWithMother')
            ->with(1, 3)
            ->willReturn(['id' => 1, 'mother_id' => 3, 'status' => 'pending']);

        $this->appointmentRepo->expects($this->once())
            ->method('updateStatus')
            ->with(1, 'cancelled');

        $result = $this->service->cancelByMother(1, 3);
        $this->assertTrue($result);
    }

    public function testCancelByMotherReturnsFalseWhenNotOwned(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findWithMother')
            ->with(1, 99)
            ->willReturn(null);

        $this->appointmentRepo->expects($this->never())
            ->method('updateStatus');

        $result = $this->service->cancelByMother(1, 99);
        $this->assertFalse($result);
    }

    public function testUpdateByExpertReturnsFalseOnInvalidAction(): void {
        $this->appointmentRepo->expects($this->never())
            ->method('findWithExpert');

        $result = $this->service->updateByExpert(1, 2, 'invalid_action');
        $this->assertFalse($result);
    }

    public function testUpdateByExpertReturnsFalseWhenNotOwned(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findWithExpert')
            ->with(1, 99)
            ->willReturn(null);

        $result = $this->service->updateByExpert(1, 99, 'confirmed');
        $this->assertFalse($result);
    }

    public function testUpdateByExpertConfirmSendsNotification(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findWithExpert')
            ->with(1, 2)
            ->willReturn(['id' => 1, 'expert_id' => 2, 'status' => 'pending']);

        $this->appointmentRepo->expects($this->once())
            ->method('updateStatus')
            ->with(1, 'confirmed');

        $this->appointmentRepo->expects($this->once())
            ->method('getMotherUserId')
            ->with(1)
            ->willReturn(5);

        $this->notifService->expects($this->once())
            ->method('sendAppointmentUpdated')
            ->with(5, 'confirmed', 1);

        $result = $this->service->updateByExpert(1, 2, 'confirmed');
        $this->assertTrue($result);
    }

    public function testUpdateByExpertCancelSendsNotification(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findWithExpert')
            ->with(1, 2)
            ->willReturn(['id' => 1, 'expert_id' => 2, 'status' => 'pending']);

        $this->appointmentRepo->expects($this->once())
            ->method('updateStatus')
            ->with(1, 'cancelled');

        $this->appointmentRepo->expects($this->once())
            ->method('getMotherUserId')
            ->with(1)
            ->willReturn(5);

        $this->notifService->expects($this->once())
            ->method('sendAppointmentUpdated')
            ->with(5, 'cancelled', 1);

        $result = $this->service->updateByExpert(1, 2, 'cancelled');
        $this->assertTrue($result);
    }

    public function testUpdateByExpertSkipsNotificationWhenNoMotherUser(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findWithExpert')
            ->with(1, 2)
            ->willReturn(['id' => 1, 'expert_id' => 2, 'status' => 'pending']);

        $this->appointmentRepo->expects($this->once())
            ->method('updateStatus')
            ->with(1, 'confirmed');

        $this->appointmentRepo->expects($this->once())
            ->method('getMotherUserId')
            ->with(1)
            ->willReturn(null);

        $this->notifService->expects($this->never())
            ->method('sendAppointmentUpdated');

        $result = $this->service->updateByExpert(1, 2, 'confirmed');
        $this->assertTrue($result);
    }
}
