<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;

class AdminArticleController {
    private ArticleRepository $articleRepo;
    private CategoryRepository $categoryRepo;

    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
        $this->articleRepo = new ArticleRepository();
        $this->categoryRepo = new CategoryRepository();
    }

    public function index() {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $articles = $this->articleRepo->raw(
            "SELECT a.*, u.name as author_name, c.name as category_name
             FROM articles a
             LEFT JOIN users u ON a.user_id = u.id
             LEFT JOIN categories c ON a.category_id = c.id
             ORDER BY a.created_at DESC
             LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
        $total = $this->articleRepo->rawOne(
            "SELECT COUNT(*) as count FROM articles"
        )['count'] ?? 0;
        $totalPages = max(1, (int)ceil($total / $perPage));
        $pagination = ['current' => $page, 'pages' => $totalPages, 'total' => $total];
        View::render('admin/articles', compact('articles', 'pagination'), 'admin');
    }

    public function create() {
        $categories = $this->categoryRepo->findAllOrdered();
        View::render('admin/article-form', compact('categories'), 'admin');
    }

    public function store() {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO articles (title, slug, content, excerpt, category_id, user_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
            [
                Request::post('title'),
                $this->articleRepo->generateSlug(Request::post('title')),
                Request::post('content'),
                Request::post('excerpt') ?: null,
                Request::post('category_id'),
                Session::get('user_id'),
                Request::post('status', 'draft'),
            ]
        );
        Session::setFlash('success', 'Article créé.');
        Request::redirect('/admin/articles');
    }

    public function edit($id) {
        $article = $this->articleRepo->findById($id);
        if (!$article) { Request::redirect('/admin/articles'); }
        $categories = $this->categoryRepo->findAllOrdered();
        View::render('admin/article-form', compact('article', 'categories'), 'admin');
    }

    public function update($id) {
        $db = Database::getInstance();
        $slug = $this->articleRepo->generateSlug(Request::post('title'), $id);
        $db->query(
            "UPDATE articles SET title = ?, slug = ?, content = ?, excerpt = ?, category_id = ?, status = ? WHERE id = ?",
            [Request::post('title'), $slug, Request::post('content'), Request::post('excerpt') ?: null, Request::post('category_id'), Request::post('status', 'draft'), $id]
        );
        Session::setFlash('success', 'Article mis à jour.');
        Request::redirect('/admin/articles');
    }

    public function destroy($id) {
        $this->articleRepo->delete($id);
        Session::setFlash('success', 'Article supprimé.');
        Request::redirect('/admin/articles');
    }
}
