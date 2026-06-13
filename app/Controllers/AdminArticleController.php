<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminArticleController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $search = Request::get('search', '');
        $page = max(1, (int) Request::get('page', 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $where = '';
        $params = [];
        if ($search) {
            $where = "WHERE a.title LIKE ?";
            $params[] = "%$search%";
        }

        $articles = $db->fetchAll(
            "SELECT a.*, c.name as category_name, u.name as author_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id LEFT JOIN users u ON a.user_id = u.id $where ORDER BY a.created_at DESC LIMIT $limit OFFSET $offset",
            $params
        );
        $total = $db->fetch(
            "SELECT COUNT(*) as count FROM articles a $where",
            $params
        )['count'];

        $pagination = [
            'current' => $page,
            'pages' => max(1, ceil($total / $limit)),
        ];

        View::render('admin/articles', compact('articles', 'page', 'total', 'limit', 'search', 'pagination'), 'admin');
    }

    public function create() {
        $db = Database::getInstance();
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
        View::render('admin/article-form', compact('categories'), 'admin');
    }

    public function store() {
        $db = Database::getInstance();
        $title = Request::post('title');
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $image = null;

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../../public/assets/uploads/' . $filename);
            $image = '/assets/uploads/' . $filename;
        }

        $db->insert(
            "INSERT INTO articles (category_id, user_id, title, slug, content, image, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
            [
                Request::post('category_id'),
                Session::get('user_id'),
                $title,
                $slug,
                Request::post('content'),
                $image,
                Request::post('status', 'draft'),
            ]
        );

        Session::setFlash('success', 'Article créé avec succès.');
        Request::redirect('/admin/articles');
    }

    public function edit($id) {
        $db = Database::getInstance();
        $article = $db->fetch("SELECT * FROM articles WHERE id = ?", [$id]);
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
        View::render('admin/article-form', compact('article', 'categories'), 'admin');
    }

    public function update($id) {
        $db = Database::getInstance();
        $title = Request::post('title');
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

        $existing = $db->fetch("SELECT image FROM articles WHERE id = ?", [$id]);
        $image = $existing['image'];

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../../public/assets/uploads/' . $filename);
            $image = '/assets/uploads/' . $filename;
        }

        $db->query(
            "UPDATE articles SET category_id=?, title=?, slug=?, content=?, image=?, status=? WHERE id=?",
            [
                Request::post('category_id'),
                $title,
                $slug,
                Request::post('content'),
                $image,
                Request::post('status', 'draft'),
                $id,
            ]
        );

        Session::setFlash('success', 'Article mis à jour.');
        Request::redirect('/admin/articles');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM articles WHERE id = ?", [$id]);
        Session::setFlash('success', 'Article supprimé.');
        Request::redirect('/admin/articles');
    }
}
