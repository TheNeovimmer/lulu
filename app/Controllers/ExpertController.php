<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;

class ExpertController extends Controller {
    public function __construct() {
        // Adapt layout and auth check based on route prefix
        if (strpos($_SERVER['REQUEST_URI'], '/expert') === 0) {
            $this->layout = 'expert';
            $this->authCheck();
            if (Session::get('user_role_slug') !== 'expert') {
                header('Location: /auth/login');
                exit;
            }
        } else {
            $this->layout = 'front';
            $this->authCheck();
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
            $specialty = trim(Request::post('specialty'));
            $bio = trim(Request::post('bio'));
            $address = trim(Request::post('address'));
            $phone = trim(Request::post('phone'));

            $db->query(
                "UPDATE users SET specialty = ?, bio = ?, address = ?, phone = ? WHERE id = ?",
                [$specialty, $bio, $address, $phone, $userId]
            );

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
            "SELECT cp.*, u.name as author_name FROM community_posts cp LEFT JOIN users u ON cp.user_id = u.id WHERE cp.status = 'published' ORDER BY cp.created_at DESC"
        );
        $this->render('expert/questions', compact('questions'));
    }

    public function answerQuestion($id) {
        if (!Request::isPost()) {
            Request::back();
        }

        $db = Database::getInstance();
        $db->query(
            "INSERT INTO community_comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())",
            [$id, Session::get('user_id'), Request::post('answer')]
        );
        Session::setFlash('success', 'Réponse publiée.');
        Request::back();
    }

    public function articles() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $articles = $db->fetchAll(
            "SELECT * FROM articles WHERE user_id = ? ORDER BY created_at DESC",
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

        $db->insert(
            "INSERT INTO articles (title, slug, content, category_id, user_id, status, created_at) VALUES (?, ?, ?, ?, ?, 'published', NOW())",
            [$title, $slug, $content, $categoryId, Session::get('user_id')]
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

    private function slugify($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}
