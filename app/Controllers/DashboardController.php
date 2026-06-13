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

    private function getMotherId($userId) {
        $db = Database::getInstance();
        $mother = $db->fetch("SELECT id FROM mothers WHERE user_id = ?", [$userId]);
        if (!$mother) {
            $motherId = $db->insert("INSERT INTO mothers (user_id) VALUES (?)", [$userId]);
            return $motherId;
        }
        return $mother['id'];
    }

    public function index() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId($userId);

        // Fetch user info
        $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        $nameParts = explode(' ', $user['name'], 2);
        $user['first_name'] = $nameParts[0] ?? '';

        // Fetch pregnancy info and calculate gestational details
        $pregnancy = $db->fetch("SELECT * FROM pregnancies WHERE mother_id = ? AND status = 'active'", [$motherId]);
        if ($pregnancy) {
            $dueDate = new \DateTime($pregnancy['due_date']);
            $now = new \DateTime();
            $interval = $now->diff($dueDate);
            $pregnancy['days_remaining'] = $dueDate > $now ? $interval->days : 0;

            // Average human gestation LMP starts 280 days before due date
            $startDate = clone $dueDate;
            $startDate->modify('-280 days');
            if ($now < $startDate) {
                $weeks = 0;
            } else {
                $daysGestation = $startDate->diff($now)->days;
                $weeks = min(40, floor($daysGestation / 7));
            }
            $pregnancy['weeks'] = $weeks;
        }

        // Fetch baby info and calculate age and latest growth records
        $baby = $db->fetch(
            "SELECT b.*, g.weight as last_weight, g.height as last_height 
             FROM babies b 
             LEFT JOIN growth_records g ON b.id = g.baby_id 
             AND g.record_date = (SELECT MAX(record_date) FROM growth_records WHERE baby_id = b.id)
             WHERE b.mother_id = ? 
             LIMIT 1",
            [$motherId]
        );
        if ($baby) {
            $birthDate = new \DateTime($baby['date_of_birth']);
            $now = new \DateTime();
            $diff = $birthDate->diff($now);
            $baby['age_months'] = ($diff->y * 12) + $diff->m;
            $baby['last_weight'] = $baby['last_weight'] ?: 0.0;
            $baby['last_height'] = $baby['last_height'] ?: 0.0;
        }

        $articles = $db->fetchAll("SELECT id, title, slug, created_at FROM articles WHERE status = 'published' ORDER BY created_at DESC LIMIT 5");
        $posts = $db->fetchAll("SELECT cp.*, u.name as author_name FROM community_posts cp LEFT JOIN users u ON cp.user_id = u.id ORDER BY cp.created_at DESC LIMIT 5");
        $notifCount = $db->fetch("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0", [$userId])['count'];

        $this->render('dashboard/index', compact('user', 'pregnancy', 'baby', 'articles', 'posts', 'notifCount'));
    }

    public function profil() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId($userId);

        if (Request::isPost()) {
            $firstName = trim(Request::post('first_name'));
            $lastName = trim(Request::post('last_name'));
            $email = trim(Request::post('email'));
            $phone = trim(Request::post('phone'));
            $dob = Request::post('date_of_birth') ?: null;

            $fullName = trim($firstName . ' ' . $lastName);

            $db->query(
                "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?",
                [$fullName, $email, $phone, $userId]
            );

            $avatar = \App\Helpers\Avatar::upload($_FILES['avatar'] ?? []);
            if ($avatar) {
                $db->query("UPDATE users SET avatar = ? WHERE id = ?", [$avatar, $userId]);
                Session::set('user_avatar', '/uploads/avatars/' . $avatar);
            }

            $db->query(
                "UPDATE mothers SET date_of_birth = ? WHERE id = ?",
                [$dob, $motherId]
            );

            Session::set('user_name', $fullName);
            Session::set('user_email', $email);
            Session::setFlash('success', 'Profil mis à jour avec succès.');
            Request::back();
        }

        $user = $db->fetch(
            "SELECT u.*, m.date_of_birth 
             FROM users u 
             LEFT JOIN mothers m ON u.id = m.user_id 
             WHERE u.id = ?",
            [$userId]
        );

        $nameParts = explode(' ', $user['name'], 2);
        $user['first_name'] = $nameParts[0] ?? '';
        $user['last_name'] = $nameParts[1] ?? '';

        $this->render('dashboard/profil', compact('user'));
    }

    public function updateProfil() { return $this->profil(); }

    public function grossesse() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId($userId);

        if (Request::isPost()) {
            $dueDate = Request::post('due_date');
            $notes = Request::post('notes');

            $existing = $db->fetch("SELECT id FROM pregnancies WHERE mother_id = ?", [$motherId]);
            if ($existing) {
                $db->query(
                    "UPDATE pregnancies SET due_date = ?, notes = ? WHERE mother_id = ?",
                    [$dueDate, $notes, $motherId]
                );
            } else {
                $db->insert(
                    "INSERT INTO pregnancies (mother_id, due_date, notes, status) VALUES (?, ?, ?, 'active')",
                    [$motherId, $dueDate, $notes]
                );
            }
            Session::setFlash('success', 'Informations de grossesse mises à jour.');
            Request::back();
        }

        $pregnancy = $db->fetch("SELECT * FROM pregnancies WHERE mother_id = ? AND status = 'active'", [$motherId]);
        if ($pregnancy) {
            $dueDate = new \DateTime($pregnancy['due_date']);
            $now = new \DateTime();
            $interval = $now->diff($dueDate);
            $pregnancy['days_remaining'] = $dueDate > $now ? $interval->days : 0;

            $startDate = clone $dueDate;
            $startDate->modify('-280 days');
            if ($now < $startDate) {
                $weeks = 0;
            } else {
                $daysGestation = $startDate->diff($now)->days;
                $weeks = min(40, floor($daysGestation / 7));
            }
            $pregnancy['weeks'] = $weeks;
        }

        $this->render('dashboard/grossesse', compact('pregnancy'));
    }

    public function updateGrossesse() { return $this->grossesse(); }

    public function bebe() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId($userId);

        if (Request::isPost()) {
            $name = Request::post('name');
            $birthDate = Request::post('birth_date');
            $gender = Request::post('gender'); // 'girl' or 'boy' or 'other'
            $weight = Request::post('weight');
            $height = Request::post('height');

            $existing = $db->fetch("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);
            if ($existing) {
                $babyId = $existing['id'];
                $db->query(
                    "UPDATE babies SET name = ?, date_of_birth = ?, gender = ? WHERE id = ?",
                    [$name, $birthDate, $gender, $babyId]
                );
            } else {
                $babyId = $db->insert(
                    "INSERT INTO babies (mother_id, name, date_of_birth, gender) VALUES (?, ?, ?, ?)",
                    [$motherId, $name, $birthDate, $gender]
                );
            }

            if ($weight || $height) {
                $db->insert(
                    "INSERT INTO growth_records (baby_id, record_date, weight, height) VALUES (?, CURRENT_DATE(), ?, ?)",
                    [$babyId, $weight ?: null, $height ?: null]
                );
            }

            Session::setFlash('success', 'Informations du bébé mises à jour.');
            Request::back();
        }

        $baby = $db->fetch(
            "SELECT b.*, g.weight, g.height 
             FROM babies b 
             LEFT JOIN growth_records g ON b.id = g.baby_id 
             AND g.record_date = (SELECT MAX(record_date) FROM growth_records WHERE baby_id = b.id)
             WHERE b.mother_id = ? 
             LIMIT 1",
            [$motherId]
        );

        $memories = [];
        $milestones = [];
        if ($baby) {
            $memories = $db->fetchAll("SELECT * FROM baby_memories WHERE baby_id = ? ORDER BY event_date DESC", [$baby['id']]);
            $milestones = $db->fetchAll("SELECT * FROM baby_milestones WHERE baby_id = ?", [$baby['id']]);
            $milestones = array_column($milestones, 'achieved_date', 'milestone_key');
        }

        $this->render('dashboard/bebe', compact('baby', 'memories', 'milestones'));
    }

    public function updateBebe() { return $this->bebe(); }

    public function memories() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId($userId);

        $baby = $db->fetch("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);
        if (!$baby) {
            Session::setFlash('error', 'Veuillez ajouter un bébé d\'abord.');
            Request::back();
        }

        $title = trim(Request::post('title'));
        $content = trim(Request::post('content'));
        $date = Request::post('event_date') ?: date('Y-m-d');

        $db->insert(
            "INSERT INTO baby_memories (baby_id, title, content, event_date) VALUES (?, ?, ?, ?)",
            [$baby['id'], $title, $content, $date]
        );

        Session::setFlash('success', 'Souvenir ajouté avec succès.');
        Request::back();
    }

    public function deleteMemory($id) {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId($userId);
        $baby = $db->fetch("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);

        if ($baby) {
            $db->query("DELETE FROM baby_memories WHERE id = ? AND baby_id = ?", [$id, $baby['id']]);
            Session::setFlash('success', 'Souvenir supprimé.');
        }
        Request::back();
    }

    public function updateMilestones() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId($userId);

        $baby = $db->fetch("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);
        if (!$baby) {
            Session::setFlash('error', 'Veuillez ajouter un bébé d\'abord.');
            Request::back();
        }

        $milestones = Request::post('milestones') ?: [];
        
        // Remove existing milestone records
        $db->query("DELETE FROM baby_milestones WHERE baby_id = ?", [$baby['id']]);

        // Insert new selected milestones
        foreach ($milestones as $key => $achieved) {
            if ($achieved) {
                $db->insert(
                    "INSERT INTO baby_milestones (baby_id, milestone_key, achieved_date) VALUES (?, ?, ?)",
                    [$baby['id'], $key, date('Y-m-d')]
                );
            }
        }

        Session::setFlash('success', 'Étapes de développement mises à jour.');
        Request::back();
    }

    public function croissance() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId($userId);

        $baby = $db->fetch("SELECT * FROM babies WHERE mother_id = ?", [$motherId]);
        $records = [];
        if ($baby) {
            $records = $db->fetchAll(
                "SELECT *, DATEDIFF(record_date, ?) as age_days FROM growth_records WHERE baby_id = ? ORDER BY record_date ASC",
                [$baby['date_of_birth'], $baby['id']]
            );
        }

        $this->render('dashboard/croissance', compact('baby', 'records'));
    }

    public function addCroissance() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId($userId);

        $baby = $db->fetch("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);
        if (!$baby) {
            Session::setFlash('error', 'Veuillez ajouter un bébé d\'abord.');
            Request::back();
        }

        $db->insert(
            "INSERT INTO growth_records (baby_id, record_date, weight, height, head_circumference) VALUES (?, ?, ?, ?, ?)",
            [
                $baby['id'],
                Request::post('measured_at') ?: date('Y-m-d'),
                Request::post('weight'),
                Request::post('height'),
                Request::post('head_circumference') ?: null
            ]
        );
        Session::setFlash('success', 'Mesure de croissance ajoutée.');
        Request::back();
    }

    public function vaccination() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId($userId);

        $baby = $db->fetch("SELECT * FROM babies WHERE mother_id = ?", [$motherId]);
        $vaccinations = [];
        if ($baby) {
            $vaccinations = $db->fetchAll(
                "SELECT id, vaccine_name, due_date as scheduled_date, administered_date, status, notes FROM vaccinations WHERE baby_id = ? ORDER BY due_date ASC",
                [$baby['id']]
            );
        }

        $this->render('dashboard/vaccination', compact('baby', 'vaccinations'));
    }

    public function addVaccination() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId($userId);

        $baby = $db->fetch("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);
        if (!$baby) {
            Session::setFlash('error', 'Veuillez ajouter un bébé d\'abord.');
            Request::back();
        }

        $administeredDate = Request::post('administered_date') ?: null;
        $status = $administeredDate ? 'done' : 'pending';

        $db->insert(
            "INSERT INTO vaccinations (baby_id, vaccine_name, due_date, administered_date, status, notes) VALUES (?, ?, ?, ?, ?, ?)",
            [
                $baby['id'],
                Request::post('vaccine_name'),
                Request::post('scheduled_date') ?: null,
                $administeredDate,
                $status,
                Request::post('notes') ?: null
            ]
        );
        Session::setFlash('success', 'Vaccin enregistré.');
        Request::back();
    }

    public function tickets() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $tickets = $db->fetchAll(
            "SELECT t.*, (SELECT COUNT(*) FROM ticket_messages WHERE ticket_id = t.id) as message_count FROM tickets t WHERE t.user_id = ? ORDER BY t.created_at DESC",
            [$userId]
        );
        foreach ($tickets as &$t) {
            $t['responses'] = $db->fetchAll(
                "SELECT tm.*, u.name as user_name FROM ticket_messages tm LEFT JOIN users u ON tm.user_id = u.id WHERE tm.ticket_id = ? ORDER BY tm.created_at ASC",
                [$t['id']]
            );
        }

        $this->render('dashboard/tickets', compact('tickets'));
    }

    public function createTicket() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $subject = trim(Request::post('subject'));
        $message = trim(Request::post('message'));
        $priority = Request::post('priority') ?: 'medium';

        $ticketId = $db->insert(
            "INSERT INTO tickets (user_id, subject, message, priority, status, created_at) VALUES (?, ?, ?, ?, 'open', NOW())",
            [$userId, $subject, $message, $priority]
        );
        $db->insert(
            "INSERT INTO ticket_messages (ticket_id, user_id, message, created_at) VALUES (?, ?, ?, NOW())",
            [$ticketId, $userId, $message]
        );

        Session::setFlash('success', 'Support ticket créé.');
        Request::redirect('/dashboard/tickets');
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

    public function readAllNotifications() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $db->query("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0", [$userId]);
        Session::setFlash('success', 'Toutes les notifications ont été marquées comme lues.');
        Request::redirect('/dashboard/notifications');
    }

    public function readNotification($id) {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $db->query("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?", [$id, $userId]);
        Request::redirect('/dashboard/notifications');
    }

    public function parametres() {
        $this->render('dashboard/parametres');
    }

    public function updateParametres() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $oldPassword = Request::post('old_password');
        $newPassword = Request::post('new_password');
        $confirm = Request::post('new_password_confirm');

        $user = $db->fetch("SELECT password FROM users WHERE id = ?", [$userId]);

        if (!password_verify($oldPassword, $user['password'])) {
            Session::setFlash('error', 'Ancien mot de passe incorrect.');
            Request::back();
        }

        if (strlen($newPassword) < 6) {
            Session::setFlash('error', 'Le nouveau mot de passe doit faire au moins 6 caractères.');
            Request::back();
        }

        if ($newPassword !== $confirm) {
            Session::setFlash('error', 'Les nouveaux mots de passe ne correspondent pas.');
            Request::back();
        }

        $db->query("UPDATE users SET password = ? WHERE id = ?", [password_hash($newPassword, PASSWORD_BCRYPT), $userId]);
        Session::setFlash('success', 'Mot de passe modifié avec succès.');
        Request::back();
    }

    public function appointments() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId($userId);

        $appointments = $db->fetchAll(
            "SELECT a.*, u.name as expert_name, u.specialty as expert_specialty 
             FROM appointments a 
             JOIN users u ON a.expert_id = u.id 
             WHERE a.mother_id = ? 
             ORDER BY a.appointment_date DESC",
            [$motherId]
        );

        $roleExpert = $db->fetch("SELECT id FROM roles WHERE slug = 'expert'");
        $experts = [];
        if ($roleExpert) {
            $experts = $db->fetchAll(
                "SELECT id, name, specialty FROM users WHERE role_id = ? AND status = 'active'",
                [$roleExpert['id']]
            );
        }

        $this->render('dashboard/appointments', compact('appointments', 'experts'));
    }

    public function bookAppointment() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId($userId);

        $expertId = Request::post('expert_id');
        $date = Request::post('appointment_date');
        $type = Request::post('type') ?: 'online';
        $notes = Request::post('notes') ?: null;

        $db->insert(
            "INSERT INTO appointments (mother_id, expert_id, appointment_date, type, notes, status) VALUES (?, ?, ?, ?, ?, 'pending')",
            [$motherId, $expertId, $date, $type, $notes]
        );

        // Notify expert
        $db->insert(
            "INSERT INTO notifications (user_id, type, title, message, link) VALUES (?, 'info', 'Nouveau rendez-vous', 'Vous avez reçu une nouvelle demande de consultation.', '/expert/dashboard')",
            [$expertId]
        );

        Session::setFlash('success', 'Demande de rendez-vous enregistrée.');
        Request::redirect('/dashboard/rendez-vous');
    }

    public function messages() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $activePartnerId = Request::get('partner_id');

        // Fetch list of experts the user has interacted with or can interact with
        $roleExpert = $db->fetch("SELECT id FROM roles WHERE slug = 'expert'");
        $experts = [];
        if ($roleExpert) {
            $experts = $db->fetchAll(
                "SELECT id, name, specialty, avatar FROM users WHERE role_id = ? AND status = 'active'",
                [$roleExpert['id']]
            );
        }

        $chatHistory = [];
        $activePartner = null;
        if ($activePartnerId) {
            $activePartner = $db->fetch("SELECT id, name, specialty, avatar FROM users WHERE id = ?", [$activePartnerId]);
            if ($activePartner) {
                // Mark messages from partner as read
                $db->query("UPDATE expert_messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?", [$activePartnerId, $userId]);

                $chatHistory = $db->fetchAll(
                    "SELECT em.*, u.name as sender_name 
                     FROM expert_messages em 
                     JOIN users u ON em.sender_id = u.id 
                     WHERE (em.sender_id = ? AND em.receiver_id = ?) 
                        OR (em.sender_id = ? AND em.receiver_id = ?) 
                     ORDER BY em.created_at ASC",
                    [$userId, $activePartnerId, $activePartnerId, $userId]
                );
            }
        }

        $this->render('dashboard/messages', compact('experts', 'chatHistory', 'activePartner'));
    }

    public function sendMessage() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $receiverId = Request::post('receiver_id');
        $message = trim(Request::post('message'));

        if ($receiverId && $message !== '') {
            $db->insert(
                "INSERT INTO expert_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)",
                [$userId, $receiverId, $message]
            );

            // Notify receiver
            $db->insert(
                "INSERT INTO notifications (user_id, type, title, message, link) VALUES (?, 'info', 'Nouveau message', 'Vous avez reçu un nouveau message de consultation.', ?)",
                [$receiverId, Session::get('user_role_slug') === 'expert' ? '/dashboard/messagerie' : '/expert/dashboard']
            );
        }

        Request::redirect('/dashboard/messagerie?partner_id=' . $receiverId);
    }

    public function agenda() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId($userId);

        $appointments = $db->fetchAll(
            "SELECT a.*, u.name as expert_name, u.specialty as expert_specialty 
             FROM appointments a 
             JOIN users u ON a.expert_id = u.id 
             WHERE a.mother_id = ? AND a.appointment_date >= CURRENT_DATE()
             ORDER BY a.appointment_date ASC",
            [$motherId]
        );

        $baby = $db->fetch("SELECT * FROM babies WHERE mother_id = ?", [$motherId]);
        $vaccinations = [];
        if ($baby) {
            $vaccinations = $db->fetchAll(
                "SELECT * FROM vaccinations WHERE baby_id = ? AND status = 'pending' ORDER BY due_date ASC",
                [$baby['id']]
            );
        }

        $pregnancy = $db->fetch("SELECT * FROM pregnancies WHERE mother_id = ? AND status = 'active'", [$motherId]);
        $milestones = [];
        if ($pregnancy) {
            $dueDate = new \DateTime($pregnancy['due_date']);
            // Add key ultrasound milestones
            $milestones = [
                ['title' => 'Échographie de datation (1er trimestre)', 'date' => (clone $dueDate)->modify('-196 days')->format('Y-m-d')],
                ['title' => 'Échographie morphologique (2e trimestre)', 'date' => (clone $dueDate)->modify('-126 days')->format('Y-m-d')],
                ['title' => 'Dépistage du diabète gestationnel', 'date' => (clone $dueDate)->modify('-98 days')->format('Y-m-d')],
                ['title' => 'Échographie de croissance (3e trimestre)', 'date' => (clone $dueDate)->modify('-56 days')->format('Y-m-d')],
                ['title' => 'Consultation d\'anesthésie', 'date' => (clone $dueDate)->modify('-28 days')->format('Y-m-d')],
                ['title' => 'Date prévue d\'accouchement', 'date' => $pregnancy['due_date']],
            ];
        }

        $this->render('dashboard/agenda', compact('appointments', 'vaccinations', 'milestones'));
    }
}
