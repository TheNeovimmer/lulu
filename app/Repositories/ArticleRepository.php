<?php
namespace App\Repositories;

use App\Core\Database;

class ArticleRepository {
    private $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function findAllPublished($limit = 12, $offset = 0, $categoryId = null) {
        $sql = "SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.status = 'published'";
        $params = [];
        if ($categoryId) { $sql .= " AND a.category_id = ?"; $params[] = $categoryId; }
        $sql .= " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit; $params[] = $offset;
        return $this->db->fetchAll($sql, $params);
    }

    public function countPublished($categoryId = null) {
        $sql = "SELECT COUNT(*) as count FROM articles WHERE status = 'published'";
        $params = [];
        if ($categoryId) { $sql .= " AND category_id = ?"; $params[] = $categoryId; }
        return $this->db->fetch($sql, $params)['count'];
    }

    public function findBySlug($slug) {
        return $this->db->fetch("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.slug = ?", [$slug]);
    }

    public function findById($id) {
        return $this->db->fetch("SELECT * FROM articles WHERE id = ?", [$id]);
    }

    public function create($data) {
        return $this->db->insert(
            "INSERT INTO articles (category_id, user_id, title, slug, content, image, status, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$data['category_id'], $data['user_id'], $data['title'], $data['slug'], $data['content'], $data['image'] ?? null, $data['status'] ?? 'draft', $data['featured'] ?? 0]
        );
    }

    public function update($id, $data) {
        $this->db->query(
            "UPDATE articles SET category_id=?, title=?, slug=?, content=?, image=?, status=?, featured=? WHERE id=?",
            [$data['category_id'], $data['title'], $data['slug'], $data['content'], $data['image'] ?? null, $data['status'] ?? 'draft', $data['featured'] ?? 0, $id]
        );
    }

    public function delete($id) {
        $this->db->query("DELETE FROM articles WHERE id = ?", [$id]);
    }

    public function findAll($limit = 20, $offset = 0) {
        return $this->db->fetchAll("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.created_at DESC LIMIT ? OFFSET ?", [$limit, $offset]);
    }

    public function countAll() {
        return $this->db->fetch("SELECT COUNT(*) as count FROM articles")['count'];
    }

    public function generateSlug($title) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        return $slug;
    }
}
