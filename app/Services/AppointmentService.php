<?php
namespace App\Services;

use App\Repositories\AppointmentRepository;
use App\Repositories\BabyRepository;
use App\Repositories\PregnancyRepository;
use App\Repositories\UserRepository;

class AppointmentService {
    private AppointmentRepository $appointmentRepo;
    private NotificationService $notifService;
    private EmailService $emailService;
    private UserRepository $userRepo;

    public function __construct(
        ?AppointmentRepository $appointmentRepo = null,
        ?NotificationService $notifService = null,
        ?EmailService $emailService = null,
        ?UserRepository $userRepo = null
    ) {
        $this->appointmentRepo = $appointmentRepo ?? new AppointmentRepository();
        $this->notifService = $notifService ?? new NotificationService();
        $this->emailService = $emailService ?? new EmailService();
        $this->userRepo = $userRepo ?? new UserRepository();
    }

    public function book(int $motherId, int $expertId, string $date, string $type, ?string $notes): int {
        $id = $this->appointmentRepo->create([
            'mother_id' => $motherId,
            'expert_id' => $expertId,
            'appointment_date' => $date,
            'type' => $type,
            'notes' => $notes,
            'status' => 'pending',
        ]);
        $this->notifService->sendAppointmentBooked($expertId, $id);
        $mother = $this->userRepo->findByMotherId($motherId);
        $this->emailService->sendAppointmentBooked($expertId, $mother['name'] ?? 'Maman', $date, $type);
        return $id;
    }

    public function cancelByMother(int $appointmentId, int $motherId): bool {
        $appt = $this->appointmentRepo->findWithMother($appointmentId, $motherId);
        if (!$appt) return false;
        $this->appointmentRepo->updateStatus($appointmentId, 'cancelled');
        return true;
    }

    public function updateByExpert(int $appointmentId, int $expertId, string $action): bool {
        if (!in_array($action, ['confirmed', 'cancelled'], true)) return false;
        $appt = $this->appointmentRepo->findWithExpert($appointmentId, $expertId);
        if (!$appt) return false;
        $this->appointmentRepo->updateStatus($appointmentId, $action);
        $motherUserId = $this->appointmentRepo->getMotherUserId($appointmentId);
        if ($motherUserId) {
            $this->notifService->sendAppointmentUpdated($motherUserId, $action, $appointmentId);
            $date = $appt['appointment_date'] ?? '';
            $formattedDate = $date ? date('d/m/Y H:i', strtotime($date)) : '';
            $this->emailService->sendAppointmentUpdated($motherUserId, $action, $formattedDate);
        }
        return true;
    }
}
