<?php
namespace App\Controllers;

use App\Repositories\CategoryRepository;
use App\Repositories\ResourceRepository;

class ResourceController extends Controller {
    private ResourceRepository $resourceRepo;
    private CategoryRepository $categoryRepo;

    public function __construct() {
        $this->layout = 'front';
        $this->resourceRepo = new ResourceRepository();
        $this->categoryRepo = new CategoryRepository();
    }

    public function index() {
        $resources = $this->resourceRepo->findPublished();
        $categories = $this->categoryRepo->findAllOrdered();
        $this->render('pages/ressources', compact('resources', 'categories'));
    }

    public function show($slug) {
        $resource = $this->resourceRepo->findBySlug($slug);
        if (!$resource) {
            $this->render('errors/404');
            return;
        }
        $this->resourceRepo->incrementDownloads($resource['id']);
        $this->render('pages/ressource', compact('resource'));
    }
}
