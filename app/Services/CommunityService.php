<?php
namespace App\Services;

use App\Repositories\CommunityPostRepository;

class CommunityService {
    private CommunityPostRepository $postRepo;
    private NotificationService $notifService;

    public function __construct(?CommunityPostRepository $postRepo = null, ?NotificationService $notifService = null) {
        $this->postRepo = $postRepo ?? new CommunityPostRepository();
        $this->notifService = $notifService ?? new NotificationService();
    }

    public function createPost(int $userId, string $title, string $content): int {
        return $this->postRepo->create([
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
            'status' => 'published',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function addComment(int $postId, int $userId, string $content): void {
        $this->postRepo->addComment($postId, $userId, $content);
        $authorId = $this->postRepo->getPostAuthorId($postId);
        if ($authorId && $authorId != $userId) {
            $this->notifService->sendQuestionAnswered($authorId, $postId);
        }
    }

    public function toggleLike(int $postId, int $userId): array {
        $liked = $this->postRepo->toggleLike($postId, $userId);
        $count = $this->postRepo->getLikeCount($postId);
        return ['liked' => $liked, 'count' => $count];
    }

    public function deleteComment(int $commentId, int $userId): ?int {
        $comment = $this->postRepo->findComment($commentId);
        if (!$comment || $comment['user_id'] != $userId) return null;
        $this->postRepo->deleteComment($commentId);
        return $comment['post_id'];
    }

    public function getPublishedPosts(): array {
        return $this->postRepo->findPublished();
    }

    public function getPostWithDetails(int $id): ?array {
        $post = $this->postRepo->findWithDetails($id);
        if (!$post) return null;
        $userId = \App\Core\Session::get('user_id');
        $post['is_expert'] = false;
        $post['user_liked'] = $userId ? $this->postRepo->hasLiked($id, $userId) : false;
        $post['likes_count'] = $post['likes_count'] ?? $this->postRepo->getLikeCount($id);
        $answers = $this->postRepo->findAnswers($id);
        foreach ($answers as &$a) {
            $a['is_expert'] = ($a['role_slug'] ?? '') === 'expert';
        }
        return ['post' => $post, 'answers' => $answers];
    }
}
