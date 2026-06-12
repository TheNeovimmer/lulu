<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminCategoryController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $categories = $db->fetchAll(
            "SELECT c.*, (SELECT COUNT(*) FROM articles WHERE category_id = c.id) as article_count FROM categories c ORDER BY c.name"
        );
        View::render('admin/categories', compact('categories'), 'admin');
    }

    public function store() {
        $db = Database::getInstance();
        $name = Request::post('name');
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $db->insert("INSERT INTO categories (name, slug) VALUES (?, ?)", [$name, $slug]);
        Session::setFlash('success', 'Catégorie créée.');
        Request::redirect('/admin/categories');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM categories WHERE id = ?", [$id]);
        Session::setFlash('success', 'Catégorie supprimée.');
        Request::redirect('/admin/categories');
    }
}
