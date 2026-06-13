<?php
namespace Tests\Unit;

use App\Repositories\AppointmentRepository;
use App\Repositories\BabyRepository;
use App\Repositories\MotherRepository;
use App\Repositories\PregnancyRepository;
use App\Services\AgendaService;
use PHPUnit\Framework\TestCase;

class AgendaServiceTest extends TestCase {
    private AppointmentRepository $appointmentRepo;
    private BabyRepository $babyRepo;
    private PregnancyRepository $pregnancyRepo;
    private MotherRepository $motherRepo;
    private AgendaService $service;

    protected function setUp(): void {
        $this->appointmentRepo = $this->createMock(AppointmentRepository::class);
        $this->babyRepo = $this->createMock(BabyRepository::class);
        $this->pregnancyRepo = $this->createMock(PregnancyRepository::class);
        $this->motherRepo = $this->createMock(MotherRepository::class);
        $this->service = new AgendaService(
            $this->appointmentRepo,
            $this->babyRepo,
            $this->pregnancyRepo,
            $this->motherRepo
        );
    }

    public function testGetMotherEventsReturnsAppointments(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findByMother')
            ->with(1)
            ->willReturn([
                ['id' => 1, 'appointment_date' => '2026-06-15 10:00', 'expert_name' => 'Dr Ben Ali', 'expert_specialty' => 'Gynécologue', 'type' => 'online', 'status' => 'pending'],
            ]);

        $this->babyRepo->expects($this->once())
            ->method('findByMother')
            ->with(1)
            ->willReturn(null);

        $this->pregnancyRepo->expects($this->once())
            ->method('findActiveByMother')
            ->with(1)
            ->willReturn(null);

        $events = $this->service->getMotherEvents(1);
        $this->assertCount(1, $events);
        $this->assertSame('Consultation avec Dr Ben Ali (Gynécologue)', $events[0]['title']);
        $this->assertSame('2026-06-15', $events[0]['date']);
        $this->assertSame('10:00', $events[0]['time']);
        $this->assertSame('consultation', $events[0]['type']);
    }

    public function testGetMotherEventsWithBabyData(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findByMother')
            ->with(1)
            ->willReturn([]);

        $this->babyRepo->expects($this->once())
            ->method('findByMother')
            ->with(1)
            ->willReturn(['id' => 5, 'date_of_birth' => '2026-01-10']);

        $this->babyRepo->expects($this->once())
            ->method('findVaccinations')
            ->with(5)
            ->willReturn([
                ['id' => 1, 'vaccine_name' => 'BCG', 'scheduled_date' => '2026-02-10', 'administered_date' => '2026-02-10', 'status' => 'done', 'notes' => ''],
                ['id' => 2, 'vaccine_name' => 'Hépatite B', 'scheduled_date' => '2026-03-10', 'administered_date' => null, 'status' => 'pending', 'notes' => ''],
            ]);

        $this->babyRepo->expects($this->once())
            ->method('findMilestones')
            ->with(5)
            ->willReturn([
                ['milestone_key' => 'first_smile', 'achieved_date' => '2026-03-01'],
            ]);

        $this->babyRepo->expects($this->once())
            ->method('findMemories')
            ->with(5)
            ->willReturn([
                ['title' => 'Premier bain', 'event_date' => '2026-01-15'],
            ]);

        $this->pregnancyRepo->expects($this->once())
            ->method('findActiveByMother')
            ->with(1)
            ->willReturn(null);

        $events = $this->service->getMotherEvents(1);
        // 2 vaccinations + 1 milestone + 1 memory + 1 birth = 5 events (no appointments)
        $this->assertCount(5, $events);

        $types = array_column($events, 'type');
        $this->assertContains('vaccin', $types);
        $this->assertContains('etape', $types);
        $this->assertContains('souvenir', $types);
        $this->assertContains('naissance', $types);
    }

    public function testGetMotherEventsWithPregnancyMilestones(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findByMother')
            ->with(1)
            ->willReturn([]);

        $this->babyRepo->expects($this->once())
            ->method('findByMother')
            ->with(1)
            ->willReturn(null);

        $this->pregnancyRepo->expects($this->once())
            ->method('findActiveByMother')
            ->with(1)
            ->willReturn(['id' => 10, 'due_date' => '2026-08-15', 'status' => 'active']);

        $this->pregnancyRepo->expects($this->once())
            ->method('getMilestones')
            ->with('2026-08-15')
            ->willReturn([
                ['title' => 'Échographie de datation', 'date' => '2026-02-01'],
                ['title' => 'Date prévue d\'accouchement', 'date' => '2026-08-15'],
            ]);

        $events = $this->service->getMotherEvents(1);
        $this->assertCount(2, $events);
        $this->assertSame('grossesse', $events[0]['type']);
    }

