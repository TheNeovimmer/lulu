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
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name ASC");
        View::render('admin/categories', compact('categories'), 'admin');
    }

    public function edit($id) {
        $db = Database::getInstance();
        $category = $db->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
        if (!$category) { Request::redirect('/admin/categories'); }
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name ASC");
        View::render('admin/categories', compact('categories', 'category'), 'admin');
    }

    public function store() {
        $db = Database::getInstance();
        $name = Request::post('name');
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $name)));
        $db->query("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)", [$name, $slug, Request::post('description') ?: null]);
        Session::setFlash('success', 'Catégorie créée.');
        Request::redirect('/admin/categories');
    }

    public function update($id) {
        $db = Database::getInstance();
        $name = Request::post('name');
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $name)));
        $db->query("UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?", [$name, $slug, Request::post('description') ?: null, $id]);
        Session::setFlash('success', 'Catégorie mise à jour.');
        Request::redirect('/admin/categories');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM categories WHERE id = ?", [$id]);
        Session::setFlash('success', 'Catégorie supprimée.');
        Request::redirect('/admin/categories');
    }
}
