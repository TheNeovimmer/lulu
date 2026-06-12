<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;

class ResourceController extends Controller {
    public function __construct() {
        $this->layout = 'front';
    }

    public function index() {
        $db = Database::getInstance();
        $search = trim(Request::get('search', ''));
        $categoryId = Request::get('category');

        $sql = "SELECT r.*, rc.name as category_name FROM resources r LEFT JOIN resource_categories rc ON r.category_id = rc.id WHERE 1=1";
        $params = [];

        if ($search) {
            $sql .= " AND (r.title LIKE ? OR r.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($categoryId) {
            $sql .= " AND r.category_id = ?";
            $params[] = $categoryId;
        }
        $sql .= " ORDER BY r.created_at DESC";

        $resources = $db->fetchAll($sql, $params);
        $categories = $db->fetchAll("SELECT * FROM resource_categories ORDER BY name");

        $this->render('pages/ressources', compact('resources', 'categories', 'search', 'categoryId'));
    }

    public function show($slug) {
        $db = Database::getInstance();
        $resource = $db->fetch(
            "SELECT r.*, rc.name as category_name FROM resources r LEFT JOIN resource_categories rc ON r.category_id = rc.id WHERE r.slug = ?",
            [$slug]
        );
        if (!$resource) {
            $this->render('errors/404');
            return;
        }
        $this->render('pages/ressources', compact('resource'));
    }
}
