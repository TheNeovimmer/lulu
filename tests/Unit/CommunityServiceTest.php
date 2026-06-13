<?php
namespace Tests\Unit;

use App\Repositories\CommunityPostRepository;
use App\Services\CommunityService;
use App\Services\NotificationService;
use PHPUnit\Framework\TestCase;

class CommunityServiceTest extends TestCase {
    private CommunityPostRepository $postRepo;
    private NotificationService $notifService;
    private CommunityService $service;

    protected function setUp(): void {
        $this->postRepo = $this->createMock(CommunityPostRepository::class);
        $this->notifService = $this->createMock(NotificationService::class);
        $this->service = new CommunityService($this->postRepo, $this->notifService);
    }

    public function testCreatePostReturnsId(): void {
        $this->postRepo->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['user_id'] === 1
                    && $data['title'] === 'Mon titre'
                    && $data['status'] === 'published';
            }))
            ->willReturn(5);

        $id = $this->service->createPost(1, 'Mon titre', 'Contenu');
        $this->assertSame(5, $id);
    }

    public function testAddCommentSendsNotificationToDifferentAuthor(): void {
        $this->postRepo->expects($this->once())
            ->method('addComment')
            ->with(1, 2, 'Bonjour');

        $this->postRepo->expects($this->once())
            ->method('getPostAuthorId')
            ->with(1)
            ->willReturn(1);

        $this->notifService->expects($this->once())
            ->method('sendQuestionAnswered')
            ->with(1, 1);

        $this->service->addComment(1, 2, 'Bonjour');
    }

    public function testAddCommentDoesNotNotifySelf(): void {
        $this->postRepo->expects($this->once())
            ->method('addComment')
            ->with(1, 5, 'Réponse');

        $this->postRepo->expects($this->once())
            ->method('getPostAuthorId')
            ->with(1)
            ->willReturn(5);

        $this->notifService->expects($this->never())
            ->method('sendQuestionAnswered');

        $this->service->addComment(1, 5, 'Réponse');
    }

    public function testToggleLikeReturnsLikedTrue(): void {
        $this->postRepo->expects($this->once())
            ->method('toggleLike')
            ->with(1, 2)
            ->willReturn(true);

        $this->postRepo->expects($this->once())
            ->method('getLikeCount')
            ->with(1)
            ->willReturn(5);

        $result = $this->service->toggleLike(1, 2);
        $this->assertSame(['liked' => true, 'count' => 5], $result);
    }

    public function testToggleLikeReturnsLikedFalse(): void {
        $this->postRepo->expects($this->once())
            ->method('toggleLike')
            ->with(1, 2)
            ->willReturn(false);

        $this->postRepo->expects($this->once())
            ->method('getLikeCount')
            ->with(1)
            ->willReturn(3);

        $result = $this->service->toggleLike(1, 2);
        $this->assertSame(['liked' => false, 'count' => 3], $result);
    }

    public function testDeleteCommentOwnedByUser(): void {
        $this->postRepo->expects($this->once())
            ->method('findComment')
            ->with(10)
            ->willReturn(['id' => 10, 'user_id' => 3, 'post_id' => 1, 'content' => 'test']);

        $this->postRepo->expects($this->once())
            ->method('deleteComment')
            ->with(10);

        $postId = $this->service->deleteComment(10, 3);
        $this->assertSame(1, $postId);
    }

    public function testDeleteCommentNotOwnedReturnsNull(): void {
        $this->postRepo->expects($this->once())
            ->method('findComment')
            ->with(10)
            ->willReturn(['id' => 10, 'user_id' => 3, 'post_id' => 1, 'content' => 'test']);

        $this->postRepo->expects($this->never())
            ->method('deleteComment');

        $postId = $this->service->deleteComment(10, 99);
        $this->assertNull($postId);
    }

    public function testDeleteCommentNotFoundReturnsNull(): void {
        $this->postRepo->expects($this->once())
            ->method('findComment')
            ->with(999)
            ->willReturn(null);

        $postId = $this->service->deleteComment(999, 1);
        $this->assertNull($postId);
    }

    public function testGetPublishedPostsDelegates(): void {
        $expected = [
            ['id' => 1, 'title' => 'Post 1'],
            ['id' => 2, 'title' => 'Post 2'],
        ];
        $this->postRepo->expects($this->once())
            ->method('findPublished')
            ->willReturn($expected);

        $result = $this->service->getPublishedPosts();
        $this->assertSame($expected, $result);
    }

    public function testGetPostWithDetailsNoPostReturnsNull(): void {
        $this->postRepo->expects($this->once())
            ->method('findWithDetails')
            ->with(999)
            ->willReturn(null);

        $result = $this->service->getPostWithDetails(999);
        $this->assertNull($result);
    }

    public function testGetPostWithDetailsReturnsPostAndAnswers(): void {
        $post = ['id' => 1, 'title' => 'Question', 'content' => 'Help'];
        $this->postRepo->expects($this->once())
            ->method('findWithDetails')
            ->with(1)
            ->willReturn($post);

        $answers = [
            ['id' => 1, 'content' => 'Réponse', 'role_slug' => 'expert'],
            ['id' => 2, 'content' => 'Merci', 'role_slug' => 'maman'],
        ];
        $this->postRepo->expects($this->once())
            ->method('findAnswers')
            ->with(1)
            ->willReturn($answers);

        $result = $this->service->getPostWithDetails(1);
        $this->assertNotNull($result);
        $this->assertSame($post, $result['post']);
        $this->assertCount(2, $result['answers']);
        $this->assertTrue($result['answers'][0]['is_expert']);
        $this->assertFalse($result['answers'][1]['is_expert']);
    }
}
