<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminResourceController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $resources = $db->fetchAll(
            "SELECT r.*, c.name as category_name, u.name as expert_name
                         FROM resources r
                         LEFT JOIN categories c ON r.category_id = c.id
                         LEFT JOIN users u ON r.user_id = u.id
                         ORDER BY r.created_at DESC"
        );
        View::render('admin/ressources', compact('resources'), 'admin');
    }

    public function create() {
        $db = Database::getInstance();
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
        $experts = $db->fetchAll("SELECT u.id, u.name FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'expert' ORDER BY u.name");
        View::render('admin/ressource-form', compact('categories', 'experts'), 'admin');
    }

    public function store() {
        $db = Database::getInstance();
        $db->insert(
            "INSERT INTO resources (title, description, file_url, category_id, expert_id) VALUES (?, ?, ?, ?, ?)",
            [
                Request::post('title'),
                Request::post('description'),
                Request::post('file_url'),
                Request::post('category_id'),
                Request::post('expert_id') ?: null,
            ]
        );
        Session::setFlash('success', 'Ressource créée avec succès.');
        Request::redirect('/admin/ressources');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM resources WHERE id = ?", [$id]);
        Session::setFlash('success', 'Ressource supprimée.');
        Request::redirect('/admin/ressources');
    }
}
