<?php
namespace App\Repositories;

class AppointmentRepository extends BaseRepository {
    protected string $table = 'appointments';

    public function findByMother(int $motherId): array {
        return $this->raw(
            "SELECT a.*, u.name as expert_name, u.specialty as expert_specialty
             FROM appointments a
             JOIN users u ON a.expert_id = u.id
             WHERE a.mother_id = ?
             ORDER BY a.appointment_date DESC",
            [$motherId]
        );
    }

    public function findByExpert(int $expertId): array {
        return $this->raw(
            "SELECT a.*, u.name as mother_name
             FROM appointments a
             JOIN mothers m ON a.mother_id = m.id
             JOIN users u ON m.user_id = u.id
             WHERE a.expert_id = ?
             ORDER BY a.appointment_date ASC",
            [$expertId]
        );
    }

    public function findWithMother(int $id, int $motherId): ?array {
        return $this->rawOne(
            "SELECT * FROM appointments WHERE id = ? AND mother_id = ?",
            [$id, $motherId]
        );
    }

    public function findWithExpert(int $id, int $expertId): ?array {
        return $this->rawOne(
            "SELECT * FROM appointments WHERE id = ? AND expert_id = ?",
            [$id, $expertId]
        );
    }

    public function updateStatus(int $id, string $status): void {
        $this->update($id, ['status' => $status]);
    }

    public function getMotherUserId(int $appointmentId): ?int {
        $result = $this->rawOne(
            "SELECT m.user_id FROM appointments a JOIN mothers m ON a.mother_id = m.id WHERE a.id = ?",
            [$appointmentId]
        );
        return $result['user_id'] ?? null;
    }

    public function getActiveExperts(): array {
        $roleExpert = $this->rawOne("SELECT id FROM roles WHERE slug = 'expert'");
        if (!$roleExpert) return [];
        return $this->raw(
            "SELECT id, name, specialty FROM users WHERE role_id = ? AND status = 'active' ORDER BY name ASC",
            [$roleExpert['id']]
        );
    }
}
