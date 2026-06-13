<?php
namespace App\Repositories;

class NotificationRepository extends BaseRepository {
    protected string $table = 'notifications';

    public function findUnreadByUser(int $userId): array {
        return $this->findAll(['user_id' => $userId, 'is_read' => 0], 'created_at DESC');
    }

    public function findByUser(int $userId): array {
        return $this->findAll(['user_id' => $userId], 'created_at DESC');
    }

    public function countUnread(int $userId): int {
        return $this->count(['user_id' => $userId, 'is_read' => 0]);
    }

    public function markAllRead(int $userId): void {
        $this->execute(
            "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0",
            [$userId]
        );
    }

    public function markRead(int $id, int $userId): void {
        $this->execute(
            "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?",
            [$id, $userId]
        );
    }

    public function createNotification(int $userId, string $type, string $title, string $message, string $link): int {
        $data = [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        return $this->create($data);
    }
}
