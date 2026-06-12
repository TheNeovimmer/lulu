<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;

class ExpertController extends Controller {
    public function __construct() {
        $this->layout = 'expert';
        $this->authCheck();
        if (Session::get('user_role_slug') !== 'expert') {
            header('Location: /auth/login');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $assignedTickets = $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE assigned_to = ? AND status = 'open'", [$userId])['count'];
        $pendingQuestions = $db->fetch("SELECT COUNT(*) as count FROM community_posts WHERE expert_answer IS NULL")['count'];
        $articlesCount = $db->fetch("SELECT COUNT(*) as count FROM articles WHERE user_id = ? AND status = 'published'", [$userId])['count'];

        $this->render('expert/index', compact('assignedTickets', 'pendingQuestions', 'articlesCount'));
    }

    public function profil() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        if (Request::isPost()) {
            $expertiseAreas = Request::post('expertise_areas');
            $bio = Request::post('bio');

            $existing = $db->fetch("SELECT id FROM expert_profiles WHERE user_id = ?", [$userId]);
            if ($existing) {
                $db->query(
                    "UPDATE expert_profiles SET expertise_areas = ?, bio = ? WHERE user_id = ?",
                    [$expertiseAreas, $bio, $userId]
                );
            } else {
                $db->insert(
                    "INSERT INTO expert_profiles (user_id, expertise_areas, bio) VALUES (?, ?, ?)",
                    [$userId, $expertiseAreas, $bio]
                );
            }

            Session::setFlash('success', 'Profil mis à jour.');
            Request::back();
        }

        $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        $profile = $db->fetch("SELECT * FROM expert_profiles WHERE user_id = ?", [$userId]);

        $this->render('expert/profil', compact('user', 'profile'));
    }

    public function questions() {
        $db = Database::getInstance();
        $questions = $db->fetchAll(
            "SELECT cp.*, u.name as author_name FROM community_posts cp LEFT JOIN users u ON cp.user_id = u.id WHERE cp.expert_answer IS NULL ORDER BY cp.created_at DESC"
        );
        $this->render('expert/questions', compact('questions'));
    }

    public function answerQuestion($id) {
        if (!Request::isPost()) {
            Request::back();
        }

        $db = Database::getInstance();
        $db->query(
            "UPDATE community_posts SET expert_answer = ?, answered_by = ?, answered_at = NOW() WHERE id = ?",
            [Request::post('answer'), Session::get('user_id'), $id]
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
        $this->render('expert/articles', compact('articles'));
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
            "SELECT r.*, rc.name as category_name FROM resources r LEFT JOIN resource_categories rc ON r.category_id = rc.id WHERE r.created_by = ? ORDER BY r.created_at DESC",
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

    private function slugify($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}
