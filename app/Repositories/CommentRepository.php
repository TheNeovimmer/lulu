<?php
namespace App\Repositories;

class CommentRepository extends BaseRepository {
    protected string $table = 'comments';
    public function findByArticle(int $articleId): array {
        return $this->findAll(['article_id' => $articleId, 'status' => 'approved'], 'created_at ASC');
    }
    public function approve(int $id): void {
        $this->update($id, ['status' => 'approved']);
    }
    public function reject(int $id): void {
        $this->update($id, ['status' => 'rejected']);
    }
}
