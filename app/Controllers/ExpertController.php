<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Services\AgendaService;
use App\Services\AppointmentService;
use App\Services\CommunityService;
use App\Services\NotificationService;
use App\Services\FileUploadService;
use App\Repositories\UserRepository;
use App\Repositories\ArticleRepository;
use App\Repositories\ResourceRepository;
use App\Repositories\CommunityPostRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\AvailabilityRepository;

class ExpertController extends Controller {
    private AgendaService $agendaService;
    private AppointmentService $appointmentService;
    private CommunityService $communityService;
    private NotificationService $notifService;
    private FileUploadService $uploadService;
    private UserRepository $userRepo;
    private ArticleRepository $articleRepo;
    private ResourceRepository $resourceRepo;
    private CommunityPostRepository $postRepo;
    private NotificationRepository $notifRepo;
    private AppointmentRepository $appointmentRepo;
    private AvailabilityRepository $availabilityRepo;

    public function __construct() {
        if (strpos($_SERVER['REQUEST_URI'], '/expert/') === 0) {
            $this->layout = 'expert';
            $this->authCheck();
            if (Session::get('user_role_slug') !== 'expert') {
                header('Location: /auth/login');
                exit;
            }
        } else {
            $this->layout = 'front';
        }
        $this->agendaService = new AgendaService();
        $this->appointmentService = new AppointmentService();
        $this->communityService = new CommunityService();
        $this->notifService = new NotificationService();
        $this->uploadService = new FileUploadService();
        $this->userRepo = new UserRepository();
        $this->articleRepo = new ArticleRepository();
        $this->resourceRepo = new ResourceRepository();
        $this->postRepo = new CommunityPostRepository();
        $this->notifRepo = new NotificationRepository();
        $this->appointmentRepo = new AppointmentRepository();
        $this->availabilityRepo = new AvailabilityRepository();
    }

    public function index() {
        $userId = Session::get('user_id');
        $assignedTickets = $this->userRepo->raw("SELECT COUNT(*) as count FROM tickets WHERE assigned_to = ? AND status NOT IN ('closed', 'resolved')", [$userId])[0]['count'] ?? 0;
        $pendingQuestions = $this->postRepo->count(['status' => 'published']);
        $articlesCount = $this->articleRepo->count(['user_id' => $userId, 'status' => 'published']);
        $appointmentsCount = $this->appointmentRepo->count(['expert_id' => $userId, 'status' => 'pending']);

        $upcomingAppointments = $this->appointmentRepo->raw(
            "SELECT a.*, m.user_id as mother_user_id,
                    (SELECT name FROM users WHERE id = m.user_id) as mother_name
             FROM appointments a
             JOIN mothers m ON a.mother_id = m.id
             WHERE a.expert_id = ? AND a.status IN ('pending', 'confirmed')
             AND a.appointment_date >= NOW()
             ORDER BY a.appointment_date ASC LIMIT 5",
            [$userId]
        );

        $totalViews = $this->articleRepo->raw(
            "SELECT COALESCE(SUM(views_count), 0) as total FROM articles WHERE user_id = ?",
            [$userId]
        )[0]['total'] ?? 0;

        $monthlyAppointments = $this->appointmentRepo->raw(
            "SELECT DATE_FORMAT(appointment_date, '%Y-%m') as month, COUNT(*) as count
             FROM appointments WHERE expert_id = ? AND appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY month ORDER BY month ASC",
            [$userId]
        );

        $firstName = explode(' ', Session::get('user_name') ?? '', 2)[0] ?? '';

        $this->render('expert/index', compact(
            'assignedTickets', 'pendingQuestions', 'articlesCount',
            'appointmentsCount', 'upcomingAppointments', 'totalViews',
            'monthlyAppointments', 'firstName'
        ));
    }

