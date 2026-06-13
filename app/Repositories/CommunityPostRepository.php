<?php
namespace App\Repositories;

class CommunityPostRepository extends BaseRepository {
    protected string $table = 'community_posts';

    public function findPublished(): array {
        return $this->raw(
            "SELECT cp.*, u.name as author_name,
                    (SELECT COUNT(*) FROM community_comments cc WHERE cc.post_id = cp.id) as answers_count,
                    (SELECT COUNT(*) FROM community_likes cl WHERE cl.post_id = cp.id) as likes_count
             FROM community_posts cp
             LEFT JOIN users u ON cp.user_id = u.id
             WHERE cp.status = 'published'
             ORDER BY cp.created_at DESC"
        );
    }

    public function findWithDetails(int $id): ?array {
        return $this->rawOne(
            "SELECT cp.*, u.name as author_name
             FROM community_posts cp
             LEFT JOIN users u ON cp.user_id = u.id
             WHERE cp.id = ?", [$id]
        );
    }

    public function findAnswers(int $postId): array {
        return $this->raw(
            "SELECT cc.*, u.name as author_name, u.role_id,
                    (SELECT slug FROM roles WHERE id = u.role_id) as role_slug
             FROM community_comments cc
             JOIN users u ON cc.user_id = u.id
             WHERE cc.post_id = ?
             ORDER BY cc.created_at ASC",
            [$postId]
        );
    }

    public function addComment(int $postId, int $userId, string $content): int {
        return $this->db->insert(
            "INSERT INTO community_comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())",
            [$postId, $userId, $content]
        );
    }

    public function getPostAuthorId(int $postId): ?int {
        $post = $this->findById($postId);
        return $post ? $post['user_id'] : null;
    }

    public function findComment(int $commentId): ?array {
        return $this->rawOne("SELECT * FROM community_comments WHERE id = ?", [$commentId]);
    }

    public function deleteComment(int $commentId): void {
        $this->execute("DELETE FROM community_comments WHERE id = ?", [$commentId]);
    }

    public function hasLiked(int $postId, int $userId): bool {
        $like = $this->rawOne(
            "SELECT id FROM community_likes WHERE post_id = ? AND user_id = ?",
            [$postId, $userId]
        );
        return $like !== null;
    }

    public function toggleLike(int $postId, int $userId): bool {
        if ($this->hasLiked($postId, $userId)) {
            $this->execute("DELETE FROM community_likes WHERE post_id = ? AND user_id = ?", [$postId, $userId]);
            $this->syncLikesCount($postId);
            return false;
        }
        $this->db->insert(
            "INSERT INTO community_likes (post_id, user_id) VALUES (?, ?)",
            [$postId, $userId]
        );
        $this->syncLikesCount($postId);
        return true;
    }

    public function syncLikesCount(int $postId): void {
        $this->execute(
            "UPDATE community_posts SET likes_count = (SELECT COUNT(*) FROM community_likes WHERE post_id = ?) WHERE id = ?",
            [$postId, $postId]
        );
    }

    public function getLikeCount(int $postId): int {
        $result = $this->rawOne("SELECT COUNT(*) as count FROM community_likes WHERE post_id = ?", [$postId]);
        return $result['count'] ?? 0;
    }
}
