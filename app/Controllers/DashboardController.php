<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Services\AppointmentService;
use App\Services\AgendaService;
use App\Services\CommunityService;
use App\Services\NotificationService;
use App\Services\FileUploadService;
use App\Repositories\UserRepository;
use App\Repositories\MotherRepository;
use App\Repositories\BabyRepository;
use App\Repositories\PregnancyRepository;
use App\Repositories\ArticleRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\TestimonialRepository;
use App\Repositories\TicketRepository;
use App\Repositories\AppointmentRepository;

class DashboardController extends Controller {
    private UserRepository $userRepo;
    private MotherRepository $motherRepo;
    private BabyRepository $babyRepo;
    private PregnancyRepository $pregnancyRepo;
    private AppointmentRepository $appointmentRepo;
    private AppointmentService $appointmentService;
    private AgendaService $agendaService;
    private CommunityService $communityService;
    private NotificationService $notifService;
    private NotificationRepository $notifRepo;
    private FileUploadService $uploadService;
    private ArticleRepository $articleRepo;
    private TestimonialRepository $testimonialRepo;
    private TicketRepository $ticketRepo;

    public function __construct() {
        $this->layout = 'maman';
        $this->authCheck();
        if (Session::get('user_role_slug') !== 'maman') {
            header('Location: /auth/login');
            exit;
        }
        $this->userRepo = new UserRepository();
        $this->motherRepo = new MotherRepository();
        $this->babyRepo = new BabyRepository();
        $this->pregnancyRepo = new PregnancyRepository();
        $this->appointmentRepo = new AppointmentRepository();
        $this->appointmentService = new AppointmentService();
        $this->agendaService = new AgendaService();
        $this->communityService = new CommunityService();
        $this->notifService = new NotificationService();
        $this->notifRepo = new NotificationRepository();
        $this->uploadService = new FileUploadService();
        $this->articleRepo = new ArticleRepository();
        $this->testimonialRepo = new TestimonialRepository();
        $this->ticketRepo = new TicketRepository();
    }

    private function getMotherId(): int {
        return $this->motherRepo->findOrCreate(Session::get('user_id'));
    }

    public function index() {
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId();
        $user = $this->userRepo->findById($userId);
        $nameParts = explode(' ', $user['name'] ?? '', 2);
        $user['first_name'] = $nameParts[0] ?? '';

        $pregnancy = $this->pregnancyRepo->findActiveByMother($motherId);
        if ($pregnancy) {
            $pregnancy['days_remaining'] = $this->pregnancyRepo->getDaysRemaining($pregnancy['due_date']);
            $pregnancy['weeks'] = $this->pregnancyRepo->getGestationalWeeks($pregnancy['due_date']);
        }

        $baby = $this->babyRepo->findByMother($motherId);
        if ($baby) {
            $birthDate = new \DateTime($baby['date_of_birth']);
            $now = new \DateTime();
            $diff = $birthDate->diff($now);
            $baby['age_months'] = ($diff->y * 12) + $diff->m;
            $baby['last_weight'] = $baby['last_weight'] ?: 0.0;
            $baby['last_height'] = $baby['last_height'] ?: 0.0;
        }

        $articles = $this->articleRepo->findPublished(5)['items'] ?? [];
        $posts = array_slice($this->communityService->getPublishedPosts(), 0, 5);
        $notifCount = $this->notifRepo->countUnread($userId);

        $this->render('dashboard/index', compact('user', 'pregnancy', 'baby', 'articles', 'posts', 'notifCount'));
    }

    public function profil() {
        $userId = Session::get('user_id');
        $motherId = $this->getMotherId();

        if (Request::isPost()) {
            $firstName = trim(Request::post('first_name'));
            $lastName = trim(Request::post('last_name'));
            $email = trim(Request::post('email'));
            $phone = trim(Request::post('phone'));
            $dob = Request::post('date_of_birth') ?: null;
            $fullName = trim($firstName . ' ' . $lastName);

            $this->userRepo->update($userId, ['name' => $fullName, 'email' => $email, 'phone' => $phone]);
            $avatar = $this->uploadService->uploadAvatar($_FILES['avatar'] ?? []);
            if ($avatar) {
                $this->userRepo->update($userId, ['avatar' => $avatar]);
                Session::set('user_avatar', '/uploads/avatars/' . $avatar);
            }
            $this->userRepo->execute("UPDATE mothers SET date_of_birth = ? WHERE user_id = ?", [$dob, $userId]);
            Session::set('user_name', $fullName);
            Session::set('user_email', $email);
            Session::setFlash('success', 'Profil mis à jour avec succès.');
            Request::back();
        }

        $user = $this->userRepo->rawOne(
            "SELECT u.*, m.date_of_birth FROM users u LEFT JOIN mothers m ON u.id = m.user_id WHERE u.id = ?", [$userId]
        );
        $nameParts = explode(' ', $user['name'] ?? '', 2);
        $user['first_name'] = $nameParts[0] ?? '';
        $user['last_name'] = $nameParts[1] ?? '';
        $this->render('dashboard/profil', compact('user'));
    }

