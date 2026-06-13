<?php
namespace Tests\Unit;

use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use App\Enums\ArticleStatus;
use App\Enums\CommentStatus;
use App\Enums\NotificationType;
use App\Enums\PostStatus;
use App\Enums\PregnancyStatus;
use App\Enums\ResourceType;
use App\Enums\TestimonialStatus;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserStatus;
use App\Enums\VaccinationStatus;
use PHPUnit\Framework\TestCase;

class EnumsTest extends TestCase {
    public function testUserStatusValues(): void {
        $this->assertSame('active', UserStatus::ACTIVE->value);
        $this->assertSame('suspended', UserStatus::SUSPENDED->value);
        $this->assertSame('banned', UserStatus::BANNED->value);
    }

    public function testUserStatusCases(): void {
        $cases = UserStatus::cases();
        $this->assertCount(3, $cases);
    }

    public function testAppointmentStatusValues(): void {
        $this->assertSame('pending', AppointmentStatus::PENDING->value);
        $this->assertSame('confirmed', AppointmentStatus::CONFIRMED->value);
        $this->assertSame('cancelled', AppointmentStatus::CANCELLED->value);
    }

    public function testAppointmentTypeValues(): void {
        $this->assertSame('online', AppointmentType::ONLINE->value);
        $this->assertSame('in_person', AppointmentType::IN_PERSON->value);
    }

    public function testArticleStatusValues(): void {
        $this->assertSame('draft', ArticleStatus::DRAFT->value);
        $this->assertSame('published', ArticleStatus::PUBLISHED->value);
    }

    public function testCommentStatusValues(): void {
        $this->assertSame('pending', CommentStatus::PENDING->value);
        $this->assertSame('approved', CommentStatus::APPROVED->value);
        $this->assertSame('rejected', CommentStatus::REJECTED->value);
    }

    public function testNotificationTypeValues(): void {
        $this->assertSame('info', NotificationType::INFO->value);
        $this->assertSame('success', NotificationType::SUCCESS->value);
        $this->assertSame('warning', NotificationType::WARNING->value);
        $this->assertSame('error', NotificationType::ERROR->value);
    }

    public function testPostStatusValues(): void {
        $this->assertSame('published', PostStatus::PUBLISHED->value);
        $this->assertSame('hidden', PostStatus::HIDDEN->value);
        $this->assertSame('reported', PostStatus::REPORTED->value);
    }

    public function testPregnancyStatusValues(): void {
        $this->assertSame('active', PregnancyStatus::ACTIVE->value);
        $this->assertSame('completed', PregnancyStatus::COMPLETED->value);
    }

    public function testResourceTypeValues(): void {
        $this->assertSame('pdf', ResourceType::PDF->value);
        $this->assertSame('ebook', ResourceType::EBOOK->value);
        $this->assertSame('video', ResourceType::VIDEO->value);
        $this->assertSame('guide', ResourceType::GUIDE->value);
    }

    public function testTestimonialStatusValues(): void {
        $this->assertSame('pending', TestimonialStatus::PENDING->value);
        $this->assertSame('approved', TestimonialStatus::APPROVED->value);
        $this->assertSame('rejected', TestimonialStatus::REJECTED->value);
    }

    public function testTicketPriorityValues(): void {
        $this->assertSame('low', TicketPriority::LOW->value);
        $this->assertSame('medium', TicketPriority::MEDIUM->value);
        $this->assertSame('high', TicketPriority::HIGH->value);
    }

    public function testTicketStatusValues(): void {
        $this->assertSame('open', TicketStatus::OPEN->value);
        $this->assertSame('in_progress', TicketStatus::IN_PROGRESS->value);
        $this->assertSame('closed', TicketStatus::CLOSED->value);
    }

    public function testVaccinationStatusValues(): void {
        $this->assertSame('pending', VaccinationStatus::PENDING->value);
        $this->assertSame('done', VaccinationStatus::DONE->value);
        $this->assertSame('missed', VaccinationStatus::MISSED->value);
    }

    public function testAllEnumsHaveCases(): void {
        $this->assertNotEmpty(AppointmentStatus::cases());
        $this->assertNotEmpty(AppointmentType::cases());
        $this->assertNotEmpty(ArticleStatus::cases());
        $this->assertNotEmpty(CommentStatus::cases());
        $this->assertNotEmpty(NotificationType::cases());
        $this->assertNotEmpty(PostStatus::cases());
        $this->assertNotEmpty(PregnancyStatus::cases());
        $this->assertNotEmpty(ResourceType::cases());
        $this->assertNotEmpty(TestimonialStatus::cases());
        $this->assertNotEmpty(TicketPriority::cases());
        $this->assertNotEmpty(TicketStatus::cases());
        $this->assertNotEmpty(UserStatus::cases());
        $this->assertNotEmpty(VaccinationStatus::cases());
    }
}
