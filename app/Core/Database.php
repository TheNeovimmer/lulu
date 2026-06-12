<?php
namespace App\Core;

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $this->pdo = new \PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]
        );
    }

    public static function getInstance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    public function getConnection() { return $this->pdo; }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch($sql, $params = []) { return $this->query($sql, $params)->fetch(); }
    public function fetchAll($sql, $params = []) { return $this->query($sql, $params)->fetchAll(); }
    public function insert($sql, $params = []) { $this->query($sql, $params); return $this->pdo->lastInsertId(); }
}
