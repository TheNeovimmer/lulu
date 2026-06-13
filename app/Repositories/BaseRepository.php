<?php
namespace App\Repositories;

use App\Core\Database;

abstract class BaseRepository {
    protected Database $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findById(int|string $id): ?array {
        $result = $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?", [$id]
        );
        return $result ?: null;
    }

    public function findAll(array $criteria = [], string $orderBy = 'created_at DESC', ?int $limit = null): array {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        if (!empty($criteria)) {
            $wheres = [];
            foreach ($criteria as $key => $value) {
                $wheres[] = "{$key} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $wheres);
        }
        $sql .= " ORDER BY {$orderBy}";
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        return $this->db->fetchAll($sql, $params);
    }

    public function create(array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        return $this->db->insert(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})",
            array_values($data)
        );
    }

    public function update(int|string $id, array $data): void {
        $sets = [];
        $params = [];
        foreach ($data as $key => $value) {
            $sets[] = "{$key} = ?";
            $params[] = $value;
        }
        $params[] = $id;
        $this->db->query(
            "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->primaryKey} = ?",
            $params
        );
    }

    public function delete(int|string $id): void {
        $this->db->query(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?", [$id]
        );
    }

    public function count(array $criteria = []): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        if (!empty($criteria)) {
            $wheres = [];
            foreach ($criteria as $key => $value) {
                $wheres[] = "{$key} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $wheres);
        }
        return $this->db->fetch($sql, $params)['count'] ?? 0;
    }

    public function exists(int|string $id): bool {
        return $this->findById($id) !== null;
    }

    public function paginate(int $page = 1, int $perPage = 20, array $criteria = [], string $orderBy = 'created_at DESC'): array {
        $total = $this->count($criteria);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $items = [];
        if ($total > 0) {
            $sql = "SELECT * FROM {$this->table}";
            $params = [];
            if (!empty($criteria)) {
                $wheres = [];
                foreach ($criteria as $key => $value) {
                    $wheres[] = "{$key} = ?";
                    $params[] = $value;
                }
                $sql .= " WHERE " . implode(' AND ', $wheres);
            }
            $sql .= " ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
            $items = $this->db->fetchAll($sql, $params);
        }

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
        ];
    }

    public function raw(string $sql, array $params = []): array {
        return $this->db->fetchAll($sql, $params);
    }

    public function rawOne(string $sql, array $params = []): ?array {
        $result = $this->db->fetch($sql, $params);
        return $result ?: null;
    }

    public function execute(string $sql, array $params = []): void {
        $this->db->query($sql, $params);
    }
}
