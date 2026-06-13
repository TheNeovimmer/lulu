<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminSettingsController {
    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index() {
        $db = Database::getInstance();
        $settings = $db->fetchAll("SELECT * FROM settings ORDER BY key_name");
        View::render('admin/settings', compact('settings'), 'admin');
    }

    public function update() {
        $db = Database::getInstance();
        foreach (Request::post('settings', []) as $key => $value) {
            $existing = $db->fetch("SELECT id FROM settings WHERE key_name = ?", [$key]);
            if ($existing) {
                $db->query("UPDATE settings SET value = ? WHERE key_name = ?", [$value, $key]);
            } else {
                $db->query("INSERT INTO settings (key_name, value) VALUES (?, ?)", [$key, $value]);
            }
        }
        Session::setFlash('success', 'Paramètres mis à jour.');
        Request::redirect('/admin/parametres');
    }
}
