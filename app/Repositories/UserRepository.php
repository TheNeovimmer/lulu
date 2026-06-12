<?php
namespace App\Repositories;

use App\Core\Database;

class UserRepository {
    private $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function findByEmail($email) {
        return $this->db->fetch("SELECT u.*, r.slug as role_slug, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.email = ?", [$email]);
    }

    public function findById($id) {
        return $this->db->fetch("SELECT u.*, r.slug as role_slug, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = ?", [$id]);
    }

    public function create($data) {
        $roleId = $this->db->fetch("SELECT id FROM roles WHERE slug = ?", [$data['role'] ?? 'maman'])['id'];
        return $this->db->insert(
            "INSERT INTO users (role_id, name, email, password, phone) VALUES (?, ?, ?, ?, ?)",
            [$roleId, $data['name'], $data['email'], password_hash($data['password'], PASSWORD_BCRYPT), $data['phone'] ?? null]
        );
    }

    public function updateAvatar($id, $avatar) {
        $this->db->query("UPDATE users SET avatar = ? WHERE id = ?", [$avatar, $id]);
    }

    public function updateProfile($id, $data) {
        $this->db->query("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?", [$data['name'], $data['email'], $data['phone'] ?? null, $id]);
    }

    public function updatePassword($id, $password) {
        $this->db->query("UPDATE users SET password = ? WHERE id = ?", [password_hash($password, PASSWORD_BCRYPT), $id]);
    }

    public function findAll() {
        return $this->db->fetchAll("SELECT u.*, r.name as role_name, r.slug as role_slug FROM users u LEFT JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC");
    }

    public function findAllByRole($roleSlug) {
        return $this->db->fetchAll("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = ? ORDER BY u.created_at DESC", [$roleSlug]);
    }

    public function toggleRole($id) {
        $user = $this->findById($id);
        $newRoleSlug = $user['role_slug'] === 'admin' ? 'maman' : 'admin';
        $newRoleId = $this->db->fetch("SELECT id FROM roles WHERE slug = ?", [$newRoleSlug])['id'];
        $this->db->query("UPDATE users SET role_id = ? WHERE id = ?", [$newRoleId, $id]);
    }

    public function updateStatus($id, $status) {
        $this->db->query("UPDATE users SET status = ? WHERE id = ?", [$status, $id]);
    }

    public function delete($id) {
        $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
    }

    public function count() {
        return $this->db->fetch("SELECT COUNT(*) as count FROM users")['count'];
    }

    public function countByRole($roleSlug) {
        return $this->db->fetch("SELECT COUNT(*) as count FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = ?", [$roleSlug])['count'];
    }
}
