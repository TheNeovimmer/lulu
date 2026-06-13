<?php
namespace App\Repositories;

class NewsletterRepository extends BaseRepository {
    protected string $table = 'newsletters';
    public function findByEmail(string $email): ?array {
        return $this->rawOne("SELECT * FROM newsletters WHERE email = ?", [$email]);
    }
}