    public function profil() {
        $userId = Session::get('user_id');
        if (Request::isPost()) {
            $data = [
                'name' => trim(Request::post('name')),
                'specialty' => trim(Request::post('specialty')),
                'bio' => trim(Request::post('bio')),
                'address' => trim(Request::post('address')),
                'phone' => trim(Request::post('phone')),
            ];
            $this->userRepo->update($userId, $data);
            $avatar = $this->uploadService->uploadAvatar($_FILES['avatar'] ?? []);
            if ($avatar) {
                $this->userRepo->update($userId, ['avatar' => $avatar]);
                Session::set('user_avatar', '/uploads/avatars/' . $avatar);
            }
            Session::set('user_name', $data['name']);
            Session::setFlash('success', 'Profil professionnel mis à jour.');
            Request::back();
        }
        $user = $this->userRepo->findById($userId);
        $this->render('expert/profil', compact('user'));
    }

    public function updateProfil() { return $this->profil(); }

    public function questions() {
        $questions = $this->postRepo->findPublished();
        $this->render('expert/questions', compact('questions'));
    }

    public function answerQuestion($id) {
        if (!Request::isPost()) { Request::back(); }
        $validator = new Validator(Request::all());
        $validator->required('content', 'La réponse');
        if (!$validator->passes()) {
            Session::setFlash('error', $validator->firstError());
            Request::back();
        }
        $this->communityService->addComment($id, Session::get('user_id'), trim(Request::post('content')));
        Session::setFlash('success', 'Réponse publiée.');
        Request::back();
    }

    public function articles() {
        $articles = $this->articleRepo->findByUser(Session::get('user_id'));
        $categories = $this->userRepo->raw("SELECT * FROM categories ORDER BY name ASC");
        $this->render('expert/articles', compact('articles', 'categories'));
    }

