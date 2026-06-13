<?php
namespace App\Services;

use App\Repositories\NotificationRepository;

class NotificationService {
    private NotificationRepository $notificationRepo;

    public function __construct(?NotificationRepository $notificationRepo = null) {
        $this->notificationRepo = $notificationRepo ?? new NotificationRepository();
    }

    public function send(int $userId, string $type, string $title, string $message, string $link): int {
        return $this->notificationRepo->createNotification($userId, $type, $title, $message, $link);
    }

    public function sendAppointmentBooked(int $expertId, int $appointmentId): void {
        $this->send(
            $expertId, 'info',
            'Nouveau rendez-vous',
            'Vous avez reçu une nouvelle demande de consultation.',
            '/expert/agenda'
        );
    }

    public function sendAppointmentUpdated(int $motherUserId, string $action, int $appointmentId): void {
        $label = $action === 'confirmed' ? 'confirmé' : 'annulé';
        $this->send(
            $motherUserId, 'info',
            "Rendez-vous {$label}",
            "Votre rendez-vous a été {$label} par l'expert.",
            '/dashboard/rendez-vous'
        );
    }

    public function sendNewMessage(int $receiverId, int $senderId, string $senderRole): void {
        $link = $senderRole === 'expert'
            ? '/dashboard/messagerie?partner_id=' . $senderId
            : '/expert/messagerie?partner_id=' . $senderId;
        $this->send(
            $receiverId, 'info',
            'Nouveau message',
            'Vous avez reçu un nouveau message.',
            $link
        );
    }

    public function sendQuestionAnswered(int $postAuthorId, int $postId): void {
        $this->send(
            $postAuthorId, 'info',
            'Réponse à votre question',
            'Un expert a répondu à votre question sur la communauté.',
            "/communaute/{$postId}"
        );
    }

    public function sendTicketAssigned(int $expertId, int $ticketId): void {
        $this->send(
            $expertId, 'info',
            'Ticket assigné',
            'Un nouveau ticket vous a été assigné.',
            "/tickets/{$ticketId}"
        );
    }

    public function sendTicketClosed(int $creatorId): void {
        $this->send(
            $creatorId, 'info',
            'Ticket fermé',
            'Votre ticket de support a été fermé.',
            '/dashboard/tickets'
        );
    }

    public function sendTicketReplied(int $creatorId): void {
        $this->send(
            $creatorId, 'info',
            'Réponse à votre ticket',
            "L'administrateur a répondu à votre ticket.",
            '/dashboard/tickets'
        );
    }

    public function sendExpertValidated(int $expertId): void {
        $this->send(
            $expertId, 'success',
            'Compte validé',
            "Votre compte expert a été validé par l'administrateur. Vous pouvez maintenant publier des articles et répondre aux mamans.",
            '/expert/dashboard'
        );
    }

    public function sendAccountSuspended(int $userId): void {
        $this->send(
            $userId, 'warning',
            'Compte suspendu',
            "Votre compte a été suspendu par l'administrateur.",
            '/auth/login'
        );
    }

    public function sendAccountActivated(int $userId): void {
        $this->send(
            $userId, 'success',
            'Compte réactivé',
            "Votre compte a été réactivé par l'administrateur.",
            '/auth/login'
        );
    }
}
