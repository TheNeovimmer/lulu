<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminCommentController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $status = Request::get('status', '');

        $statusFilter = '';
        $params = [];
        if ($status) {
            $statusFilter = "WHERE c.status = ?";
            $params[] = $status;
        }

        $comments = $db->fetchAll(
            "SELECT c.*, u.name as user_name, a.title as article_title
                         FROM comments c
                         LEFT JOIN users u ON c.user_id = u.id
                         LEFT JOIN articles a ON c.article_id = a.id
                         $statusFilter
                         ORDER BY c.created_at DESC",
            $params
        );
        View::render('admin/comments', compact('comments', 'status'), 'admin');
    }

    public function approve($id) {
        $db = Database::getInstance();
        $db->query("UPDATE comments SET status = 'approved' WHERE id = ?", [$id]);
        Session::setFlash('success', 'Commentaire approuvé.');
        Request::back();
    }

    public function reject($id) {
        $db = Database::getInstance();
        $db->query("UPDATE comments SET status = 'rejected' WHERE id = ?", [$id]);
        Session::setFlash('success', 'Commentaire rejeté.');
        Request::back();
    }
}