    public function testGetMotherEventsSkipsVaccinationWithoutDates(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findByMother')
            ->with(1)
            ->willReturn([]);

        $this->babyRepo->expects($this->once())
            ->method('findByMother')
            ->with(1)
            ->willReturn(['id' => 5, 'date_of_birth' => '2026-01-10']);

        $this->babyRepo->expects($this->once())
            ->method('findVaccinations')
            ->with(5)
            ->willReturn([
                ['id' => 1, 'vaccine_name' => 'BCG', 'scheduled_date' => null, 'administered_date' => null, 'status' => 'pending', 'notes' => ''],
            ]);

        $this->babyRepo->expects($this->once())
            ->method('findMilestones')
            ->with(5)
            ->willReturn([]);

        $this->babyRepo->expects($this->once())
            ->method('findMemories')
            ->with(5)
            ->willReturn([]);

        $this->pregnancyRepo->expects($this->once())
            ->method('findActiveByMother')
            ->with(1)
            ->willReturn(null);

        $events = $this->service->getMotherEvents(1);
        // Only birth event, the vaccination has no dates so it's skipped
        $this->assertCount(1, $events);
        $this->assertSame('naissance', $events[0]['type']);
    }

    public function testGetMotherEventsFilterUpcoming(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findByMother')
            ->with(1)
            ->willReturn([
                ['id' => 1, 'appointment_date' => '2025-01-01 10:00', 'expert_name' => 'Dr Test', 'expert_specialty' => '', 'type' => 'online', 'status' => 'confirmed'],
                ['id' => 2, 'appointment_date' => '2030-01-01 10:00', 'expert_name' => 'Dr Future', 'expert_specialty' => '', 'type' => 'in_person', 'status' => 'pending'],
            ]);

        $this->babyRepo->expects($this->once())
            ->method('findByMother')
            ->with(1)
            ->willReturn(null);

        $this->pregnancyRepo->expects($this->once())
            ->method('findActiveByMother')
            ->with(1)
            ->willReturn(null);

        $events = $this->service->getMotherEvents(1, 'upcoming');
        $this->assertCount(1, $events);
        $this->assertStringContainsString('Dr Future', $events[0]['title']);
    }

    public function testGetMotherEventsFilterPast(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findByMother')
            ->with(1)
            ->willReturn([
                ['id' => 1, 'appointment_date' => '2025-01-01 10:00', 'expert_name' => 'Dr Past', 'expert_specialty' => '', 'type' => 'online', 'status' => 'confirmed'],
                ['id' => 2, 'appointment_date' => '2030-01-01 10:00', 'expert_name' => 'Dr Future', 'expert_specialty' => '', 'type' => 'in_person', 'status' => 'pending'],
            ]);

        $this->babyRepo->expects($this->once())
            ->method('findByMother')
            ->with(1)
            ->willReturn(null);

        $this->pregnancyRepo->expects($this->once())
            ->method('findActiveByMother')
            ->with(1)
            ->willReturn(null);

        $events = $this->service->getMotherEvents(1, 'past');
        $this->assertCount(1, $events);
        $this->assertStringContainsString('Dr Past', $events[0]['title']);
    }

    public function testGetExpertAppointmentsSplitsUpcomingAndPast(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findByExpert')
            ->with(2)
            ->willReturn([
                ['id' => 1, 'appointment_date' => '2025-01-01 10:00', 'mother_name' => 'Fatima', 'status' => 'confirmed', 'type' => 'online', 'notes' => ''],
                ['id' => 2, 'appointment_date' => '2030-06-15 14:30', 'mother_name' => 'Amina', 'status' => 'pending', 'type' => 'in_person', 'notes' => 'Test'],
            ]);

        $result = $this->service->getExpertAppointments(2);
        $this->assertArrayHasKey('upcoming', $result);
        $this->assertArrayHasKey('past', $result);
        $this->assertCount(1, $result['upcoming']);
        $this->assertCount(1, $result['past']);
        $this->assertStringContainsString('Amina', $result['upcoming'][0]['title']);
        $this->assertStringContainsString('Fatima', $result['past'][0]['title']);
    }

    public function testGetExpertAppointmentsReturnsEmptyArrays(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findByExpert')
            ->with(99)
            ->willReturn([]);

        $result = $this->service->getExpertAppointments(99);
        $this->assertSame(['upcoming' => [], 'past' => []], $result);
    }

    public function testGetMotherEventsSortsByDate(): void {
        $this->appointmentRepo->expects($this->once())
            ->method('findByMother')
            ->with(1)
            ->willReturn([
                ['id' => 1, 'appointment_date' => '2026-06-20 10:00', 'expert_name' => 'Dr C', 'expert_specialty' => '', 'type' => 'online', 'status' => 'pending'],
                ['id' => 2, 'appointment_date' => '2026-06-10 10:00', 'expert_name' => 'Dr A', 'expert_specialty' => '', 'type' => 'online', 'status' => 'pending'],
                ['id' => 3, 'appointment_date' => '2026-06-15 10:00', 'expert_name' => 'Dr B', 'expert_specialty' => '', 'type' => 'online', 'status' => 'pending'],
            ]);

        $this->babyRepo->expects($this->once())
            ->method('findByMother')
            ->with(1)
            ->willReturn(null);

        $this->pregnancyRepo->expects($this->once())
            ->method('findActiveByMother')
            ->with(1)
            ->willReturn(null);

        $events = $this->service->getMotherEvents(1);
        $this->assertCount(3, $events);
        $dates = array_column($events, 'date');
        $this->assertSame(['2026-06-10', '2026-06-15', '2026-06-20'], $dates);
    }
}
