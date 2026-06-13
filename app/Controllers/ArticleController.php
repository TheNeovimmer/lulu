<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Repositories\ArticleRepository;

class ArticleController {
    private $articleRepo;
    public function __construct() { $this->articleRepo = new ArticleRepository(); }

    public function index() {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $category = $_GET['category'] ?? null;
        $limit = 9;
        $offset = ($page - 1) * $limit;
        $articles = $this->articleRepo->findAllPublished($limit, $offset, $category);
        $total = $this->articleRepo->countPublished($category);

        $db = Database::getInstance();
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");

        View::render('pages/blog', compact('articles', 'categories', 'category', 'page', 'total', 'limit'), 'front');
    }

    public function show($slug) {
        $article = $this->articleRepo->findBySlug($slug);
        if (!$article) { View::render('errors/404', [], 'front'); return; }

        $db = Database::getInstance();
        $db->query("UPDATE articles SET views_count = views_count + 1 WHERE id = ?", [$article['id']]);
        $comments = $db->fetchAll(
            "SELECT c.*, u.name as user_name FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.article_id = ? AND c.status = 'approved' ORDER BY c.created_at DESC",
            [$article['id']]
        );
        $popular = $db->fetchAll("SELECT id, title, slug, created_at FROM articles WHERE status='published' AND id != ? ORDER BY created_at DESC LIMIT 4", [$article['id']]);

        View::render('pages/blog-single', compact('article', 'comments', 'popular'), 'front');
    }

    public function comment($slug) {
        if (!Session::has('user_id')) { Request::redirect('/auth/login'); return; }
        $article = $this->articleRepo->findBySlug($slug);
        if (!$article) { Request::back(); }

        $db = Database::getInstance();
        $db->insert(
            "INSERT INTO comments (article_id, user_id, content, status) VALUES (?, ?, ?, 'pending')",
            [$article['id'], Session::get('user_id'), Request::post('content')]
        );
        Session::setFlash('success', 'Votre commentaire a été soumis et sera visible après modération.');
        Request::back();
    }
}
