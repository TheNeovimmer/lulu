<?php
namespace App\Repositories;

class TestimonialRepository extends BaseRepository {
    protected string $table = 'testimonials';
    public function findByUser(int $userId): array {
        return $this->findAll(['user_id' => $userId], 'created_at DESC');
    }
    public function findApproved(): array {
        return $this->findAll(['status' => 'approved'], 'created_at DESC');
    }
}
