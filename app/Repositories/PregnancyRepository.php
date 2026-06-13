<?php
namespace App\Repositories;

class PregnancyRepository extends BaseRepository {
    protected string $table = 'pregnancies';

    public function findActiveByMother(int $motherId): ?array {
        return $this->rawOne(
            "SELECT * FROM pregnancies WHERE mother_id = ? AND status = 'active'",
            [$motherId]
        );
    }

    public function upsert(int $motherId, string $dueDate, ?string $notes): void {
        $existing = $this->rawOne("SELECT id FROM pregnancies WHERE mother_id = ?", [$motherId]);
        if ($existing) {
            $this->update($existing['id'], [
                'due_date' => $dueDate,
                'notes' => $notes,
            ]);
        } else {
            $this->create([
                'mother_id' => $motherId,
                'due_date' => $dueDate,
                'notes' => $notes,
                'status' => 'active',
            ]);
        }
    }

    public function complete(int $motherId): void {
        $this->execute(
            "UPDATE pregnancies SET status = 'completed' WHERE mother_id = ? AND status = 'active'",
            [$motherId]
        );
    }

    public function getGestationalWeeks(string $dueDate): int {
        $due = new \DateTime($dueDate);
        $now = new \DateTime();
        $start = (clone $due)->modify('-280 days');
        if ($now < $start) return 0;
        $days = (int)$start->diff($now)->days;
        return min(40, (int)floor($days / 7));
    }

    public function getDaysRemaining(string $dueDate): int {
        $due = new \DateTime($dueDate);
        $now = new \DateTime();
        return $due > $now ? (int)$now->diff($due)->days : 0;
    }

    public function getMilestones(string $dueDate): array {
        $due = new \DateTime($dueDate);
        return [
            ['title' => 'Échographie de datation (1er trimestre)', 'date' => (clone $due)->modify('-196 days')->format('Y-m-d')],
            ['title' => 'Échographie morphologique (2e trimestre)', 'date' => (clone $due)->modify('-126 days')->format('Y-m-d')],
            ['title' => 'Dépistage du diabète gestationnel', 'date' => (clone $due)->modify('-98 days')->format('Y-m-d')],
            ['title' => 'Échographie de croissance (3e trimestre)', 'date' => (clone $due)->modify('-56 days')->format('Y-m-d')],
            ['title' => "Consultation d'anesthésie", 'date' => (clone $due)->modify('-28 days')->format('Y-m-d')],
            ['title' => 'Date prévue d\'accouchement', 'date' => $dueDate],
        ];
    }
}