    public function createArticle() {
        if (!Request::isPost()) { Request::back(); }
        $title = trim(Request::post('title'));
        $content = Request::post('content');
        $categoryId = Request::post('category_id');
        $status = in_array(Request::post('status'), ['draft', 'published']) ? Request::post('status') : 'draft';
        $slug = $this->articleRepo->generateSlug($title);
        $this->articleRepo->create([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'category_id' => $categoryId,
            'user_id' => Session::get('user_id'),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        Session::setFlash('success', 'Article créé avec succès.');
        Request::back();
    }

    public function editArticle($id) {
        $article = $this->articleRepo->findByUserWithId($id, Session::get('user_id'));
        if (!$article) {
            Session::setFlash('error', 'Article introuvable.');
            Request::redirect('/expert/articles');
        }
        $categories = $this->userRepo->raw("SELECT * FROM categories ORDER BY name ASC");
        $this->render('expert/edit_article', compact('article', 'categories'));
    }

    public function updateArticle($id) {
        if (!Request::isPost()) { Request::back(); }
        $article = $this->articleRepo->findByUserWithId($id, Session::get('user_id'));
        if (!$article) {
            Session::setFlash('error', 'Article introuvable.');
            Request::back();
        }
        $title = trim(Request::post('title'));
        $status = in_array(Request::post('status'), ['draft', 'published']) ? Request::post('status') : 'draft';
        $slug = $this->articleRepo->generateSlug($title, $id);
        $this->articleRepo->update($id, [
            'title' => $title,
            'slug' => $slug,
            'content' => Request::post('content'),
            'category_id' => Request::post('category_id'),
            'status' => $status,
        ]);
        Session::setFlash('success', 'Article mis à jour.');
        Request::back();
    }

    public function deleteArticle($id) {
        if (!Request::isPost()) { Request::back(); }
        $this->articleRepo->deleteByUserAndId($id, Session::get('user_id'));
        // Actually just check user_id in WHERE
        $this->userRepo->execute("DELETE FROM articles WHERE id = ? AND user_id = ?", [$id, Session::get('user_id')]);
        Session::setFlash('success', 'Article supprimé.');
        Request::back();
    }

    public function ressources() {
        $resources = $this->resourceRepo->findByUser(Session::get('user_id'));
        $this->render('expert/ressources', compact('resources'));
    }

    public function createResource() {
        if (!Request::isPost()) { Request::back(); }
        $title = trim(Request::post('title'));
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = $slug ?: 'resource-' . time();
        $fileUrl = $this->uploadService->upload($_FILES['file_url'] ?? [], 'ressources', $slug) ?: '';
        $this->resourceRepo->create([
            'title' => $title,
            'slug' => $slug,
            'description' => Request::post('description'),
            'type' => Request::post('type', 'guide'),
            'file_url' => $fileUrl,
            'category_id' => Request::post('category_id'),
            'user_id' => Session::get('user_id'),
            'status' => 'published',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        Session::setFlash('success', 'Ressource créée.');
        Request::back();
    }

    public function editResource($id) {
        $resource = $this->resourceRepo->findByUserWithId($id, Session::get('user_id'));
        if (!$resource) {
            Session::setFlash('error', 'Ressource introuvable.');
            Request::redirect('/expert/ressources');
        }
        $categories = $this->userRepo->raw("SELECT * FROM categories ORDER BY name ASC");
        $this->render('expert/edit_resource', compact('resource', 'categories'));
    }

    public function updateResource($id) {
        if (!Request::isPost()) { Request::back(); }
        $resource = $this->resourceRepo->findByUserWithId($id, Session::get('user_id'));
        if (!$resource) {
            Session::setFlash('error', 'Ressource introuvable.');
            Request::back();
        }
        $title = trim(Request::post('title'));
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = $slug ?: 'resource-' . time();
        $fileUrl = $resource['file_url'];
        $newFile = $this->uploadService->upload($_FILES['file_url'] ?? [], 'ressources', $slug);
        if ($newFile) $fileUrl = $newFile;
        $this->resourceRepo->update($id, [
            'title' => $title,
            'slug' => $slug,
            'description' => Request::post('description'),
            'type' => Request::post('type', 'guide'),
            'file_url' => $fileUrl,
            'category_id' => Request::post('category_id'),
        ]);
        Session::setFlash('success', 'Ressource mise à jour.');
        Request::back();
    }

    public function deleteResource($id) {
        if (!Request::isPost()) { Request::back(); }
        $this->userRepo->execute("DELETE FROM resources WHERE id = ? AND user_id = ?", [$id, Session::get('user_id')]);
        Session::setFlash('success', 'Ressource supprimée.');
        Request::back();
    }

    public function notifications() {
        $userId = Session::get('user_id');
        $this->notifRepo->markAllRead($userId);
        $notifications = $this->notifRepo->findByUser($userId);
        $this->render('expert/notifications', compact('notifications'));
    }

    public function readAllNotifications() {
        $this->notifRepo->markAllRead(Session::get('user_id'));
        Session::setFlash('success', 'Notifications marquées comme lues.');
        Request::back();
    }

    public function readNotification($id) {
        $this->notifRepo->markRead($id, Session::get('user_id'));
        Request::back();
    }

    public function agenda() {
        $result = $this->agendaService->getExpertAppointments(Session::get('user_id'));
        $upcoming = $result['upcoming'];
        $past = $result['past'];
        $this->render('expert/agenda', compact('upcoming', 'past'));
    }

    public function updateAppointment($id) {
        if (!Request::isPost()) { Request::back(); }
        $action = Request::post('action');
        if ($this->appointmentService->updateByExpert($id, Session::get('user_id'), $action)) {
            $statusMsg = $action === 'confirmed' ? 'confirmé' : 'annulé';
            Session::setFlash('success', "Rendez-vous {$statusMsg}.");
        } else {
            Session::setFlash('error', 'Rendez-vous introuvable.');
        }
        Request::back();
    }

    public function messages() {
        $userId = Session::get('user_id');
        $activePartnerId = Request::get('partner_id');
        $conversations = $this->userRepo->raw(
            "SELECT DISTINCT u.id, u.name, u.avatar,
                    (SELECT message FROM expert_messages WHERE (sender_id = u.id AND receiver_id = ?) OR (sender_id = ? AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_message,
                    (SELECT created_at FROM expert_messages WHERE (sender_id = u.id AND receiver_id = ?) OR (sender_id = ? AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_message_at,
                    (SELECT COUNT(*) FROM expert_messages WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0) as unread_count
             FROM expert_messages em
             JOIN users u ON (em.sender_id = u.id OR em.receiver_id = u.id)
             WHERE (em.sender_id = ? OR em.receiver_id = ?) AND u.id != ? AND u.role_id = (SELECT id FROM roles WHERE slug = 'maman')
             ORDER BY last_message_at DESC",
            [$userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId]
        );
        $chatHistory = [];
        $activePartner = null;
        if ($activePartnerId) {
            $activePartner = $this->userRepo->findById($activePartnerId);
            if ($activePartner) {
                $this->userRepo->execute("UPDATE expert_messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?", [$activePartnerId, $userId]);
                $chatHistory = $this->userRepo->raw(
                    "SELECT em.*, u.name as sender_name FROM expert_messages em JOIN users u ON em.sender_id = u.id WHERE (em.sender_id = ? AND em.receiver_id = ?) OR (em.sender_id = ? AND em.receiver_id = ?) ORDER BY em.created_at ASC",
                    [$userId, $activePartnerId, $activePartnerId, $userId]
                );
            }
        }
        $this->render('expert/messagerie', compact('conversations', 'chatHistory', 'activePartner'));
    }

    public function sendMessage() {
        $userId = Session::get('user_id');
        $receiverId = Request::post('receiver_id');
        $message = trim(Request::post('message'));
        if ($receiverId && $message !== '') {
            $this->userRepo->execute("INSERT INTO expert_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)", [$userId, $receiverId, $message]);
            $this->notifService->sendNewMessage($receiverId, $userId, 'expert');
        }
        Request::redirect('/expert/messagerie?partner_id=' . $receiverId);
    }

    public function parametres() {
        $this->render('expert/parametres');
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

    public function availability() {
        $expertId = Session::get('user_id');
        $slots = $this->availabilityRepo->findByExpert($expertId);
        $unavailableDates = $this->availabilityRepo->findUnavailableDates($expertId);

        $dayNames = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $grouped = [];
        foreach ($slots as $s) {
            $day = (int)$s['day_of_week'];
            $grouped[$day]['label'] = $dayNames[$day] ?? 'Jour ' . $day;
            $grouped[$day]['slots'][] = $s;
        }

        $this->render('expert/disponibilites', compact('grouped', 'unavailableDates', 'dayNames'));
    }

    public function saveAvailability() {
        if (!Request::isPost()) { Request::back(); }
        $expertId = Session::get('user_id');
        $days = Request::post('days', []);
        $startTimes = Request::post('start_times', []);
        $endTimes = Request::post('end_times', []);

        $slots = [];
        foreach ($days as $i => $day) {
            if (isset($startTimes[$i]) && isset($endTimes[$i])) {
                $slots[] = [
                    'day' => $day,
                    'start' => $startTimes[$i],
                    'end' => $endTimes[$i],
                    'active' => 1,
                ];
            }
        }
        $this->availabilityRepo->saveSlots($expertId, $slots);
        Session::setFlash('success', 'Disponibilités enregistrées.');
        Request::back();
    }

    public function addUnavailableDate() {
        if (!Request::isPost()) { Request::back(); }
        $expertId = Session::get('user_id');
        $date = Request::post('unavailable_date');
        $reason = Request::post('reason', '');
        if ($date) {
            $this->availabilityRepo->addUnavailableDate($expertId, $date, $reason);
            Session::setFlash('success', 'Date d\'indisponibilité ajoutée.');
        }
        Request::back();
    }

    public function removeUnavailableDate($date) {
        if (!Request::isPost()) { Request::back(); }
        $expertId = Session::get('user_id');
        $this->availabilityRepo->removeUnavailableDate($expertId, $date);
        Session::setFlash('success', 'Date d\'indisponibilité retirée.');
        Request::back();
    }

    public function availableSlots($id) {
        $date = Request::get('date');
        if (!$date) {
            $this->json([]);
            return;
        }
        $slots = $this->availabilityRepo->getAvailableSlots((int)$id, $date);
        $this->json($slots);
    }

    private function json($data): void {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function directory() {
        $roleExpert = $this->userRepo->rawOne("SELECT id FROM roles WHERE slug = 'expert'");
        $experts = $roleExpert ? $this->userRepo->findByRoleId($roleExpert['id']) : [];
        $this->render('pages/experts', compact('experts'));
    }

    public function showProfile($id) {
        $expert = $this->userRepo->findById($id);
        if (!$expert || $expert['specialty'] === null) {
            $this->render('errors/404');
            return;
        }
        $this->render('pages/expert_detail', compact('expert'));
    }

    private function slugify($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}
