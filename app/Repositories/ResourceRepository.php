<?php
namespace App\Repositories;

class ResourceRepository extends BaseRepository {
    protected string $table = 'resources';

    public function findBySlug(string $slug): ?array {
        return $this->rawOne("SELECT * FROM resources WHERE slug = ?", [$slug]);
    }

    public function findByUser(int $userId): array {
        return $this->raw(
            "SELECT r.*, c.name as category_name
             FROM resources r
             LEFT JOIN categories c ON r.category_id = c.id
             WHERE r.user_id = ?
             ORDER BY r.created_at DESC",
            [$userId]
        );
    }

    public function findByUserWithId(int $id, int $userId): ?array {
        return $this->rawOne("SELECT * FROM resources WHERE id = ? AND user_id = ?", [$id, $userId]);
    }

    public function findPublished(): array {
        return $this->raw(
            "SELECT r.*, c.name as category_name
             FROM resources r
             LEFT JOIN categories c ON r.category_id = c.id
             WHERE r.status = 'published'
             ORDER BY r.created_at DESC"
        );
    }

    public function incrementDownloads(int $id): void {
        $this->execute("UPDATE resources SET downloads_count = downloads_count + 1 WHERE id = ?", [$id]);
    }
}
