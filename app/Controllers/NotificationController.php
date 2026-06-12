<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;

class NotificationController extends Controller {
    public function __construct() {
        $this->layout = 'front';
        $this->authCheck();
    }

    public function index() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $db->query("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0", [$userId]);

        $notifications = $db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );

        $this->render('notifications/index', compact('notifications'));
    }

    public function markRead($id) {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        $db->query(
            "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?",
            [$id, $userId]
        );

        Session::setFlash('success', 'Notification marquée comme lue.');
        Request::back();
    }
}
