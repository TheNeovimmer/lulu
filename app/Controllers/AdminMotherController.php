<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Repositories\MotherRepository;

class AdminMotherController {
    private MotherRepository $motherRepo;

    public function __construct() {
        if (Session::get('user_role_slug') !== 'admin') {
            header('Location: /');
            exit;
        }
        $this->motherRepo = new MotherRepository();
    }

    public function index() {
        $mamans = $this->motherRepo->allWithPregnancies();
        View::render('admin/mamans', compact('mamans'), 'admin');
    }

    public function show($id) {
        $mother = $this->motherRepo->findWithDetails($id);
        if (!$mother) {
            Session::setFlash('error', 'Maman introuvable.');
            Request::redirect('/admin/mamans');
        }
        $pregnancy = $this->motherRepo->rawOne("SELECT * FROM pregnancies WHERE mother_id = ?", [$mother['mother_id']]);
        $babies = $this->motherRepo->raw("SELECT * FROM babies WHERE mother_id = ?", [$mother['mother_id']]);
        foreach ($babies as &$b) {
            $b['vaccinations'] = $this->motherRepo->raw("SELECT * FROM vaccinations WHERE baby_id = ? ORDER BY due_date ASC", [$b['id']]);
            $b['growth_records'] = $this->motherRepo->raw("SELECT * FROM growth_records WHERE baby_id = ? ORDER BY record_date ASC", [$b['id']]);
        }
        View::render('admin/mother-show', compact('mother', 'pregnancy', 'babies'), 'admin');
    }
}
