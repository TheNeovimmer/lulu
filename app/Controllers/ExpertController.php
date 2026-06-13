<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;

class ExpertController extends Controller {
    public function __construct() {
        if (strpos($_SERVER['REQUEST_URI'], '/expert/') === 0) {
            $this->layout = 'expert';
            $this->authCheck();
            if (Session::get('user_role_slug') !== 'expert') {
                header('Location: /auth/login');
                exit;
            }
        } else {
            $this->layout = 'front';
        }
    }

    public function index() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $assignedTickets = $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE assigned_to = ? AND status = 'open'", [$userId])['count'];
        $pendingQuestions = $db->fetch("SELECT COUNT(*) as count FROM community_posts WHERE status = 'published'")['count'];
        $articlesCount = $db->fetch("SELECT COUNT(*) as count FROM articles WHERE user_id = ? AND status = 'published'", [$userId])['count'];

        $this->render('expert/index', compact('assignedTickets', 'pendingQuestions', 'articlesCount'));
    }

    public function profil() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        if (Request::isPost()) {
            $name = trim(Request::post('name'));
            $specialty = trim(Request::post('specialty'));
            $bio = trim(Request::post('bio'));
            $address = trim(Request::post('address'));
            $phone = trim(Request::post('phone'));

            $db->query(
                "UPDATE users SET name = ?, specialty = ?, bio = ?, address = ?, phone = ? WHERE id = ?",
                [$name, $specialty, $bio, $address, $phone, $userId]
            );

            $avatar = \App\Helpers\Avatar::upload($_FILES['avatar'] ?? []);
            if ($avatar) {
                $db->query("UPDATE users SET avatar = ? WHERE id = ?", [$avatar, $userId]);
                Session::set('user_avatar', '/uploads/avatars/' . $avatar);
            }

            Session::set('user_name', $name);
            Session::setFlash('success', 'Profil professionnel mis à jour.');
            Request::back();
        }

        $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        $this->render('expert/profil', compact('user'));
    }

    public function updateProfil() { return $this->profil(); }

    public function questions() {
        $db = Database::getInstance();
        $questions = $db->fetchAll(
            "SELECT cp.*, u.name as author_name,
                    (SELECT COUNT(*) FROM community_comments cc WHERE cc.post_id = cp.id) as answers_count
             FROM community_posts cp
             LEFT JOIN users u ON cp.user_id = u.id
             WHERE cp.status = 'published'
             ORDER BY cp.created_at DESC"
        );
        foreach ($questions as &$q) {
            $q['answers'] = $db->fetchAll(
                "SELECT cc.*, u.name as author_name, u.role_id
                 FROM community_comments cc
                 JOIN users u ON cc.user_id = u.id
                 WHERE cc.post_id = ?
                 ORDER BY cc.created_at ASC",
                [$q['id']]
            );
            foreach ($q['answers'] as &$a) {
                $a['is_expert'] = $db->fetchColumn("SELECT slug FROM roles WHERE id = ?", [$a['role_id']]) === 'expert';
            }
        }
        $this->render('expert/questions', compact('questions'));
    }

    public function answerQuestion($id) {
        if (!Request::isPost()) {
            Request::back();
        }

        $db = Database::getInstance();
        $content = trim(Request::post('content'));
        if ($content === '') {
            Session::setFlash('error', 'La réponse ne peut pas être vide.');
            Request::back();
        }
        $db->query(
            "INSERT INTO community_comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())",
            [$id, Session::get('user_id'), $content]
        );

        // Notify the post author
        $post = $db->fetch("SELECT user_id FROM community_posts WHERE id = ?", [$id]);
        if ($post && $post['user_id'] != Session::get('user_id')) {
            $db->insert(
                "INSERT INTO notifications (user_id, type, title, message, link) VALUES (?, 'info', 'Réponse à votre question', 'Un expert a répondu à votre question sur la communauté.', '/communaute/{$id}')",
                [$post['user_id']]
            );
        }

        Session::setFlash('success', 'Réponse publiée.');
        Request::back();
    }

    public function articles() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $articles = $db->fetchAll(
            "SELECT a.*, c.name as category_name
             FROM articles a
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.user_id = ?
             ORDER BY a.created_at DESC",
            [$userId]
        );
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name ASC");
        $this->render('expert/articles', compact('articles', 'categories'));
    }

    public function createArticle() {
        if (!Request::isPost()) {
            Request::back();
        }

        $db = Database::getInstance();
        $title = trim(Request::post('title'));
        $content = Request::post('content');
        $categoryId = Request::post('category_id');
        $slug = $this->slugify($title);

        $originalSlug = $slug;
        $i = 1;
        while ($db->fetch("SELECT id FROM articles WHERE slug = ?", [$slug])) {
            $slug = $originalSlug . '-' . $i++;
        }

        $status = Request::post('status', 'draft');
        if (!in_array($status, ['draft', 'published'])) {
            $status = 'draft';
        }
        $db->insert(
            "INSERT INTO articles (title, slug, content, category_id, user_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())",
            [$title, $slug, $content, $categoryId, Session::get('user_id'), $status]
        );
        Session::setFlash('success', 'Article créé avec succès.');
        Request::back();
    }

    public function ressources() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $resources = $db->fetchAll(
            "SELECT r.*, c.name as category_name 
             FROM resources r 
             LEFT JOIN categories c ON r.category_id = c.id 
             WHERE r.user_id = ? 
             ORDER BY r.created_at DESC",
            [$userId]
        );
        $this->render('expert/ressources', compact('resources'));
    }

    public function notifications() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $db->query("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0", [$userId]);
        $notifications = $db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
        $this->render('expert/notifications', compact('notifications'));
    }

    public function agenda() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $appointments = $db->fetchAll(
            "SELECT a.*, u.name as mother_name
             FROM appointments a
             JOIN mothers m ON a.mother_id = m.id
             JOIN users u ON m.user_id = u.id
             WHERE a.expert_id = ?
             ORDER BY a.appointment_date ASC",
            [$userId]
        );

        $this->render('expert/agenda', compact('appointments'));
    }

    public function parametres() {
        $this->render('expert/parametres');
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

    public function createResource() {
        if (!Request::isPost()) { Request::back(); }
        $db = Database::getInstance();
        $title = trim(Request::post('title'));
        $description = Request::post('description');
        $type = Request::post('type', 'guide');
        $categoryId = Request::post('category_id');
        $userId = Session::get('user_id');
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = $slug ?: 'resource-' . time();

        // Handle file upload
        $fileUrl = '';
        $uploadDir = __DIR__ . '/../../public/uploads/ressources/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        if (!empty($_FILES['file_url']['name'])) {
            $ext = pathinfo($_FILES['file_url']['name'], PATHINFO_EXTENSION);
            $filename = $slug . '-' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['file_url']['tmp_name'], $uploadDir . $filename)) {
                $fileUrl = '/uploads/ressources/' . $filename;
            }
        }

        $db->insert(
            "INSERT INTO resources (title, slug, description, type, file_url, category_id, user_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'published', NOW())",
            [$title, $slug, $description, $type, $fileUrl, $categoryId, $userId]
        );
        Session::setFlash('success', 'Ressource créée.');
        Request::back();
    }

    public function readAllNotifications() {
        $db = Database::getInstance();
        $db->query("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0", [Session::get('user_id')]);
        Session::setFlash('success', 'Notifications marquées comme lues.');
        Request::back();
    }

    public function readNotification($id) {
        $db = Database::getInstance();
        $db->query("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?", [$id, Session::get('user_id')]);
        Request::back();
    }

    public function directory() {
        $db = Database::getInstance();
        $roleExpert = $db->fetch("SELECT id FROM roles WHERE slug = 'expert'");
        $experts = [];
        if ($roleExpert) {
            $experts = $db->fetchAll(
                "SELECT * FROM users WHERE role_id = ? AND status = 'active' ORDER BY name ASC",
                [$roleExpert['id']]
            );
        }
        $this->render('pages/experts', compact('experts'));
    }

    public function showProfile($id) {
        $db = Database::getInstance();
        $expert = $db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
        
        if (!$expert || $expert['specialty'] === null) {
            $this->render('errors/404');
            return;
        }
        
        $this->render('pages/expert_detail', compact('expert'));
    }

    public function messages() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $activePartnerId = Request::get('partner_id');

        // Get all mothers who have messaged this expert
        $conversations = $db->fetchAll(
            "SELECT DISTINCT u.id, u.name, u.avatar,
                    (SELECT message FROM expert_messages WHERE (sender_id = u.id AND receiver_id = ?) OR (sender_id = ? AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_message,
                    (SELECT created_at FROM expert_messages WHERE (sender_id = u.id AND receiver_id = ?) OR (sender_id = ? AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_message_at,
                    (SELECT COUNT(*) FROM expert_messages WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0) as unread_count
             FROM expert_messages em
             JOIN users u ON (em.sender_id = u.id OR em.receiver_id = u.id)
             WHERE (em.sender_id = ? OR em.receiver_id = ?)
               AND u.id != ?
               AND u.role_id = (SELECT id FROM roles WHERE slug = 'maman')
             ORDER BY last_message_at DESC",
            [$userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId]
        );

        $chatHistory = [];
        $activePartner = null;
        if ($activePartnerId) {
            $activePartner = $db->fetch("SELECT id, name, avatar FROM users WHERE id = ?", [$activePartnerId]);
            if ($activePartner) {
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

        $this->render('expert/messagerie', compact('conversations', 'chatHistory', 'activePartner'));
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
            $db->insert(
                "INSERT INTO notifications (user_id, type, title, message, link) VALUES (?, 'info', 'Nouveau message', 'Vous avez reçu un nouveau message de votre expert.', '/dashboard/messagerie?partner_id=')",
                [$receiverId, $userId]
            );
        }

        Request::redirect('/expert/messagerie?partner_id=' . $receiverId);
    }

    public function updateAppointment($id) {
        if (!Request::isPost()) { Request::back(); }
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $action = Request::post('action');

        if (!in_array($action, ['confirmed', 'cancelled'])) {
            Session::setFlash('error', 'Action invalide.');
            Request::back();
        }

        $appt = $db->fetch("SELECT a.* FROM appointments a WHERE a.id = ? AND a.expert_id = ?", [$id, $userId]);
        if (!$appt) {
            Session::setFlash('error', 'Rendez-vous introuvable.');
            Request::back();
        }

        $db->query("UPDATE appointments SET status = ? WHERE id = ?", [$action, $id]);

        // Notify the mother
        $mother = $db->fetch("SELECT m.user_id FROM appointments a JOIN mothers m ON a.mother_id = m.id WHERE a.id = ?", [$id]);
        $actionLabel = $action === 'confirmed' ? 'confirmé' : 'annulé';
        if ($mother) {
            $db->insert(
                "INSERT INTO notifications (user_id, type, title, message, link) VALUES (?, 'info', 'Rendez-vous {$actionLabel}', 'Votre rendez-vous a été {$actionLabel} par l\\'expert.', '/dashboard/rendez-vous')",
                [$mother['user_id']]
            );
        }

        $statusMsg = $action === 'confirmed' ? 'confirmé' : 'annulé';
        Session::setFlash('success', "Rendez-vous {$statusMsg}.");
        Request::back();
    }

    public function editArticle($id) {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $article = $db->fetch("SELECT * FROM articles WHERE id = ? AND user_id = ?", [$id, $userId]);
        if (!$article) {
            Session::setFlash('error', 'Article introuvable.');
            Request::redirect('/expert/articles');
        }
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name ASC");
        $this->render('expert/edit_article', compact('article', 'categories'));
    }

    public function updateArticle($id) {
        if (!Request::isPost()) { Request::back(); }
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $article = $db->fetch("SELECT * FROM articles WHERE id = ? AND user_id = ?", [$id, $userId]);
        if (!$article) {
            Session::setFlash('error', 'Article introuvable.');
            Request::back();
        }

        $title = trim(Request::post('title'));
        $content = Request::post('content');
        $categoryId = Request::post('category_id');
        $status = Request::post('status', 'draft');
        if (!in_array($status, ['draft', 'published'])) {
            $status = 'draft';
        }

        $slug = $this->slugify($title);
        $db->query(
            "UPDATE articles SET title = ?, slug = ?, content = ?, category_id = ?, status = ? WHERE id = ? AND user_id = ?",
            [$title, $slug, $content, $categoryId, $status, $id, $userId]
        );
        Session::setFlash('success', 'Article mis à jour.');
        Request::back();
    }

    public function deleteArticle($id) {
        if (!Request::isPost()) { Request::back(); }
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $db->query("DELETE FROM articles WHERE id = ? AND user_id = ?", [$id, $userId]);
        Session::setFlash('success', 'Article supprimé.');
        Request::back();
    }

    private function slugify($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}
