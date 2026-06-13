<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Services\NotificationService;
use App\Repositories\UserRepository;

class AdminUserController {
    private UserRepository $userRepo;
    private NotificationService $notifService;

    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
        $this->userRepo = new UserRepository();
        $this->notifService = new NotificationService();
    }

    public function index() {
        $roleSlug = Request::get('role', '');
        $users = $roleSlug
            ? $this->userRepo->allWithRoles(['r.slug' => $roleSlug])
            : $this->userRepo->allWithRoles();
        $roles = $this->userRepo->raw("SELECT * FROM roles ORDER BY name");
        View::render('admin/users', compact('users', 'roles'), 'admin');
    }

    public function store() {
        $db = Database::getInstance();
        $name = Request::post('name');
        $email = Request::post('email');
        $password = Request::post('password');
        $roleId = Request::post('role_id');
        $status = Request::post('status', 'active');
        $existing = $this->userRepo->findByEmail($email);
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
        Session::validate_csrf();
        $roleSlug = Request::post('role');
        $role = $db->fetch("SELECT id FROM roles WHERE slug = ?", [$roleSlug]);
        if ($role) {
            $db->query("UPDATE users SET role_id = ? WHERE id = ?", [$role['id'], $id]);
            Session::setFlash('success', 'Rôle modifié.');
        }
        Request::redirect('/admin/utilisateurs');
    }

    public function suspend($id) {
        Session::validate_csrf();
        $this->userRepo->update($id, ['status' => 'suspended']);
        $this->notifService->sendAccountSuspended($id);
        Session::setFlash('success', 'Utilisateur suspendu.');
        Request::redirect('/admin/utilisateurs');
    }

    public function activate($id) {
        Session::validate_csrf();
        $this->userRepo->update($id, ['status' => 'active']);
        $this->notifService->sendAccountActivated($id);
        Session::setFlash('success', 'Utilisateur réactivé.');
        Request::redirect('/admin/utilisateurs');
    }

    public function destroy($id) {
        Session::validate_csrf();
        $this->userRepo->delete($id);
        Session::setFlash('success', 'Utilisateur supprimé.');
        Request::redirect('/admin/utilisateurs');
    }
}
