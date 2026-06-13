<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminCommunityController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $posts = $db->fetchAll(
            "SELECT p.*, u.name as author_name,
                                (SELECT COUNT(*) FROM community_likes WHERE post_id = p.id) as reaction_count,
                                (SELECT COUNT(*) FROM community_comments WHERE post_id = p.id) as comments_count
                         FROM community_posts p
                         LEFT JOIN users u ON p.user_id = u.id
                         ORDER BY FIELD(p.status, 'reported', 'published', 'hidden'), p.created_at DESC"
        );
        View::render('admin/communaute', compact('posts'), 'admin');
    }

    public function hide($id) {
        $db = Database::getInstance();
        $post = $db->fetch("SELECT status FROM community_posts WHERE id = ?", [$id]);
        if ($post) {
            $newStatus = $post['status'] === 'hidden' ? 'published' : 'hidden';
            $db->query("UPDATE community_posts SET status = ? WHERE id = ?", [$newStatus, $id]);
            Session::setFlash('success', $newStatus === 'hidden' ? 'Publication masquée.' : 'Publication visible.');
        }
        Request::redirect('/admin/communaute');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM community_posts WHERE id = ?", [$id]);
        Session::setFlash('success', 'Publication supprimée.');
        Request::redirect('/admin/communaute');
    }
}
