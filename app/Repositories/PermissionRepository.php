<?php
namespace App\Repositories;

use App\Core\Database;

class PermissionRepository {
    private $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function hasPermission($userId, $permissionSlug) {
        return $this->db->fetch(
            "SELECT 1 FROM users u
             JOIN role_permissions rp ON u.role_id = rp.role_id
             JOIN permissions p ON rp.permission_id = p.id
             WHERE u.id = ? AND p.slug = ?",
            [$userId, $permissionSlug]
        ) !== false;
    }

    public function getUserPermissions($userId) {
        return $this->db->fetchAll(
            "SELECT p.slug, p.name FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             JOIN users u ON u.role_id = rp.role_id
             WHERE u.id = ?", [$userId]
        );
    }

    public function getRolePermissions($roleId) {
        return $this->db->fetchAll(
            "SELECT p.* FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             WHERE rp.role_id = ?", [$roleId]
        );
    }

    public function getAllPermissions() {
        return $this->db->fetchAll("SELECT * FROM permissions ORDER BY group_name, name");
    }

    public function getAllRoles() {
        return $this->db->fetchAll("SELECT * FROM roles ORDER BY name");
    }
}
