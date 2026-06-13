<?php
namespace App\Services;

use App\Repositories\AppointmentRepository;
use App\Repositories\BabyRepository;
use App\Repositories\PregnancyRepository;
use App\Repositories\MotherRepository;

class AgendaService {
    private AppointmentRepository $appointmentRepo;
    private BabyRepository $babyRepo;
    private PregnancyRepository $pregnancyRepo;
    private MotherRepository $motherRepo;

    private array $milestoneLabels = [
        'first_smile' => 'Premier sourire',
        'first_word' => 'Premier mot',
        'first_step' => 'Premier pas',
        'first_bath' => 'Premier bain',
        'first_solid' => 'Première diversification',
        'first_tooth' => 'Première dent',
        'crawling' => 'Quatre pattes',
        'sitting' => 'Assis seul',
        'standing' => 'Debout seul',
        'walking' => 'Marche seul',
    ];

    public function __construct(
        ?AppointmentRepository $appointmentRepo = null,
        ?BabyRepository $babyRepo = null,
        ?PregnancyRepository $pregnancyRepo = null,
        ?MotherRepository $motherRepo = null
    ) {
        $this->appointmentRepo = $appointmentRepo ?? new AppointmentRepository();
        $this->babyRepo = $babyRepo ?? new BabyRepository();
        $this->pregnancyRepo = $pregnancyRepo ?? new PregnancyRepository();
        $this->motherRepo = $motherRepo ?? new MotherRepository();
    }

    public function getMotherEvents(int $motherId, string $filter = 'all'): array {
        $events = [];

        // 1. Appointments
        $appointments = $this->appointmentRepo->findByMother($motherId);
        foreach ($appointments as $a) {
            $events[] = [
                'title' => 'Consultation avec ' . ($a['expert_name'] ?? 'Expert') . ' (' . ($a['expert_specialty'] ?: 'Généraliste') . ')',
                'date' => $a['appointment_date'] ? date('Y-m-d', strtotime($a['appointment_date'])) : null,
                'time' => $a['appointment_date'] ? date('H:i', strtotime($a['appointment_date'])) : null,
                'type' => 'consultation',
                'badge' => $a['type'] === 'online' ? 'Téléconsultation' : 'Cabinet',
                'badge_class' => 'info',
                'icon' => 'bi-calendar-event',
                'status' => $a['status'],
            ];
        }

        // 2. Baby data
        $baby = $this->babyRepo->findByMother($motherId);
        if ($baby) {
            // Vaccinations
            $vaccinations = $this->babyRepo->findVaccinations($baby['id']);
            foreach ($vaccinations as $v) {
                if (!$v['scheduled_date'] && !$v['administered_date']) continue;
                $isDone = $v['administered_date'] || $v['status'] === 'done';
                $events[] = [
                    'title' => 'Vaccin : ' . $v['vaccine_name'],
                    'date' => $isDone ? $v['administered_date'] : $v['scheduled_date'],
                    'time' => null,
                    'type' => 'vaccin',
                    'badge' => $isDone ? 'Fait' : 'À faire',
                    'badge_class' => $isDone ? 'success' : 'warning',
                    'icon' => 'bi-shield-check',
                ];
            }

            // Milestones
            $dbMilestones = $this->babyRepo->findMilestones($baby['id']);
            foreach ($dbMilestones as $bm) {
                $events[] = [
                    'title' => $this->milestoneLabels[$bm['milestone_key']] ?? $bm['milestone_key'],
                    'date' => $bm['achieved_date'],
                    'time' => null,
                    'type' => 'etape',
                    'badge' => 'Étape clé',
                    'badge_class' => 'success',
                    'icon' => 'bi-star',
                ];
            }

            // Memories
            $memories = $this->babyRepo->findMemories($baby['id']);
            foreach ($memories as $m) {
                $events[] = [
                    'title' => $m['title'],
                    'date' => $m['event_date'],
                    'time' => null,
                    'type' => 'souvenir',
                    'badge' => 'Souvenir',
                    'badge_class' => 'info',
                    'icon' => 'bi-camera',
                ];
            }

            // Birth date
            if ($baby['date_of_birth']) {
                $events[] = [
                    'title' => 'Naissance de bébé',
                    'date' => $baby['date_of_birth'],
                    'time' => null,
                    'type' => 'naissance',
                    'badge' => 'Naissance',
                    'badge_class' => 'success',
                    'icon' => 'bi-gift',
                ];
            }
        }

        // 3. Pregnancy milestones
        $pregnancy = $this->pregnancyRepo->findActiveByMother($motherId);
        if ($pregnancy && $pregnancy['due_date']) {
            $pregMilestones = $this->pregnancyRepo->getMilestones($pregnancy['due_date']);
            foreach ($pregMilestones as $pm) {
                $events[] = [
                    'title' => $pm['title'],
                    'date' => $pm['date'],
                    'time' => null,
                    'type' => 'grossesse',
                    'badge' => 'Suivi grossesse',
                    'badge_class' => 'info',
                    'icon' => 'bi-flower1',
                ];
            }
        }

        // Filter and sort
        $events = array_filter($events, fn($e) => $e['date'] !== null);
        usort($events, fn($a, $b) => ($a['date'] ?? '') <=> ($b['date'] ?? ''));

        $today = new \DateTime('today');
        if ($filter === 'upcoming') {
            $events = array_values(array_filter($events, fn($e) => new \DateTime($e['date']) >= $today));
        } elseif ($filter === 'past') {
            $events = array_values(array_filter($events, fn($e) => new \DateTime($e['date']) < $today));
        }

        return $events;
    }

    public function getExpertAppointments(int $expertId): array {
        $today = new \DateTime('today');
        $appointments = $this->appointmentRepo->findByExpert($expertId);
        $upcoming = [];
        $past = [];
        foreach ($appointments as $a) {
            $evt = [
                'id' => $a['id'],
                'title' => 'Consultation avec ' . ($a['mother_name'] ?? 'Maman'),
                'date' => date('Y-m-d', strtotime($a['appointment_date'])),
                'time' => date('H:i', strtotime($a['appointment_date'])),
                'status' => $a['status'],
                'type' => $a['type'],
                'notes' => $a['notes'] ?? '',
            ];
            if (new \DateTime($evt['date']) < $today) {
                $past[] = $evt;
            } else {
                $upcoming[] = $evt;
            }
        }
        return ['upcoming' => $upcoming, 'past' => $past];
    }
}
