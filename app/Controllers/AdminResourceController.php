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
            "SELECT r.*, u.name as expert_name, c.name as category_name,
                    COALESCE(r.downloads_count, 0) as downloads_count
             FROM resources r LEFT JOIN users u ON r.user_id = u.id LEFT JOIN categories c ON r.category_id = c.id
             ORDER BY r.created_at DESC"
        );
        View::render('admin/ressources', compact('resources'), 'admin');
    }

    public function create() {
        $db = Database::getInstance();
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
        $experts = $db->fetchAll("SELECT u.* FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'expert' AND u.status = 'active' ORDER BY u.name");
        View::render('admin/ressource-form', compact('categories', 'experts'), 'admin');
    }

    public function store() {
        $db = Database::getInstance();
        $title = Request::post('title');
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $title))) ?: 'resource-' . time();
        $db->query(
            "INSERT INTO resources (title, slug, description, type, file_url, category_id, user_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'published', NOW())",
            [$title, $slug, Request::post('description'), Request::post('type', 'guide'), Request::post('file_url') ?: '', Request::post('category_id'), Session::get('user_id')]
        );
        Session::setFlash('success', 'Ressource créée.');
        Request::redirect('/admin/ressources');
    }

    public function edit($id) {
        $db = Database::getInstance();
        $resource = $db->fetch("SELECT * FROM resources WHERE id = ?", [$id]);
        if (!$resource) { Request::redirect('/admin/ressources'); }
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
        $experts = $db->fetchAll("SELECT u.* FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'expert' AND u.status = 'active' ORDER BY u.name");
        View::render('admin/ressource-form', compact('resource', 'categories', 'experts'), 'admin');
    }

    public function update($id) {
        $db = Database::getInstance();
        $db->query(
            "UPDATE resources SET title = ?, description = ?, type = ?, category_id = ? WHERE id = ?",
            [Request::post('title'), Request::post('description'), Request::post('type', 'guide'), Request::post('category_id'), $id]
        );
        Session::setFlash('success', 'Ressource mise à jour.');
        Request::redirect('/admin/ressources');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM resources WHERE id = ?", [$id]);
        Session::setFlash('success', 'Ressource supprimée.');
        Request::redirect('/admin/ressources');
    }
}
