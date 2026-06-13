<?php
namespace App\Services;

use App\Repositories\UserRepository;

class EmailService {
    private UserRepository $userRepo;
    private string $fromEmail;
    private string $fromName;

    public function __construct(?UserRepository $userRepo = null) {
        $this->userRepo = $userRepo ?? new UserRepository();
        $this->fromEmail = 'noreply@luma.tn';
        $this->fromName = 'LUMA - La où commence le soin';
    }

    public function send(int|string $to, string $subject, string $htmlBody): bool {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'X-Mailer: LUMA/' . phpversion(),
        ];

        $message = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Inter,system-ui,-apple-system,sans-serif;background:#f4f4f6">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f6;padding:40px 20px">
<tr><td align="center">
<table role="presentation" width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.08)">
<tr><td style="padding:32px 40px;background:#c94b72;text-align:center">
<h1 style="margin:0;color:#fff;font-size:22px;font-weight:700">LUMA</h1>
<p style="margin:4px 0 0;color:rgba(255,255,255,0.85);font-size:13px">La où commence le soin</p>
</td></tr>
<tr><td style="padding:32px 40px">
{$htmlBody}
</td></tr>
<tr><td style="padding:20px 40px;background:#f8f9fc;border-top:1px solid #e9ecef;text-align:center">
<p style="margin:0;font-size:12px;color:#6b7280">
Cet email a été envoyé automatiquement. Merci de ne pas y répondre.<br>
LUMA &copy; 2026
</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
HTML;

        $headersStr = implode("\r\n", $headers) . "\r\n";

        return mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, $headersStr);
    }

    public function sendToUser(int $userId, string $subject, string $htmlBody): bool {
        $user = $this->userRepo->findById($userId);
        if (!$user || empty($user['email'])) return false;
        return $this->send($user['email'], $subject, $htmlBody);
    }

    public function sendAppointmentBooked(int $expertId, string $motherName, string $date, string $type): void {
        $typeLabel = $type === 'online' ? 'Téléconsultation' : 'Consultation en cabinet';
        $body = <<<BODY
<p style="font-size:15px;color:#374151;margin:0 0 16px">Bonjour,</p>
<p style="font-size:14px;color:#374151;margin:0 0 16px;line-height:1.6">
Vous avez reçu une nouvelle demande de consultation de la part de <strong>{$motherName}</strong>.
</p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fc;border-radius:8px;margin:0 0 16px">
<tr><td style="padding:16px 20px">
<p style="margin:0 0 4px;font-size:13px;color:#6b7280">Date</p>
<p style="margin:0 0 12px;font-size:15px;color:#111827;font-weight:600">{$date}</p>
<p style="margin:0 0 4px;font-size:13px;color:#6b7280">Type</p>
<p style="margin:0;font-size:15px;color:#111827;font-weight:600">{$typeLabel}</p>
</td></tr>
</table>
<p style="font-size:14px;color:#374151;margin:0 0 16px;line-height:1.6">
Connectez-vous à votre espace expert pour confirmer ou annuler ce rendez-vous.
</p>
<a href="https://luma.ddev.site/expert/agenda" style="display:inline-block;padding:12px 24px;background:#c94b72;color:#fff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:600">Voir mon agenda</a>
BODY;
        $this->sendToUser($expertId, 'Nouvelle demande de consultation', $body);
    }

    public function sendAppointmentUpdated(int $motherUserId, string $action, string $date): void {
        $label = $action === 'confirmed' ? 'confirmé' : 'annulé';
        $body = <<<BODY
<p style="font-size:15px;color:#374151;margin:0 0 16px">Bonjour,</p>
<p style="font-size:14px;color:#374151;margin:0 0 16px;line-height:1.6">
Votre rendez-vous du <strong>{$date}</strong> a été <strong>{$label}</strong> par l'expert.
</p>
<p style="font-size:14px;color:#374151;margin:0;line-height:1.6">
Connectez-vous à votre espace pour voir vos rendez-vous.
</p>
BODY;
        $this->sendToUser($motherUserId, "Rendez-vous {$label}", $body);
    }

    public function sendTicketAssigned(int $expertId, int $ticketId, string $subject): void {
        $body = <<<BODY
<p style="font-size:15px;color:#374151;margin:0 0 16px">Bonjour,</p>
<p style="font-size:14px;color:#374151;margin:0 0 16px;line-height:1.6">
Un nouveau ticket de support vous a été assigné :
</p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fc;border-radius:8px;margin:0 0 16px">
<tr><td style="padding:16px 20px">
<p style="margin:0 0 4px;font-size:13px;color:#6b7280">Sujet</p>
<p style="margin:0;font-size:15px;color:#111827;font-weight:600">{$subject}</p>
</td></tr>
</table>
<a href="https://luma.ddev.site/tickets/{$ticketId}" style="display:inline-block;padding:12px 24px;background:#c94b72;color:#fff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:600">Voir le ticket</a>
BODY;
        $this->sendToUser($expertId, 'Nouveau ticket assigné', $body);
    }

    public function sendTicketReplied(int $creatorId, int $ticketId): void {
        $body = <<<BODY
<p style="font-size:15px;color:#374151;margin:0 0 16px">Bonjour,</p>
<p style="font-size:14px;color:#374151;margin:0 0 16px;line-height:1.6">
Une réponse a été apportée à votre ticket de support.
</p>
<a href="https://luma.ddev.site/dashboard/tickets" style="display:inline-block;padding:12px 24px;background:#c94b72;color:#fff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:600">Voir la réponse</a>
BODY;
        $this->sendToUser($creatorId, 'Réponse à votre ticket', $body);
    }

    public function sendTicketClosed(int $creatorId, int $ticketId): void {
        $body = <<<BODY
<p style="font-size:15px;color:#374151;margin:0 0 16px">Bonjour,</p>
<p style="font-size:14px;color:#374151;margin:0 0 16px;line-height:1.6">
Votre ticket de support a été fermé. Si vous avez besoin d'aide supplémentaire, n'hésitez pas à créer un nouveau ticket.
</p>
<a href="https://luma.ddev.site/dashboard/tickets" style="display:inline-block;padding:12px 24px;background:#c94b72;color:#fff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:600">Voir mes tickets</a>
BODY;
        $this->sendToUser($creatorId, 'Ticket fermé', $body);
    }

    public function sendNewMessage(int $receiverId, string $senderName): void {
        $body = <<<BODY
<p style="font-size:15px;color:#374151;margin:0 0 16px">Bonjour,</p>
<p style="font-size:14px;color:#374151;margin:0 0 16px;line-height:1.6">
Vous avez reçu un nouveau message de <strong>{$senderName}</strong>.
</p>
<p style="font-size:14px;color:#374151;margin:0;line-height:1.6">
Connectez-vous à votre espace pour lire et répondre à ce message.
</p>
BODY;
        $this->sendToUser($receiverId, 'Nouveau message', $body);
    }

    public function sendExpertValidated(int $expertId, string $expertName): void {
        $body = <<<BODY
<p style="font-size:15px;color:#374151;margin:0 0 16px">Bonjour {$expertName},</p>
<p style="font-size:14px;color:#374151;margin:0 0 16px;line-height:1.6">
Votre compte expert a été validé par l'administrateur. Vous pouvez dès maintenant :
</p>
<ul style="font-size:14px;color:#374151;line-height:1.8;padding-left:20px;margin:0 0 16px">
<li>Publier des articles</li>
<li>Répondre aux questions des mamans</li>
<li>Partager des ressources</li>
</ul>
<a href="https://luma.ddev.site/expert/dashboard" style="display:inline-block;padding:12px 24px;background:#c94b72;color:#fff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:600">Accéder à mon espace</a>
BODY;
        $this->sendToUser($expertId, 'Compte validé', $body);
    }
}
