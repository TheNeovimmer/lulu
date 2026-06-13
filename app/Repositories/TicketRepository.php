<?php
namespace App\Repositories;

class TicketRepository extends BaseRepository {
    protected string $table = 'tickets';

    public function findByUser(int $userId): array {
        return $this->raw(
            "SELECT t.*, (SELECT COUNT(*) FROM ticket_messages WHERE ticket_id = t.id) as message_count
             FROM tickets t WHERE t.user_id = ? ORDER BY t.created_at DESC",
            [$userId]
        );
    }

    public function findWithMessages(int $id): ?array {
        return $this->rawOne(
            "SELECT t.*, u.name as user_name, u.email as user_email, e.name as expert_name
             FROM tickets t
             LEFT JOIN users u ON t.user_id = u.id
             LEFT JOIN users e ON t.assigned_to = e.id
             WHERE t.id = ?", [$id]
        );
    }

    public function findMessages(int $ticketId): array {
        return $this->raw(
            "SELECT tm.*, u.name as user_name FROM ticket_messages tm LEFT JOIN users u ON tm.user_id = u.id WHERE tm.ticket_id = ? ORDER BY tm.created_at ASC",
            [$ticketId]
        );
    }

    public function addMessage(int $ticketId, int $userId, string $message): int {
        return $this->db->insert(
            "INSERT INTO ticket_messages (ticket_id, user_id, message, created_at) VALUES (?, ?, ?, NOW())",
            [$ticketId, $userId, $message]
        );
    }

    public function allWithDetails(string $where = '', array $params = [], string $orderBy = 't.created_at DESC'): array {
        $sql = "SELECT t.*, u.name as user_name, u.email as user_email, e.name as expert_name
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN users e ON t.assigned_to = e.id";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $sql .= " ORDER BY {$orderBy}";
        return $this->raw($sql, $params);
    }

    public function assign(int $ticketId, int $expertId): void {
        $this->update($ticketId, ['assigned_to' => $expertId, 'status' => 'in_progress']);
    }

    public function close(int $ticketId): void {
        $this->update($ticketId, ['status' => 'closed']);
    }

    public function getAgents(): array {
        return $this->raw(
            "SELECT u.id, u.name FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug IN ('ctt', 'expert') ORDER BY u.name"
        );
    }

    public function getTicketCreatorId(int $ticketId): ?int {
        $ticket = $this->findById($ticketId);
        return $ticket ? $ticket['user_id'] : null;
    }

    public function getStats(): array {
        return [
            'open_tickets' => $this->count(['status' => 'open']),
            'resolved_today' => $this->rawOne(
                "SELECT COUNT(*) as count FROM tickets WHERE status = 'closed' AND DATE(updated_at) = CURDATE()"
            )['count'] ?? 0,
            'total' => $this->count(),
            'resolved' => $this->count(['status' => 'closed']),
        ];
    }

    public function getAvgResponseTime(): string {
        $result = $this->rawOne(
            "SELECT AVG(TIMESTAMPDIFF(MINUTE, t.created_at, tm.created_at)) as avg_time
             FROM tickets t
             JOIN ticket_messages tm ON tm.ticket_id = t.id
             AND tm.id = (SELECT MIN(id) FROM ticket_messages WHERE ticket_id = t.id AND user_id != t.user_id)
             WHERE tm.id IS NOT NULL"
        );
        return ($result && $result['avg_time']) ? round($result['avg_time']) . ' min' : 'N/A';
    }
}
