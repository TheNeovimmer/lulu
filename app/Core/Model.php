<?php
namespace App\Core;

abstract class Model {
    protected static Database $db;
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    public static function init(): void {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
    }

    public static function find(int|string $id): ?array {
        static::init();
        $table = static::$table;
        $pk = static::$primaryKey;
        return self::$db->fetch("SELECT * FROM {$table} WHERE {$pk} = ?", [$id]) ?: null;
    }

    public static function all(array $criteria = [], string $orderBy = 'created_at DESC'): array {
        static::init();
        $table = static::$table;
        $sql = "SELECT * FROM {$table}";
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
        return self::$db->fetchAll($sql, $params);
    }

    public static function create(array $data): int {
        static::init();
        $table = static::$table;
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        return self::$db->insert(
            "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})",
            array_values($data)
        );
    }

    public static function update(int|string $id, array $data): void {
        static::init();
        $table = static::$table;
        $pk = static::$primaryKey;
        $sets = [];
        $params = [];
        foreach ($data as $key => $value) {
            $sets[] = "{$key} = ?";
            $params[] = $value;
        }
        $params[] = $id;
        self::$db->query(
            "UPDATE {$table} SET " . implode(', ', $sets) . " WHERE {$pk} = ?",
            $params
        );
    }

    public static function delete(int|string $id): void {
        static::init();
        $table = static::$table;
        $pk = static::$primaryKey;
        self::$db->query("DELETE FROM {$table} WHERE {$pk} = ?", [$id]);
    }

    public static function count(array $criteria = []): int {
        static::init();
        $table = static::$table;
        $sql = "SELECT COUNT(*) as count FROM {$table}";
        $params = [];
        if (!empty($criteria)) {
            $wheres = [];
            foreach ($criteria as $key => $value) {
                $wheres[] = "{$key} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $wheres);
        }
        return self::$db->fetch($sql, $params)['count'] ?? 0;
    }

    public static function raw(string $sql, array $params = []): array {
        static::init();
        return self::$db->fetchAll($sql, $params);
    }

    public static function rawOne(string $sql, array $params = []): ?array {
        static::init();
        $result = self::$db->fetch($sql, $params);
        return $result ?: null;
    }

    public static function execute(string $sql, array $params = []): void {
        static::init();
        self::$db->query($sql, $params);
    }
}
