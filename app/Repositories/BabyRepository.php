<?php
namespace App\Repositories;

class BabyRepository extends BaseRepository {
    protected string $table = 'babies';

    public function findByMother(int $motherId): ?array {
        return $this->rawOne(
            "SELECT b.*, g.weight as last_weight, g.height as last_height
             FROM babies b
             LEFT JOIN growth_records g ON b.id = g.baby_id
             AND g.record_date = (SELECT MAX(record_date) FROM growth_records WHERE baby_id = b.id)
             WHERE b.mother_id = ?
             LIMIT 1",
            [$motherId]
        );
    }

    public function findMemories(int $babyId): array {
        return $this->raw(
            "SELECT * FROM baby_memories WHERE baby_id = ? ORDER BY event_date DESC",
            [$babyId]
        );
    }

    public function findMilestones(int $babyId): array {
        return $this->raw(
            "SELECT * FROM baby_milestones WHERE baby_id = ? AND achieved_date IS NOT NULL ORDER BY achieved_date ASC",
            [$babyId]
        );
    }

    public function findMilestonesAsArray(int $babyId): array {
        $milestones = $this->findMilestones($babyId);
        return array_column($milestones, 'achieved_date', 'milestone_key');
    }

    public function addMemory(int $babyId, string $title, string $content, string $eventDate): int {
        return $this->db->insert(
            "INSERT INTO baby_memories (baby_id, title, content, event_date) VALUES (?, ?, ?, ?)",
            [$babyId, $title, $content, $eventDate]
        );
    }

    public function updateMemory(int $memoryId, int $babyId, string $title, string $content, string $eventDate): void {
        $this->execute(
            "UPDATE baby_memories SET title = ?, content = ?, event_date = ? WHERE id = ? AND baby_id = ?",
            [$title, $content, $eventDate, $memoryId, $babyId]
        );
    }

    public function deleteMemory(int $memoryId, int $babyId): void {
        $this->execute("DELETE FROM baby_memories WHERE id = ? AND baby_id = ?", [$memoryId, $babyId]);
    }

    public function updateMilestones(int $babyId, array $selectedMilestones): void {
        $this->db->getConnection()->beginTransaction();
        try {
            $this->execute("DELETE FROM baby_milestones WHERE baby_id = ?", [$babyId]);
            foreach ($selectedMilestones as $key => $achieved) {
                if ($achieved) {
                    $this->db->insert(
                        "INSERT INTO baby_milestones (baby_id, milestone_key, achieved_date) VALUES (?, ?, ?)",
                        [$babyId, $key, date('Y-m-d')]
                    );
                }
            }
            $this->db->getConnection()->commit();
        } catch (\Exception $e) {
            $this->db->getConnection()->rollBack();
            throw $e;
        }
    }

    public function findGrowthRecords(int $babyId, string $birthDate): array {
        return $this->raw(
            "SELECT *, DATEDIFF(record_date, ?) as age_days FROM growth_records WHERE baby_id = ? ORDER BY record_date ASC",
            [$birthDate, $babyId]
        );
    }

    public function addGrowthRecord(int $babyId, string $date, ?float $weight, ?float $height, ?float $headCircumference): int {
        return $this->db->insert(
            "INSERT INTO growth_records (baby_id, record_date, weight, height, head_circumference) VALUES (?, ?, ?, ?, ?)",
            [$babyId, $date, $weight, $height, $headCircumference]
        );
    }

    public function deleteGrowthRecord(int $recordId, int $babyId): void {
        $this->execute("DELETE FROM growth_records WHERE id = ? AND baby_id = ?", [$recordId, $babyId]);
    }

    public function findVaccinations(int $babyId): array {
        return $this->raw(
            "SELECT id, vaccine_name, due_date as scheduled_date, administered_date, status, notes FROM vaccinations WHERE baby_id = ? ORDER BY due_date ASC",
            [$babyId]
        );
    }

    public function addVaccination(int $babyId, string $vaccineName, ?string $dueDate, ?string $administeredDate, string $status, ?string $notes): int {
        return $this->db->insert(
            "INSERT INTO vaccinations (baby_id, vaccine_name, due_date, administered_date, status, notes) VALUES (?, ?, ?, ?, ?, ?)",
            [$babyId, $vaccineName, $dueDate, $administeredDate, $status, $notes]
        );
    }

    public function deleteVaccination(int $vaccinationId, int $babyId): void {
        $this->execute("DELETE FROM vaccinations WHERE id = ? AND baby_id = ?", [$vaccinationId, $babyId]);
    }
}
