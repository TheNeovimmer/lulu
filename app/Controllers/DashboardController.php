<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;

class DashboardController extends Controller {
    public function __construct() {
        $this->layout = 'maman';
        $this->authCheck();
        if (Session::get('user_role_slug') !== 'maman') {
            header('Location: /auth/login');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $pregnancy = $db->fetch("SELECT * FROM pregnancies WHERE user_id = ?", [$userId]);
        $articles = $db->fetchAll("SELECT id, title, slug, created_at FROM articles WHERE status = 'published' ORDER BY created_at DESC LIMIT 5");
        $posts = $db->fetchAll("SELECT cp.*, u.name as author_name FROM community_posts cp LEFT JOIN users u ON cp.user_id = u.id ORDER BY cp.created_at DESC LIMIT 5");
        $notifCount = $db->fetch("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0", [$userId])['count'];

        $this->render('dashboard/index', compact('pregnancy', 'articles', 'posts', 'notifCount'));
    }

    public function profil() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        if (Request::isPost()) {
            $name = trim(Request::post('name'));
            $email = trim(Request::post('email'));
            $phone = trim(Request::post('phone'));
            $address = trim(Request::post('address'));

            $db->query(
                "UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?",
                [$name, $email, $phone, $address, $userId]
            );
            Session::set('user_name', $name);
            Session::setFlash('success', 'Profil mis à jour avec succès.');
            Request::back();
        }

        $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        $this->render('dashboard/profil', compact('user'));
    }

    public function grossesse() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        if (Request::isPost()) {
            $dueDate = Request::post('due_date');
            $weeksGestation = Request::post('weeks_gestation');
            $notes = Request::post('notes');

            $existing = $db->fetch("SELECT id FROM pregnancies WHERE user_id = ?", [$userId]);
            if ($existing) {
                $db->query(
                    "UPDATE pregnancies SET due_date = ?, weeks_gestation = ?, notes = ? WHERE user_id = ?",
                    [$dueDate, $weeksGestation, $notes, $userId]
                );
            } else {
                $db->insert(
                    "INSERT INTO pregnancies (user_id, due_date, weeks_gestation, notes) VALUES (?, ?, ?, ?)",
                    [$userId, $dueDate, $weeksGestation, $notes]
                );
            }
            Session::setFlash('success', 'Informations de grossesse mises à jour.');
            Request::back();
        }

        $pregnancy = $db->fetch("SELECT * FROM pregnancies WHERE user_id = ?", [$userId]);
        $this->render('dashboard/grossesse', compact('pregnancy'));
    }

    public function bebe() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        if (Request::isPost()) {
            $name = Request::post('name');
            $birthDate = Request::post('birth_date');
            $gender = Request::post('gender');
            $weight = Request::post('weight');
            $height = Request::post('height');

            $existing = $db->fetch("SELECT id FROM babies WHERE user_id = ?", [$userId]);
            if ($existing) {
                $db->query(
                    "UPDATE babies SET name = ?, birth_date = ?, gender = ?, weight = ?, height = ? WHERE user_id = ?",
                    [$name, $birthDate, $gender, $weight, $height, $userId]
                );
            } else {
                $db->insert(
                    "INSERT INTO babies (user_id, name, birth_date, gender, weight, height) VALUES (?, ?, ?, ?, ?, ?)",
                    [$userId, $name, $birthDate, $gender, $weight, $height]
                );
            }
            Session::setFlash('success', 'Informations du bébé mises à jour.');
            Request::back();
        }

        $baby = $db->fetch("SELECT * FROM babies WHERE user_id = ?", [$userId]);
        $this->render('dashboard/bebe', compact('baby'));
    }

    public function croissance() {
        if (!Request::isPost()) {
            Request::back();
        }

        $db = Database::getInstance();
        $db->insert(
            "INSERT INTO growth_records (baby_id, weight, height, head_circumference, measured_at) VALUES (?, ?, ?, ?, NOW())",
            [
                Request::post('baby_id'),
                Request::post('weight'),
                Request::post('height'),
                Request::post('head_circumference')
            ]
        );
        Session::setFlash('success', 'Mesure de croissance ajoutée.');
        Request::back();
    }

    public function vaccination() {
        if (!Request::isPost()) {
            Request::back();
        }

        $db = Database::getInstance();
        $db->insert(
            "INSERT INTO vaccinations (baby_id, vaccine_name, scheduled_date, administered_date, notes) VALUES (?, ?, ?, ?, ?)",
            [
                Request::post('baby_id'),
                Request::post('vaccine_name'),
                Request::post('scheduled_date'),
                Request::post('administered_date'),
                Request::post('notes')
            ]
        );
        Session::setFlash('success', 'Vaccin enregistré.');
        Request::back();
    }

    public function tickets() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        if (Request::isPost()) {
            $subject = trim(Request::post('subject'));
            $message = trim(Request::post('message'));

            $ticketId = $db->insert(
                "INSERT INTO tickets (user_id, subject, status, priority, created_at) VALUES (?, ?, 'open', 'normal', NOW())",
                [$userId, $subject]
            );
            $db->insert(
                "INSERT INTO ticket_messages (ticket_id, user_id, message, created_at) VALUES (?, ?, ?, NOW())",
                [$ticketId, $userId, $message]
            );
            Session::setFlash('success', 'Ticket créé avec succès.');
            Request::redirect('/dashboard/tickets');
        }

        $tickets = $db->fetchAll(
            "SELECT t.*, (SELECT COUNT(*) FROM ticket_messages WHERE ticket_id = t.id) as message_count FROM tickets t WHERE t.user_id = ? ORDER BY t.created_at DESC",
            [$userId]
        );
        $this->render('dashboard/tickets', compact('tickets'));
    }

    public function notifications() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $db->query("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0", [$userId]);
        $notifications = $db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
        $this->render('dashboard/notifications', compact('notifications'));
    }
}
