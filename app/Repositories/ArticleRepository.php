<?php
namespace App\Repositories;

class ArticleRepository extends BaseRepository {
    protected string $table = 'articles';

    public function findBySlug(string $slug): ?array {
        return $this->rawOne(
            "SELECT a.*, c.name as category_name
             FROM articles a
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.slug = ?",
            [$slug]
        );
    }

    public function findPublished(int $limit = 10, ?int $categoryId = null, int $page = 1): array {
        $sql = "SELECT a.*, c.name as category_name
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                WHERE a.status = 'published'";
        $params = [];
        if ($categoryId) {
            $sql .= " AND a.category_id = ?";
            $params[] = $categoryId;
        }
        $sql .= " ORDER BY a.created_at DESC";

        $countSql = str_replace("SELECT a.*, c.name as category_name", "SELECT COUNT(*) as count", $sql);
        $total = $this->rawOne($countSql, $params)['count'] ?? 0;

        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $items = $this->raw($sql, $params);
        return ['items' => $items, 'total' => $total, 'page' => $page, 'limit' => $limit];
    }

    public function findByUser(int $userId): array {
        return $this->raw(
            "SELECT a.*, c.name as category_name
             FROM articles a
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.user_id = ?
             ORDER BY a.created_at DESC",
            [$userId]
        );
    }

    public function findByUserWithId(int $id, int $userId): ?array {
        return $this->rawOne("SELECT * FROM articles WHERE id = ? AND user_id = ?", [$id, $userId]);
    }

    public function incrementViews(int $id): void {
        $this->execute("UPDATE articles SET views_count = views_count + 1 WHERE id = ?", [$id]);
    }

    public function getPopular(int $limit = 5): array {
        return $this->findAll(['status' => 'published'], 'views_count DESC', $limit);
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool {
        if ($excludeId) {
            $result = $this->rawOne("SELECT id FROM articles WHERE slug = ? AND id != ?", [$slug, $excludeId]);
        } else {
            $result = $this->rawOne("SELECT id FROM articles WHERE slug = ?", [$slug]);
        }
        return $result !== null;
    }

    public function generateSlug(string $title, ?int $excludeId = null): string {
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', $title)));
        $slug = trim($slug, '-') ?: 'article';
        $original = $slug;
        $i = 1;
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }
}
