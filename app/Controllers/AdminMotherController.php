<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminMotherController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $mamans = $db->fetchAll(
            "SELECT u.*, p.due_date, p.week as weeks_gestation, p.created_at as pregnancy_since
                         FROM users u
                         LEFT JOIN mothers m ON u.id = m.user_id
                         LEFT JOIN pregnancies p ON m.id = p.mother_id
                         JOIN roles r ON u.role_id = r.id
                         WHERE r.slug = 'maman'
                         ORDER BY u.created_at DESC",
        );
        View::render('admin/mamans', compact('mamans'), 'admin');
    }

    public function show($id) {
        $db = Database::getInstance();
        $mother = $db->fetch(
            "SELECT u.*, m.id as mother_id, m.date_of_birth, m.city
             FROM users u
             LEFT JOIN mothers m ON u.id = m.user_id
             WHERE u.id = ?",
            [$id]
        );
        if (!$mother) {
            Session::setFlash('error', 'Maman introuvable.');
            Request::redirect('/admin/mamans');
        }
        $pregnancy = $db->fetch("SELECT * FROM pregnancies WHERE mother_id = ?", [$mother['mother_id']]);
        $baby = $db->fetch("SELECT * FROM babies WHERE mother_id = ?", [$mother['mother_id']]);
        $babies = $baby ? [$baby] : [];
        foreach ($babies as &$b) {
            $b['vaccinations'] = $db->fetchAll("SELECT * FROM vaccinations WHERE baby_id = ? ORDER BY due_date ASC", [$b['id']]);
            $b['growth_records'] = $db->fetchAll("SELECT * FROM growth_records WHERE baby_id = ? ORDER BY record_date ASC", [$b['id']]);
        }
        View::render('admin/mother-show', compact('mother', 'pregnancy', 'babies'), 'admin');
    }
}
