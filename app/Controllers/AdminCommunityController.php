<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Repositories\CommunityPostRepository;

class AdminCommunityController {
    private CommunityPostRepository $postRepo;

    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
        $this->postRepo = new CommunityPostRepository();
    }

    public function index() {
        $posts = $this->postRepo->raw(
            "SELECT p.*, u.name as author_name FROM community_posts p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC"
        );
        View::render('admin/communaute', compact('posts'), 'admin');
    }

    public function hide($id) {
        $post = $this->postRepo->findById($id);
        if ($post) {
            $newStatus = $post['status'] === 'hidden' ? 'published' : 'hidden';
            $this->postRepo->update($id, ['status' => $newStatus]);
            Session::setFlash('success', $newStatus === 'hidden' ? 'Sujet masqué.' : 'Sujet réaffiché.');
        }
        Request::redirect('/admin/communaute');
    }

    public function destroy($id) {
        $this->postRepo->delete($id);
        Session::setFlash('success', 'Sujet supprimé.');
        Request::redirect('/admin/communaute');
    }
}
