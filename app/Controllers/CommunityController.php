<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;

class CommunityController extends Controller {
    public function __construct() {
        $this->layout = 'front';
    }

    public function index() {
        $db = Database::getInstance();
        $page = max(1, (int)Request::get('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $total = $db->fetch("SELECT COUNT(*) as count FROM community_posts")['count'];
        $posts = $db->fetchAll(
            "SELECT cp.*, u.name as author_name, u.avatar as author_avatar,
                    (SELECT COUNT(*) FROM community_likes WHERE post_id = cp.id) as likes_count,
                    (SELECT COUNT(*) FROM community_comments WHERE post_id = cp.id) as comments_count
             FROM community_posts cp
             LEFT JOIN users u ON cp.user_id = u.id
             ORDER BY cp.created_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );

        $totalPages = ceil($total / $limit);
        $this->render('pages/communaute', compact('posts', 'page', 'totalPages', 'total'));
    }

    public function show($id) {
        $db = Database::getInstance();
        $post = $db->fetch(
            "SELECT cp.*, u.name as author_name, u.avatar as author_avatar
             FROM community_posts cp
             LEFT JOIN users u ON cp.user_id = u.id
             WHERE cp.id = ?",
            [$id]
        );
        if (!$post) {
            $this->render('errors/404');
            return;
        }

        $comments = $db->fetchAll(
            "SELECT cc.*, u.name as user_name, u.avatar as user_avatar
             FROM community_comments cc
             LEFT JOIN users u ON cc.user_id = u.id
             WHERE cc.post_id = ?
             ORDER BY cc.created_at ASC",
            [$id]
        );

        $likesCount = $db->fetch("SELECT COUNT(*) as count FROM community_likes WHERE post_id = ?", [$id])['count'];

        $userLiked = false;
        if (Session::has('user_id')) {
            $like = $db->fetch("SELECT id FROM community_likes WHERE post_id = ? AND user_id = ?", [$id, Session::get('user_id')]);
            $userLiked = (bool)$like;
        }

        $this->render('pages/sujet', compact('post', 'comments', 'likesCount', 'userLiked'));
    }

    public function store() {
        if (!Session::has('user_id')) {
            header('Location: /auth/login');
            exit;
        }

        $title = trim(Request::post('title'));
        $content = trim(Request::post('content'));

        if (empty($title) || empty($content)) {
            Session::setFlash('error', 'Le titre et le contenu sont requis.');
            Request::back();
        }

        $db = Database::getInstance();
        $db->insert(
            "INSERT INTO community_posts (title, content, user_id, status, created_at) VALUES (?, ?, ?, 'published', NOW())",
            [$title, $content, Session::get('user_id')]
        );

        Session::setFlash('success', 'Publication créée avec succès.');
        Request::redirect('/communaute');
    }

    public function comment($id) {
        if (!Session::has('user_id')) {
            header('Location: /auth/login');
            exit;
        }

        $db = Database::getInstance();
        $db->insert(
            "INSERT INTO community_comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())",
            [$id, Session::get('user_id'), Request::post('content')]
        );
        Session::setFlash('success', 'Commentaire ajouté.');
        Request::back();
    }

    public function like($id) {
        if (!Session::has('user_id')) {
            header('Location: /auth/login');
            exit;
        }

        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $existing = $db->fetch("SELECT id FROM community_likes WHERE post_id = ? AND user_id = ?", [$id, $userId]);

        if ($existing) {
            $db->query("DELETE FROM community_likes WHERE id = ?", [$existing['id']]);
        } else {
            $db->insert("INSERT INTO community_likes (post_id, user_id, created_at) VALUES (?, ?, NOW())", [$id, $userId]);
        }

        $liked = !$existing;
        $count = $db->fetch("SELECT COUNT(*) as count FROM community_likes WHERE post_id = ?", [$id])['count'];

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode(['liked' => $liked, 'count' => $count]);
            exit;
        }

        Request::back();
    }
}
