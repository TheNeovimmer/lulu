<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Core\Session;

class AuthService {
    private UserRepository $userRepo;

    public function __construct(?UserRepository $userRepo = null) {
        $this->userRepo = $userRepo ?? new UserRepository();
    }

    public function authenticate(string $email, string $password): ?array {
        $user = $this->userRepo->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }
        if ($user['status'] !== 'active') {
            return ['error' => 'Votre compte est suspendu. Contactez l\'administrateur.'];
        }
        return $user;
    }

    public function login(array $user): void {
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_avatar', $user['avatar'] ? '/uploads/avatars/' . $user['avatar'] : null);

        $role = $this->userRepo->getWithRole($user['id']);
        Session::set('user_role_id', $role['role_id'] ?? null);
        Session::set('user_role_slug', $role['role_slug'] ?? null);
    }

    public function register(string $name, string $email, string $password, string $phone = ''): ?int {
        $existing = $this->userRepo->findByEmail($email);
        if ($existing) return null;

        $roleId = $this->userRepo->getRoleIdBySlug('maman');
        if (!$roleId) return null;

        return $this->userRepo->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'phone' => $phone,
            'role_id' => $roleId,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getRedirectUrl(string $roleSlug): string {
        return match ($roleSlug) {
            'admin' => '/admin',
            'expert' => '/expert/dashboard',
            'ctt' => '/ctt/dashboard',
            default => '/dashboard',
        };
    }
}
