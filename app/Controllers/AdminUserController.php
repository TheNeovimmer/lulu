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
        $status = Request::get('status', '');

        $where = '';
        $params = [];
        if ($status) {
            $where = "WHERE u.status = ?";
            $params[] = $status;
        }

        $users = $db->fetchAll(
            "SELECT u.*, r.name as role_name, r.slug as role_slug FROM users u LEFT JOIN roles r ON u.role_id = r.id $where ORDER BY u.created_at DESC",
            $params
        );
        View::render('admin/users', compact('users', 'status'), 'admin');
    }

    public function toggleRole($id) {
        $db = Database::getInstance();
        $user = $db->fetch("SELECT role_id FROM users WHERE id = ?", [$id]);
        if (!$user) {
            Request::redirect('/admin/users');
        }

        $roles = $db->fetchAll("SELECT id, slug FROM roles ORDER BY id");
        $currentIndex = null;
        foreach ($roles as $i => $r) {
            if ($r['id'] == $user['role_id']) {
                $currentIndex = $i;
                break;
            }
        }
        $nextIndex = ($currentIndex !== null) ? ($currentIndex + 1) % count($roles) : 0;
        $newRoleId = $roles[$nextIndex]['id'];

        $db->query("UPDATE users SET role_id = ? WHERE id = ?", [$newRoleId, $id]);
        Session::setFlash('success', 'Rôle modifié.');
        Request::redirect('/admin/users');
    }

    public function suspend($id) {
        $db = Database::getInstance();
        $db->query("UPDATE users SET status = 'suspended' WHERE id = ?", [$id]);
        Session::setFlash('success', 'Utilisateur suspendu.');
        Request::redirect('/admin/users');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM users WHERE id = ?", [$id]);
        Session::setFlash('success', 'Utilisateur supprimé.');
        Request::redirect('/admin/users');
    }
}
