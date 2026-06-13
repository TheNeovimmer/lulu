<?php
namespace App\Repositories;

class UserRepository extends BaseRepository {
    protected string $table = 'users';

    public function findByEmail(string $email): ?array {
        return $this->rawOne("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function findByRole(string $roleSlug, string $status = 'active'): array {
        return $this->raw(
            "SELECT u.*, r.name as role_name
             FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE r.slug = ? AND u.status = ?
             ORDER BY u.name ASC",
            [$roleSlug, $status]
        );
    }

    public function findByRoleId(int $roleId, string $status = 'active'): array {
        return $this->findAll(['role_id' => $roleId, 'status' => $status], 'name ASC');
    }

    public function getWithRole(int $id): ?array {
        return $this->rawOne(
            "SELECT u.*, r.name as role_name, r.slug as role_slug
             FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE u.id = ?", [$id]
        );
    }

    public function allWithRoles(array $criteria = []): array {
        $sql = "SELECT u.*, r.name as role_name, r.slug as role_slug FROM users u LEFT JOIN roles r ON u.role_id = r.id";
        $params = [];
        if (!empty($criteria)) {
            $wheres = [];
            foreach ($criteria as $key => $value) {
                if (str_contains($key, '.')) {
                    $wheres[] = "{$key} = ?";
                } else {
                    $wheres[] = "u.{$key} = ?";
                }
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $wheres);
        }
        $sql .= " ORDER BY u.created_at DESC";
        return $this->raw($sql, $params);
    }

    public function getMotherId(int $userId): int {
        $mother = $this->rawOne("SELECT id FROM mothers WHERE user_id = ?", [$userId]);
        if ($mother) {
            return $mother['id'];
        }
        return $this->db->insert("INSERT INTO mothers (user_id) VALUES (?)", [$userId]);
    }

    public function updatePassword(int $userId, string $newPassword): void {
        $this->update($userId, ['password' => password_hash($newPassword, PASSWORD_BCRYPT)]);
    }

    public function verifyPassword(int $userId, string $password): bool {
        $user = $this->findById($userId);
        return $user && password_verify($password, $user['password']);
    }

    public function getRoleSlug(int $userId): ?string {
        $result = $this->rawOne(
            "SELECT r.slug FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?",
            [$userId]
        );
        return $result['slug'] ?? null;
    }

    public function findByMotherId(int $motherId): ?array {
        return $this->rawOne(
            "SELECT u.* FROM users u JOIN mothers m ON u.id = m.user_id WHERE m.id = ?",
            [$motherId]
        );
    }

    public function getRoleIdBySlug(string $slug): ?int {
        $result = $this->rawOne("SELECT id FROM roles WHERE slug = ?", [$slug]);
        return $result['id'] ?? null;
    }
}
