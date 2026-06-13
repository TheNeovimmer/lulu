<?php
namespace App\Repositories;

class AvailabilityRepository extends BaseRepository {
    protected string $table = 'expert_availability';

    public function findByExpert(int $expertId): array {
        return $this->findAll(['expert_id' => $expertId], 'day_of_week ASC, start_time ASC');
    }

    public function findActiveByExpert(int $expertId): array {
        return $this->findAll(['expert_id' => $expertId, 'is_active' => 1], 'day_of_week ASC, start_time ASC');
    }

    public function saveSlots(int $expertId, array $slots): void {
        $this->execute("DELETE FROM expert_availability WHERE expert_id = ?", [$expertId]);
        foreach ($slots as $slot) {
            if (empty($slot['day']) || empty($slot['start']) || empty($slot['end'])) continue;
            $this->create([
                'expert_id' => $expertId,
                'day_of_week' => (int)$slot['day'],
                'start_time' => $slot['start'],
                'end_time' => $slot['end'],
                'is_active' => isset($slot['active']) ? (int)$slot['active'] : 1,
            ]);
        }
    }

    public function isExpertAvailable(int $expertId, string $dateTime): bool {
        $timestamp = strtotime($dateTime);
        $dayOfWeek = (int)date('N', $timestamp) - 1; // Monday=0
        $time = date('H:i:s', $timestamp);

        $slot = $this->rawOne(
            "SELECT id FROM expert_availability
             WHERE expert_id = ? AND day_of_week = ? AND is_active = 1
             AND start_time <= ? AND end_time >= ?",
            [$expertId, $dayOfWeek, $time, $time]
        );

        if (!$slot) return false;

        $date = date('Y-m-d', $timestamp);
        $exception = $this->rawOne(
            "SELECT id FROM expert_unavailable_dates WHERE expert_id = ? AND unavailable_date = ?",
            [$expertId, $date]
        );

        return !$exception;
    }

    public function getAvailableSlots(int $expertId, string $date): array {
        $timestamp = strtotime($date);
        $dayOfWeek = (int)date('N', $timestamp) - 1;
        $slots = $this->findAll([
            'expert_id' => $expertId,
            'day_of_week' => $dayOfWeek,
            'is_active' => 1,
        ], 'start_time ASC');

        if (empty($slots)) return [];

        $exception = $this->rawOne(
            "SELECT id FROM expert_unavailable_dates WHERE expert_id = ? AND unavailable_date = ?",
            [$expertId, $date]
        );
        if ($exception) return [];

        $existingAppointments = $this->raw(
            "SELECT appointment_date FROM appointments
             WHERE expert_id = ? AND DATE(appointment_date) = ? AND status != 'cancelled'",
            [$expertId, $date]
        );
        $bookedTimes = array_map(fn($a) => date('H:i:s', strtotime($a['appointment_date'])), $existingAppointments);

        $available = [];
        foreach ($slots as $slot) {
            $start = strtotime($slot['start_time']);
            $end = strtotime($slot['end_time']);
            while ($start < $end) {
                $timeStr = date('H:i:s', $start);
                $nextHour = $start + 3600;
                if (!in_array($timeStr, $bookedTimes)) {
                    $available[] = date('H:i', $start);
                }
                $start = $nextHour;
            }
        }
        return $available;
    }

    // Unavailable dates

    public function findUnavailableDates(int $expertId): array {
        return $this->raw(
            "SELECT * FROM expert_unavailable_dates WHERE expert_id = ? ORDER BY unavailable_date ASC",
            [$expertId]
        );
    }

    public function addUnavailableDate(int $expertId, string $date, ?string $reason = null): bool {
        try {
            $this->db->insert(
                "INSERT INTO expert_unavailable_dates (expert_id, unavailable_date, reason) VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE reason = VALUES(reason)",
                [$expertId, $date, $reason]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function removeUnavailableDate(int $expertId, string $date): void {
        $this->execute(
            "DELETE FROM expert_unavailable_dates WHERE expert_id = ? AND unavailable_date = ?",
            [$expertId, $date]
        );
    }
}
