<?php
namespace App\Repositories;

class MotherRepository extends BaseRepository {
    protected string $table = 'mothers';

    public function findByUserId(int $userId): ?array {
        return $this->rawOne("SELECT * FROM mothers WHERE user_id = ?", [$userId]);
    }

    public function findOrCreate(int $userId): int {
        $mother = $this->findByUserId($userId);
        if ($mother) return $mother['id'];
        return $this->create(['user_id' => $userId]);
    }

    public function findWithDetails(int $userId): ?array {
        return $this->rawOne(
            "SELECT u.*, m.id as mother_id, m.date_of_birth, m.city
             FROM users u
             LEFT JOIN mothers m ON u.id = m.user_id
             WHERE u.id = ?", [$userId]
        );
    }

    public function allWithPregnancies(): array {
        return $this->raw(
            "SELECT u.*, p.due_date, p.week as weeks_gestation, p.created_at as pregnancy_since
             FROM users u
             LEFT JOIN mothers m ON u.id = m.user_id
             LEFT JOIN pregnancies p ON m.id = p.mother_id
             JOIN roles r ON u.role_id = r.id
             WHERE r.slug = 'maman'
             ORDER BY u.created_at DESC"
        );
    }
}