    public function updateProfil() { return $this->profil(); }

    public function grossesse() {
        $motherId = $this->getMotherId();
        if (Request::isPost()) {
            $dueDate = Request::post('due_date');
            $notes = Request::post('notes');
            $this->pregnancyRepo->upsert($motherId, $dueDate, $notes);
            Session::setFlash('success', 'Informations de grossesse mises à jour.');
            Request::back();
        }
        $pregnancy = $this->pregnancyRepo->findActiveByMother($motherId);
        if ($pregnancy) {
            $pregnancy['days_remaining'] = $this->pregnancyRepo->getDaysRemaining($pregnancy['due_date']);
            $pregnancy['weeks'] = $this->pregnancyRepo->getGestationalWeeks($pregnancy['due_date']);
        }
        $this->render('dashboard/grossesse', compact('pregnancy'));
    }

    public function updateGrossesse() { return $this->grossesse(); }

    public function bebe() {
        $motherId = $this->getMotherId();
        if (Request::isPost()) {
            $name = Request::post('name');
            $birthDate = Request::post('birth_date');
            $gender = Request::post('gender');
            $weight = Request::post('weight');
            $height = Request::post('height');
            $bloodType = Request::post('blood_type') ?: null;
            $notes = Request::post('notes') ?: null;

            $existing = $this->babyRepo->rawOne("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);
            if ($existing) {
                $babyId = $existing['id'];
                $this->babyRepo->update($babyId, [
                    'name' => $name, 'date_of_birth' => $birthDate, 'gender' => $gender,
                    'blood_type' => $bloodType, 'notes' => $notes,
                ]);
            } else {
                $babyId = $this->babyRepo->create([
                    'mother_id' => $motherId, 'name' => $name, 'date_of_birth' => $birthDate,
                    'gender' => $gender, 'blood_type' => $bloodType, 'notes' => $notes,
                ]);
            }
            if ($weight || $height) {
                $this->babyRepo->addGrowthRecord($babyId, date('Y-m-d'), $weight ?: null, $height ?: null, null);
            }
            Session::setFlash('success', 'Informations du bébé mises à jour.');
            Request::back();
        }

        $baby = $this->babyRepo->findByMother($motherId);
        $memories = $baby ? $this->babyRepo->findMemories($baby['id']) : [];
        $milestones = $baby ? $this->babyRepo->findMilestonesAsArray($baby['id']) : [];
        $this->render('dashboard/bebe', compact('baby', 'memories', 'milestones'));
    }

    public function updateBebe() { return $this->bebe(); }

    public function memories() {
        $motherId = $this->getMotherId();
        $baby = $this->babyRepo->rawOne("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);
        if (!$baby) {
            Session::setFlash('error', 'Veuillez ajouter un bébé d\'abord.');
            Request::back();
        }
        $title = trim(Request::post('title'));
        $content = trim(Request::post('content'));
        $date = Request::post('event_date') ?: date('Y-m-d');
        $this->babyRepo->addMemory($baby['id'], $title, $content, $date);
        Session::setFlash('success', 'Souvenir ajouté avec succès.');
        Request::back();
    }

    public function updateMemory($id) {
        if (!Request::isPost()) { Request::back(); }
        $motherId = $this->getMotherId();
        $baby = $this->babyRepo->rawOne("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);
        if (!$baby) { Request::back(); }
        $this->babyRepo->updateMemory(
            $id, $baby['id'],
            trim(Request::post('title')),
            trim(Request::post('content')),
            Request::post('event_date') ?: date('Y-m-d')
        );
        Session::setFlash('success', 'Souvenir mis à jour.');
        Request::back();
    }

    public function deleteMemory($id) {
        if (!Request::isPost()) { Request::back(); }
        $motherId = $this->getMotherId();
        $baby = $this->babyRepo->rawOne("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);
        if ($baby) {
            $this->babyRepo->deleteMemory($id, $baby['id']);
            Session::setFlash('success', 'Souvenir supprimé.');
        }
        Request::back();
    }

    public function updateMilestones() {
        $motherId = $this->getMotherId();
        $baby = $this->babyRepo->rawOne("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);
        if (!$baby) {
            Session::setFlash('error', 'Veuillez ajouter un bébé d\'abord.');
            Request::back();
        }
        try {
            $this->babyRepo->updateMilestones($baby['id'], Request::post('milestones') ?: []);
            Session::setFlash('success', 'Étapes de développement mises à jour.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Erreur lors de la mise à jour des étapes.');
        }
        Request::back();
    }

    public function croissance() {
        $motherId = $this->getMotherId();
        $baby = $this->babyRepo->rawOne("SELECT * FROM babies WHERE mother_id = ?", [$motherId]);
        $records = [];
        if ($baby) {
            $records = $this->babyRepo->findGrowthRecords($baby['id'], $baby['date_of_birth']);
        }
        $this->render('dashboard/croissance', compact('baby', 'records'));
    }

    public function addCroissance() {
        $motherId = $this->getMotherId();
        $baby = $this->babyRepo->rawOne("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);
        if (!$baby) {
            Session::setFlash('error', 'Veuillez ajouter un bébé d\'abord.');
            Request::back();
        }
        $this->babyRepo->addGrowthRecord(
            $baby['id'],
            Request::post('measured_at') ?: date('Y-m-d'),
            Request::post('weight'),
            Request::post('height'),
            Request::post('head_circumference') ?: null
        );
        Session::setFlash('success', 'Mesure de croissance ajoutée.');
        Request::back();
    }

    public function deleteCroissance($id) {
        if (!Request::isPost()) { Request::back(); }
        $motherId = $this->getMotherId();
        $baby = $this->babyRepo->rawOne("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);
        if ($baby) {
            $this->babyRepo->deleteGrowthRecord($id, $baby['id']);
            Session::setFlash('success', 'Mesure supprimée.');
        }
        Request::back();
    }

    public function vaccination() {
        $motherId = $this->getMotherId();
        $baby = $this->babyRepo->rawOne("SELECT * FROM babies WHERE mother_id = ?", [$motherId]);
        $vaccinations = [];
        if ($baby) {
            $vaccinations = $this->babyRepo->findVaccinations($baby['id']);
        }
        $this->render('dashboard/vaccination', compact('baby', 'vaccinations'));
    }

    public function addVaccination() {
        $motherId = $this->getMotherId();
        $baby = $this->babyRepo->rawOne("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);
        if (!$baby) {
            Session::setFlash('error', 'Veuillez ajouter un bébé d\'abord.');
            Request::back();
        }
        $administeredDate = Request::post('administered_date') ?: null;
        $status = $administeredDate ? 'done' : 'pending';
        $this->babyRepo->addVaccination(
            $baby['id'],
            Request::post('vaccine_name'),
            Request::post('scheduled_date') ?: null,
            $administeredDate,
            $status,
            Request::post('notes') ?: null
        );
        Session::setFlash('success', 'Vaccin enregistré.');
        Request::back();
    }

    public function deleteVaccination($id) {
        if (!Request::isPost()) { Request::back(); }
        $motherId = $this->getMotherId();
        $baby = $this->babyRepo->rawOne("SELECT id FROM babies WHERE mother_id = ?", [$motherId]);
        if ($baby) {
            $this->babyRepo->deleteVaccination($id, $baby['id']);
            Session::setFlash('success', 'Vaccin supprimé.');
        }
        Request::back();
    }

    public function completePregnancy() {
        if (!Request::isPost()) { Request::back(); }
        $this->pregnancyRepo->complete($this->getMotherId());
        Session::setFlash('success', 'Grossesse marquée comme terminée.');
        Request::back();
    }

    public function tickets() {
        $userId = Session::get('user_id');
        $tickets = $this->ticketRepo->findByUser($userId);
        foreach ($tickets as &$t) {
            $t['responses'] = $this->ticketRepo->findMessages($t['id']);
        }
        $this->render('dashboard/tickets', compact('tickets'));
    }

    public function createTicket() {
        $validator = new Validator(Request::all());
        $validator->required('subject', 'Sujet')->required('message', 'Message');
        if (!$validator->passes()) {
            Session::setFlash('error', $validator->firstError());
            Request::back();
        }
        $this->ticketRepo->create(
            Session::get('user_id'),
            trim(Request::post('subject')),
            trim(Request::post('message')),
            Request::post('priority') ?: 'medium'
        );
        Session::setFlash('success', 'Support ticket créé.');
        Request::redirect('/dashboard/tickets');
    }

    public function appointments() {
        $motherId = $this->getMotherId();
        $appointments = $this->appointmentRepo->findByMother($motherId);
        $experts = $this->appointmentRepo->getActiveExperts();
        $this->render('dashboard/appointments', compact('appointments', 'experts'));
    }

    public function bookAppointment() {
        $motherId = $this->getMotherId();
        $date = Request::post('appointment_date');
        $time = Request::post('appointment_time');
        $dateTime = $date && $time ? $date . ' ' . $time . ':00' : Request::post('appointment_date');
        $this->appointmentService->book(
            $motherId,
            Request::post('expert_id'),
            $dateTime,
            Request::post('type') ?: 'online',
            Request::post('notes') ?: null
        );
        Session::setFlash('success', 'Demande de rendez-vous enregistrée.');
        Request::redirect('/dashboard/rendez-vous');
    }

    public function cancelAppointment($id) {
        if (!Request::isPost()) { Request::back(); }
        if ($this->appointmentService->cancelByMother($id, $this->getMotherId())) {
            Session::setFlash('success', 'Rendez-vous annulé.');
        } else {
            Session::setFlash('error', 'Rendez-vous introuvable.');
        }
        Request::back();
    }

    public function agenda() {
        $motherId = $this->getMotherId();
        $filter = Request::get('filter', 'all');
        $events = $this->agendaService->getMotherEvents($motherId, $filter);
        $this->render('dashboard/agenda', compact('events', 'filter'));
    }

    public function messages() {
        $userId = Session::get('user_id');
        $activePartnerId = Request::get('partner_id');
        $roleExpert = $this->userRepo->rawOne("SELECT id FROM roles WHERE slug = 'expert'");
        $experts = [];
        if ($roleExpert) {
            $experts = $this->userRepo->findByRoleId($roleExpert['id']);
        }
        $chatHistory = [];
        $activePartner = null;
        if ($activePartnerId) {
            $activePartner = $this->userRepo->findById($activePartnerId);
            if ($activePartner) {
                $this->userRepo->execute(
                    "UPDATE expert_messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?",
                    [$activePartnerId, $userId]
                );
                $chatHistory = $this->userRepo->raw(
                    "SELECT em.*, u.name as sender_name FROM expert_messages em JOIN users u ON em.sender_id = u.id WHERE (em.sender_id = ? AND em.receiver_id = ?) OR (em.sender_id = ? AND em.receiver_id = ?) ORDER BY em.created_at ASC",
                    [$userId, $activePartnerId, $activePartnerId, $userId]
                );
            }
        }
        $this->render('dashboard/messages', compact('experts', 'chatHistory', 'activePartner'));
    }

    public function sendMessage() {
        $userId = Session::get('user_id');
        $receiverId = Request::post('receiver_id');
        $message = trim(Request::post('message'));
        if ($receiverId && $message !== '') {
            $this->userRepo->execute(
                "INSERT INTO expert_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)",
                [$userId, $receiverId, $message]
            );
            $this->notifService->sendNewMessage($receiverId, $userId, Session::get('user_role_slug'));
        }
        Request::redirect('/dashboard/messagerie?partner_id=' . $receiverId);
    }

    public function notifications() {
        $userId = Session::get('user_id');
        $this->notifRepo->markAllRead($userId);
        $notifications = $this->notifRepo->findByUser($userId);
        $this->render('dashboard/notifications', compact('notifications'));
    }

    public function readAllNotifications() {
        $this->notifRepo->markAllRead(Session::get('user_id'));
        Session::setFlash('success', 'Toutes les notifications ont été marquées comme lues.');
        Request::redirect('/dashboard/notifications');
    }

    public function readNotification($id) {
        $this->notifRepo->markRead($id, Session::get('user_id'));
        Request::redirect('/dashboard/notifications');
    }

    public function parametres() {
        $this->render('dashboard/parametres');
    }

    public function updateParametres() {
        $userId = Session::get('user_id');
        $oldPassword = Request::post('old_password');
        $newPassword = Request::post('new_password');
        $confirm = Request::post('new_password_confirm');
        if (!$this->userRepo->verifyPassword($userId, $oldPassword)) {
            Session::setFlash('error', 'Ancien mot de passe incorrect.');
            Request::back();
        }
        if (strlen($newPassword) < 6) {
            Session::setFlash('error', 'Le nouveau mot de passe doit faire au moins 6 caractères.');
            Request::back();
        }
        if ($newPassword !== $confirm) {
            Session::setFlash('error', 'Les nouveaux mots de passe ne correspondent pas.');
            Request::back();
        }
        $this->userRepo->updatePassword($userId, $newPassword);
        Session::setFlash('success', 'Mot de passe modifié avec succès.');
        Request::back();
    }

    public function testimonials() {
        $testimonials = $this->testimonialRepo->findByUser(Session::get('user_id'));
        $this->render('dashboard/testimonials', compact('testimonials'));
    }

    public function submitTestimonial() {
        if (!Request::isPost()) { Request::back(); }
        $validator = new Validator(Request::all());
        $validator->required('content', 'Le témoignage');
        if (!$validator->passes()) {
            Session::setFlash('error', $validator->firstError());
            Request::back();
        }
        $rating = min(5, max(1, (int)Request::post('rating', 5)));
        $this->testimonialRepo->create([
            'user_id' => Session::get('user_id'),
            'content' => trim(Request::post('content')),
            'rating' => $rating,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        Session::setFlash('success', 'Témoignage soumis pour modération.');
        Request::back();
    }
}
