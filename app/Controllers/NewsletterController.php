<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;

class NewsletterController {
    public function subscribe() {
        Session::validate_csrf();

        $email = trim(Request::post('email'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Veuillez fournir une adresse email valide.');
            Request::back();
        }

        $db = Database::getInstance();

        $existing = $db->fetch("SELECT id FROM newsletters WHERE email = ?", [$email]);
        if ($existing) {
            Session::setFlash('info', 'Vous êtes déjà inscrit à notre newsletter.');
            Request::back();
        }

        $db->insert(
            "INSERT INTO newsletters (email, ip_address, is_active, created_at) VALUES (?, ?, 1, NOW())",
            [$email, $_SERVER['REMOTE_ADDR'] ?? '']
        );

        Session::setFlash('success', 'Inscription à la newsletter réussie !');
        Request::back();
    }

    public function unsubscribe() {
        Session::validate_csrf();

        $email = trim(Request::post('email'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Veuillez fournir une adresse email valide.');
            Request::back();
        }

        $db = Database::getInstance();
        $db->query("UPDATE newsletters SET is_active = 0 WHERE email = ?", [$email]);

        Session::setFlash('success', 'Désabonnement réussi.');
        Request::back();
    }
}
