<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CommentRepository;

class ArticleController extends Controller {
    private ArticleRepository $articleRepo;
    private CategoryRepository $categoryRepo;
    private CommentRepository $commentRepo;

    public function __construct() {
        $this->layout = 'front';
        $this->articleRepo = new ArticleRepository();
        $this->categoryRepo = new CategoryRepository();
        $this->commentRepo = new CommentRepository();
    }

    public function index() {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $category = (int)($_GET['category'] ?? 0);
        $categories = $this->categoryRepo->findAllOrdered();
        $result = $this->articleRepo->findPublished(9, $category ?: null, $page);
        $articles = $result['items'];
        $total = $result['total'];
        $limit = $result['limit'];
        $this->render('pages/blog', compact('articles', 'categories', 'category', 'total', 'limit', 'page'));
    }

    public function show($slug) {
        $article = $this->articleRepo->findBySlug($slug);
        if (!$article) {
            $this->render('errors/404');
            return;
        }
        $this->articleRepo->incrementViews($article['id']);
        $comments = $this->commentRepo->findByArticle($article['id']);
        $popular = $this->articleRepo->getPopular(5);
        $this->render('pages/blog-single', compact('article', 'comments', 'popular'));
    }

    public function comment($slug) {
        if (!Request::isPost()) { Request::back(); }
        $article = $this->articleRepo->findBySlug($slug);
        if (!$article) { Request::back(); }
        $this->commentRepo->create([
            'article_id' => $article['id'],
            'user_id' => Session::get('user_id'),
            'content' => trim(Request::post('content')),
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        Session::setFlash('success', 'Commentaire soumis pour modération.');
        Request::redirect('/blog/' . $slug);
    }
}
