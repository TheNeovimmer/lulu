<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Repositories\UserRepository;

class AuthController {
    private $userRepo;
    public function __construct() { $this->userRepo = new UserRepository(); }

    public function login() {
        View::render('auth/login', ['title' => 'Connexion - LUMA'], 'front');
    }

    public function authenticate() {
        $email = Request::post('email');
        $password = Request::post('password');
        $user = $this->userRepo->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            Session::setFlash('error', 'Email ou mot de passe incorrect');
            Request::back();
        }

        if ($user['status'] !== 'active') {
            Session::setFlash('error', 'Votre compte est suspendu. Contactez l\'administration.');
            Request::back();
        }

        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_role_slug', $user['role_slug']);
        Session::set('user_role_name', $user['role_name']);
        Session::set('user_avatar', $user['avatar']);

        switch ($user['role_slug']) {
            case 'admin': Request::redirect('/admin'); break;
            case 'expert': Request::redirect('/expert/dashboard'); break;
            case 'ctt': Request::redirect('/ctt/dashboard'); break;
            default: Request::redirect('/dashboard');
        }
    }

    public function register() {
        View::render('auth/register', ['title' => 'Inscription - LUMA'], 'front');
    }

    public function store() {
        $name = trim(Request::post('name'));
        $email = trim(Request::post('email'));
        $role = Request::post('role', 'maman');
        $password = Request::post('password');
        $confirm = Request::post('password_confirm');

        $errors = [];
        if (strlen($name) < 2) $errors[] = 'Nom trop court';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide';
        if (!in_array($role, ['maman', 'expert'])) $errors[] = 'Rôle invalide';
        if (strlen($password) < 6) $errors[] = 'Mot de passe trop court (6 caractères minimum)';
        if ($password !== $confirm) $errors[] = 'Les mots de passe ne correspondent pas';
        if ($this->userRepo->findByEmail($email)) $errors[] = 'Cet email est déjà utilisé';

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            Request::back();
        }

        $this->userRepo->create(['name' => $name, 'email' => $email, 'password' => $password, 'role' => $role]);
        Session::setFlash('success', 'Inscription réussie ! Connectez-vous.');
        Request::redirect('/auth/login');
    }

    public function logout() {
        Session::destroy();
        Request::redirect('/');
    }
}
