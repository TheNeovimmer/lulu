<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminUserController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $roleSlug = Request::get('role', '');

        $where = '';
        $params = [];
        if ($roleSlug) {
            $where = "WHERE r.slug = ?";
            $params[] = $roleSlug;
        }

        $users = $db->fetchAll(
            "SELECT u.*, r.name as role_name, r.slug as role_slug FROM users u LEFT JOIN roles r ON u.role_id = r.id $where ORDER BY u.created_at DESC",
            $params
        );
        $roles = $db->fetchAll("SELECT * FROM roles ORDER BY name");
        View::render('admin/users', compact('users', 'roles'), 'admin');
    }

    public function store() {
        $db = Database::getInstance();
        $name = Request::post('name');
        $email = Request::post('email');
        $password = Request::post('password');
        $roleId = Request::post('role_id');
        $status = Request::post('status', 'active');

        $existing = $db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) {
            Session::setFlash('error', 'Cet email est déjà utilisé.');
            Request::redirect('/admin/utilisateurs');
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $db->query("INSERT INTO users (name, email, password, role_id, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())", [$name, $email, $hashed, $roleId, $status]);
        Session::setFlash('success', 'Utilisateur créé.');
        Request::redirect('/admin/utilisateurs');
    }

    public function toggleRole($id) {
        $db = Database::getInstance();
        \App\Core\Session::validate_csrf();
        $roleSlug = Request::post('role');
        $role = $db->fetch("SELECT id FROM roles WHERE slug = ?", [$roleSlug]);
        if ($role) {
            $db->query("UPDATE users SET role_id = ? WHERE id = ?", [$role['id'], $id]);
            Session::setFlash('success', 'Rôle modifié.');
        }
        Request::redirect('/admin/utilisateurs');
    }

    public function suspend($id) {
        $db = Database::getInstance();
        \App\Core\Session::validate_csrf();
        $db->query("UPDATE users SET status = 'suspended' WHERE id = ?", [$id]);
        Session::setFlash('success', 'Utilisateur suspendu.');
        Request::redirect('/admin/utilisateurs');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        \App\Core\Session::validate_csrf();
        $db->query("DELETE FROM users WHERE id = ?", [$id]);
        Session::setFlash('success', 'Utilisateur supprimé.');
        Request::redirect('/admin/utilisateurs');
    }
}
