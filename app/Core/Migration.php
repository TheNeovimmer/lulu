<?php
namespace App\Core;

class Migration {
    private Database $db;
    private string $migrationsDir;

    public function __construct(string $migrationsDir = null) {
        $this->db = Database::getInstance();
        $this->migrationsDir = $migrationsDir ?: __DIR__ . '/../../database/migrations';
        $this->ensureMigrationsTable();
    }

    private function ensureMigrationsTable(): void {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                filename VARCHAR(255) NOT NULL UNIQUE,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )"
        );
    }

    private function getExecuted(): array {
        $rows = $this->db->fetchAll("SELECT filename FROM migrations ORDER BY filename");
        return array_column($rows, 'filename');
    }

    public function migrate(): void {
        $executed = $this->getExecuted();
        $files = glob($this->migrationsDir . '/*.sql');
        sort($files);

        $count = 0;
        foreach ($files as $file) {
            $filename = basename($file);
            if (in_array($filename, $executed, true)) {
                continue;
            }
            $sql = file_get_contents($file);
            if ($sql === false || trim($sql) === '') {
                continue;
            }
            // Split by semicolons for multi-statement SQL
            $statements = array_filter(
                array_map('trim', explode(';', $sql)),
                fn($s) => $s !== ''
            );
            foreach ($statements as $statement) {
                $this->db->query($statement . ';');
            }
            $this->db->insert(
                "INSERT INTO migrations (filename) VALUES (?)",
                [$filename]
            );
            $count++;
        }
        echo "Executed {$count} migration(s).\n";
    }

    public function rollback(): void {
        $executed = $this->getExecuted();
        if (empty($executed)) {
            echo "No migrations to rollback.\n";
            return;
        }
        $last = array_pop($executed);
        $this->db->query("DELETE FROM migrations WHERE filename = ?", [$last]);
        echo "Rolled back: {$last}\n";
    }
}
