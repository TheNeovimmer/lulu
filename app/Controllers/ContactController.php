<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class ContactController {
    public function index() {
        View::render('pages/contact', ['title' => 'Contact - LUMA'], 'front');
    }

    public function store() {
        $db = Database::getInstance();
        $db->insert(
            "INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)",
            [Request::post('name'), Request::post('email'), Request::post('subject'), Request::post('message')]
        );
        Session::setFlash('success', 'Message envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.');
        Request::back();
    }
}
