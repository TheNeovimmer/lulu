<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Repositories\NewsletterRepository;

class NewsletterController {
    private NewsletterRepository $newsletterRepo;

    public function __construct() {
        $this->newsletterRepo = new NewsletterRepository();
    }

    public function subscribe() {
        $validator = new Validator(Request::all());
        $validator->required('email', 'Email')->email('email');
        if (!$validator->passes()) {
            Session::setFlash('error', $validator->firstError());
            Request::back();
        }
        $email = trim(Request::post('email'));
        if ($this->newsletterRepo->findByEmail($email)) {
            Session::setFlash('error', 'Cet email est déjà inscrit.');
            Request::back();
        }
        $this->newsletterRepo->create([
            'email' => $email,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        Session::setFlash('success', 'Inscription à la newsletter réussie.');
        Request::back();
    }

    public function unsubscribe() {
        $email = trim(Request::post('email'));
        $sub = $this->newsletterRepo->findByEmail($email);
        if ($sub) {
            $this->newsletterRepo->delete($sub['id']);
            Session::setFlash('success', 'Désinscription réussie.');
        }
        Request::back();
    }
}
