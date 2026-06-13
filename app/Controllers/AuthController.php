<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Services\AuthService;

class AuthController extends Controller {
    private AuthService $authService;

    public function __construct() {
        $this->layout = 'front';
        $this->authService = new AuthService();
    }

    public function login() {
        $this->render('auth/login');
    }

    public function authenticate() {
        $validator = new Validator(Request::all());
        $validator->required('email', 'Email')->required('password', 'Mot de passe');
        if (!$validator->passes()) {
            Session::setFlash('error', $validator->firstError());
            Request::back();
        }

        $result = $this->authService->authenticate(Request::post('email'), Request::post('password'));
        if (!$result) {
            Session::setFlash('error', 'Email ou mot de passe incorrect.');
            Request::back();
        }
        if (is_string($result)) {
            Session::setFlash('error', $result);
            Request::back();
        }

        $this->authService->login($result);
        Session::setFlash('success', 'Bon retour parmi nous !');
        Request::redirect($this->authService->getRedirectUrl(Session::get('user_role_slug')));
    }

    public function register() {
        $this->render('auth/register');
    }

    public function store() {
        $validator = new Validator(Request::all());
        $validator->required('name', 'Le nom')->required('email', 'Email')->required('password', 'Mot de passe')
            ->email('email')->minLength('password', 6)
            ->matches('password', 'password_confirm', 'Mot de passe', 'Confirmation');
        if (!$validator->passes()) {
            Session::setFlash('error', $validator->firstError());
            Request::back();
        }
        $userId = $this->authService->register(
            trim(Request::post('name')),
            trim(Request::post('email')),
            Request::post('password'),
            Request::post('phone') ?: ''
        );
        if (!$userId) {
            Session::setFlash('error', 'Cet email est déjà utilisé.');
            Request::back();
        }
        Session::setFlash('success', 'Compte créé avec succès. Connectez-vous !');
        Request::redirect('/auth/login');
    }

    public function logout() {
        Session::destroy();
        Request::redirect('/');
    }
}
