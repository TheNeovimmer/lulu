<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Repositories\TestimonialRepository;
use App\Services\CommunityService;

class CommunityController extends Controller {
    private CommunityService $communityService;

    public function __construct() {
        $this->layout = 'front';
        $this->communityService = new CommunityService();
    }

    public function index() {
        $posts = $this->communityService->getPublishedPosts();
        $totalPages = 1;

        $db = Database::getInstance();
        $testimonialRepo = new TestimonialRepository();
        $testimonials = $testimonialRepo->findApproved();
        $mamansCount = $db->fetch("SELECT COUNT(*) as count FROM users")['count'] ?? 0;
        $postsCount = $db->fetch("SELECT COUNT(*) as count FROM community_posts WHERE status = 'published'")['count'] ?? 0;

        $themes = [
            ['name' => 'Grossesse', 'icon' => 'bi-emoji-heart-eyes', 'desc' => 'Suivi, symptômes, alimentation', 'members' => '12k+'],
            ['name' => 'Bébé', 'icon' => 'bi-heart', 'desc' => 'Soins, éveil, alimentation', 'members' => '15k+'],
            ['name' => 'Parentalité', 'icon' => 'bi-people', 'desc' => 'Éducation, organisation, vie de famille', 'members' => '10k+'],
            ['name' => 'Bien-être & Santé', 'icon' => 'bi-flower2', 'desc' => 'Santé mentale, forme, nutrition', 'members' => '8k+'],
        ];

        $sujets = ['Grossesse', 'Accouchement', 'Allaitement', 'Sommeil bébé', 'Soutien moral', 'Retour à la maison', 'Alimentation'];

        $this->render('pages/communaute', compact('posts', 'totalPages', 'testimonials', 'mamansCount', 'postsCount', 'themes', 'sujets'));
    }

    public function show($id) {
        $result = $this->communityService->getPostWithDetails($id);
        if (!$result) {
            $this->render('errors/404');
            return;
        }
        $this->render('pages/sujet', $result);
    }

    public function store() {
        if (!Request::isPost()) { Request::back(); }
        $validator = new Validator(Request::all());
        $validator->required('title', 'Le titre')->required('content', 'Le contenu');
        if (!$validator->passes()) {
            Session::setFlash('error', $validator->firstError());
            Request::back();
        }
        $this->communityService->createPost(
            Session::get('user_id'),
            trim(Request::post('title')),
            trim(Request::post('content'))
        );
        Session::setFlash('success', 'Sujet créé avec succès.');
        Request::redirect('/communaute');
    }

    public function comment($id) {
        if (!Request::isPost()) { Request::back(); }
        $validator = new Validator(Request::all());
        $validator->required('content', 'Le commentaire');
        if (!$validator->passes()) {
            Session::setFlash('error', $validator->firstError());
            Request::back();
        }
        $this->communityService->addComment($id, Session::get('user_id'), trim(Request::post('content')));
        Request::redirect('/communaute/' . $id);
    }

    public function like($id) {
        if (!Request::isPost()) { Request::back(); }
        $result = $this->communityService->toggleLike($id, Session::get('user_id'));
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
        Request::redirect('/communaute/' . $id);
    }

    public function deleteComment($id) {
        if (!Request::isPost()) { Request::back(); }
        $postId = $this->communityService->deleteComment($id, Session::get('user_id'));
        if ($postId) {
            Session::setFlash('success', 'Commentaire supprimé.');
            Request::redirect('/communaute/' . $postId);
        }
        Session::setFlash('error', 'Commentaire introuvable.');
        Request::back();
    }
}
