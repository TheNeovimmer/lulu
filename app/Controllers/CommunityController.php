<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
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
        $this->render('pages/communaute', compact('posts', 'totalPages'));
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
