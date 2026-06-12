# LUMA Full-Stack MVC Conversion Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Convert 4 static PHP pages to a complete MVC PHP/MySQL full-stack application with auth, dashboards, CRUD, and responsive design preserving the existing UI.

**Architecture:** Vanilla PHP MVC with custom Router, PDO Database layer, Middleware chain, and template system. 10 database tables. Admin + Member dashboards.

**Tech Stack:** PHP 8+, MySQL, PDO, vanilla CSS (charte preserved), vanilla JS

---

### Task 1: Project Structure & Core Framework

**Files:**
- Create: `.htaccess`
- Create: `env.example.php`
- Create: `public/index.php`
- Create: `public/.htaccess`
- Create: `app/Core/Router.php`
- Create: `app/Core/Database.php`
- Create: `app/Core/Request.php`
- Create: `app/Core/Session.php`
- Create: `app/Core/View.php`

- [ ] **Step 1: Root .htaccess**

Rewrite all requests into `public/`:

```apache
RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
```

- [ ] **Step 2: env.example.php**

Define environment configuration template:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'luma');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', 'http://localhost/luma');
```

- [ ] **Step 3: public/.htaccess**

URL rewriting to `index.php`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

- [ ] **Step 4: public/index.php — Front Controller**

```php
<?php
require_once __DIR__ . '/../env.php';
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require $file;
});

use App\Core\Router;
use App\Core\Session;

Session::start();
$router = new Router();
require_once __DIR__ . '/../routes.php';
$router->dispatch($_SERVER['REQUEST_METHOD'], $_GET['url'] ?? '');
```

- [ ] **Step 5: app/Core/Session.php**

```php
<?php
namespace App\Core;

class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }
    public static function set($key, $value) { $_SESSION[$key] = $value; }
    public static function get($key, $default = null) { return $_SESSION[$key] ?? $default; }
    public static function has($key) { return isset($_SESSION[$key]); }
    public static function remove($key) { unset($_SESSION[$key]); }
    public static function destroy() { session_destroy(); }
    public static function setFlash($key, $value) { $_SESSION['_flash'][$key] = $value; }
    public static function getFlash($key, $default = null) {
        $val = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }
}
```

- [ ] **Step 6: app/Core/Database.php**

```php
<?php
namespace App\Core;

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $this->pdo = new \PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]
        );
    }

    public static function getInstance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    public function getConnection() { return $this->pdo; }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch($sql, $params = []) { return $this->query($sql, $params)->fetch(); }
    public function fetchAll($sql, $params = []) { return $this->query($sql, $params)->fetchAll(); }
    public function insert($sql, $params = []) { $this->query($sql, $params); return $this->pdo->lastInsertId(); }
}
```

- [ ] **Step 7: app/Core/Request.php**

```php
<?php
namespace App\Core;

class Request {
    public static function method() { return $_SERVER['REQUEST_METHOD']; }
    public static function post($key, $default = null) { return $_POST[$key] ?? $default; }
    public static function get($key, $default = null) { return $_GET[$key] ?? $default; }
    public static function file($key) { return $_FILES[$key] ?? null; }
    public static function all() { return $_POST; }
    public static function isPost() { return self::method() === 'POST'; }
    public static function redirect($url) { header('Location: ' . BASE_URL . $url); exit; }
    public static function back() { header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/')); exit; }
}
```

- [ ] **Step 8: app/Core/View.php**

```php
<?php
namespace App\Core;

class View {
    public static function render($view, $data = [], $layout = 'front') {
        extract($data);
        ob_start();
        require __DIR__ . "/../../views/{$view}.php";
        $content = ob_get_clean();
        require __DIR__ . "/../../views/layouts/{$layout}.php";
    }

    public static function renderPartial($view, $data = []) {
        extract($data);
        require __DIR__ . "/../../views/{$view}.php";
    }
}
```

- [ ] **Step 9: app/Core/Router.php**

```php
<?php
namespace App\Core;

class Router {
    private $routes = [];

    public function get($path, $handler) { $this->routes['GET'][$path] = $handler; }
    public function post($path, $handler) { $this->routes['POST'][$path] = $handler; }

    public function dispatch($method, $url) {
        $url = $url ? trim($url, '/') : '';
        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
            if (preg_match('#^' . $pattern . '$#', $url, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$controller, $action] = explode('@', $handler);
                $controller = "App\\Controllers\\{$controller}";
                $instance = new $controller();
                call_user_func_array([$instance, $action], $params);
                return;
            }
        }
        http_response_code(404);
        View::render('errors/404', [], 'front');
    }
}
```

- [ ] **Step 10: Verify structure**

```bash
ls -la public/ app/Core/ env.example.php .htaccess
```

---

### Task 2: Database Migration SQL

**Files:**
- Create: `migrations/001_create_tables.sql`

- [ ] **Step 1: Write migration SQL**

Copy the 10 CREATE TABLE statements from the design doc into `migrations/001_create_tables.sql`.

- [ ] **Step 2: Import migration**

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS luma CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root luma < migrations/001_create_tables.sql
```

- [ ] **Step 3: Verify tables exist**

```bash
mysql -u root luma -e "SHOW TABLES;"
mysql -u root luma -e "DESCRIBE users;"
mysql -u root luma -e "DESCRIBE articles;"
mysql -u root luma -e "DESCRIBE categories;"
```

- [ ] **Step 4: Insert seed data — categories**

```sql
INSERT INTO categories (name, slug, description) VALUES
('Grossesse', 'grossesse', 'Articles sur la grossesse'),
('Bébé', 'bebe', 'Soins et développement du bébé'),
('Bien-être', 'bien-etre', 'Bien-être et santé maternelle'),
('Allaitement', 'allaitement', 'Conseils sur l allaitement'),
('Retour d\'expérience', 'retour-experience', 'Témoignages et retours d\'expérience'),
('Organisation', 'organisation', 'Conseils d\'organisation familiale');
```

- [ ] **Step 5: Insert admin user**

```sql
INSERT INTO users (name, email, password, role) VALUES
('Admin LUMA', 'admin@luma.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- password: password
```

---

### Task 3: Routes Definition + Middleware

**Files:**
- Create: `routes.php`
- Create: `app/Middleware/AuthMiddleware.php`
- Create: `app/Middleware/AdminMiddleware.php`
- Create: `app/Middleware/GuestMiddleware.php`

- [ ] **Step 1: routes.php**

```php
<?php
use App\Core\Router;

$router->get('', 'PageController@home');
$router->get('blog', 'ArticleController@index');
$router->get('blog/{slug}', 'ArticleController@show');
$router->post('blog/{slug}/comment', 'ArticleController@comment');
$router->get('community', 'CommunityController@index');
$router->get('community/{id}', 'CommunityController@topic');
$router->post('community/{id}/reply', 'CommunityController@reply');
$router->get('contact', 'ContactController@index');
$router->post('contact', 'ContactController@store');
$router->get('faq', 'FaqController@index');
$router->get('auth/login', 'AuthController@login');
$router->post('auth/login', 'AuthController@authenticate');
$router->get('auth/register', 'AuthController@register');
$router->post('auth/register', 'AuthController@store');
$router->get('auth/logout', 'AuthController@logout');
$router->get('newsletter/subscribe', 'NewsletterController@subscribe');
$router->post('newsletter/subscribe', 'NewsletterController@store');
$router->get('dashboard', 'DashboardController@index');
$router->get('admin', 'AdminController@index');
$router->get('admin/articles', 'AdminArticleController@index');
$router->get('admin/articles/create', 'AdminArticleController@create');
$router->post('admin/articles/create', 'AdminArticleController@store');
$router->get('admin/articles/edit/{id}', 'AdminArticleController@edit');
$router->post('admin/articles/edit/{id}', 'AdminArticleController@update');
$router->post('admin/articles/delete/{id}', 'AdminArticleController@destroy');
$router->get('admin/categories', 'AdminCategoryController@index');
$router->post('admin/categories/create', 'AdminCategoryController@store');
$router->post('admin/categories/delete/{id}', 'AdminCategoryController@destroy');
$router->get('admin/users', 'AdminUserController@index');
$router->post('admin/users/toggle-role/{id}', 'AdminUserController@toggleRole');
$router->post('admin/users/delete/{id}', 'AdminUserController@destroy');
$router->get('admin/comments', 'AdminCommentController@index');
$router->post('admin/comments/approve/{id}', 'AdminCommentController@approve');
$router->post('admin/comments/reject/{id}', 'AdminCommentController@reject');
$router->get('admin/testimonials', 'AdminTestimonialController@index');
$router->post('admin/testimonials/approve/{id}', 'AdminTestimonialController@approve');
$router->post('admin/testimonials/reject/{id}', 'AdminTestimonialController@reject');
$router->get('admin/faqs', 'AdminFaqController@index');
$router->post('admin/faqs/create', 'AdminFaqController@store');
$router->post('admin/faqs/update/{id}', 'AdminFaqController@update');
$router->post('admin/faqs/delete/{id}', 'AdminFaqController@destroy');
$router->get('admin/contacts', 'AdminContactController@index');
$router->post('admin/contacts/mark-read/{id}', 'AdminContactController@markRead');
$router->post('admin/contacts/delete/{id}', 'AdminContactController@destroy');
$router->get('admin/newsletters', 'AdminNewsletterController@index');
$router->post('admin/newsletters/delete/{id}', 'AdminNewsletterController@destroy');
$router->get('admin/forum', 'AdminForumController@index');
$router->post('admin/forum/delete-topic/{id}', 'AdminForumController@deleteTopic');
```

- [ ] **Step 2: AuthMiddleware.php**

```php
<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Request;

class AuthMiddleware {
    public static function check() {
        if (!Session::has('user_id')) {
            Request::redirect('/auth/login');
        }
    }
}
```

- [ ] **Step 3: AdminMiddleware.php**

```php
<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Request;

class AdminMiddleware {
    public static function check() {
        if (!Session::has('user_id') || Session::get('user_role') !== 'admin') {
            Request::redirect('/auth/login');
        }
    }
}
```

- [ ] **Step 4: GuestMiddleware.php**

```php
<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Request;

class GuestMiddleware {
    public static function check() {
        if (Session::has('user_id')) {
            Request::redirect('/');
        }
    }
}
```

---

### Task 4: Auth System (Controllers + Views)

**Files:**
- Create: `app/Controllers/AuthController.php`
- Create: `app/Models/User.php`
- Create: `app/Repositories/UserRepository.php`
- Create: `views/auth/login.php`
- Create: `views/auth/register.php`

- [ ] **Step 1: UserRepository.php**

```php
<?php
namespace App\Repositories;

use App\Core\Database;

class UserRepository {
    private $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function findByEmail($email) {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function findById($id) {
        return $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function create($data) {
        return $this->db->insert(
            "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'member')",
            [$data['name'], $data['email'], password_hash($data['password'], PASSWORD_BCRYPT)]
        );
    }

    public function updateAvatar($id, $avatar) {
        $this->db->query("UPDATE users SET avatar = ? WHERE id = ?", [$avatar, $id]);
    }

    public function updateProfile($id, $data) {
        $this->db->query("UPDATE users SET name = ?, email = ? WHERE id = ?", [$data['name'], $data['email'], $id]);
    }

    public function updatePassword($id, $password) {
        $this->db->query("UPDATE users SET password = ? WHERE id = ?", [password_hash($password, PASSWORD_BCRYPT), $id]);
    }

    public function findAll() {
        return $this->db->fetchAll("SELECT * FROM users ORDER BY created_at DESC");
    }

    public function toggleRole($id) {
        $user = $this->findById($id);
        $newRole = $user['role'] === 'admin' ? 'member' : 'admin';
        $this->db->query("UPDATE users SET role = ? WHERE id = ?", [$newRole, $id]);
    }

    public function delete($id) {
        $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
    }

    public function count() {
        return $this->db->fetch("SELECT COUNT(*) as count FROM users")['count'];
    }
}
```

- [ ] **Step 2: AuthController.php**

```php
<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Repositories\UserRepository;

class AuthController {
    private $userRepo;
    public function __construct() { $this->userRepo = new UserRepository(); }

    public function login() {
        View::render('auth/login', ['title' => 'Connexion - LUMA'], 'front');
    }

    public function authenticate() {
        $email = Request::post('email');
        $password = Request::post('password');
        $user = $this->userRepo->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            Session::setFlash('error', 'Email ou mot de passe incorrect');
            Request::back();
        }

        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['role']);
        Session::set('user_avatar', $user['avatar']);

        if ($user['role'] === 'admin') {
            Request::redirect('/admin');
        }
        Request::redirect('/dashboard');
    }

    public function register() {
        View::render('auth/register', ['title' => 'Inscription - LUMA'], 'front');
    }

    public function store() {
        $name = trim(Request::post('name'));
        $email = trim(Request::post('email'));
        $password = Request::post('password');
        $confirm = Request::post('password_confirm');

        $errors = [];
        if (strlen($name) < 2) $errors[] = 'Nom trop court';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide';
        if (strlen($password) < 6) $errors[] = 'Mot de passe trop court (6 caractères minimum)';
        if ($password !== $confirm) $errors[] = 'Les mots de passe ne correspondent pas';
        if ($this->userRepo->findByEmail($email)) $errors[] = 'Cet email est déjà utilisé';

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            Request::back();
        }

        $this->userRepo->create(['name' => $name, 'email' => $email, 'password' => $password]);
        Session::setFlash('success', 'Inscription réussie ! Connectez-vous.');
        Request::redirect('/auth/login');
    }

    public function logout() {
        Session::destroy();
        Request::redirect('/');
    }
}
```

- [ ] **Step 3: views/auth/login.php**

Preserve the existing dark design. Form styling matches the contact page.

```php
<div class="auth-section">
  <div class="auth-container">
    <h1 class="auth-title">Connexion</h1>
    <p class="auth-subtitle">Retrouvez votre espace LUMA</p>
    
    <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
      <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>
    
    <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
      <div class="alert alert-error"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <form method="POST" action="/auth/login" class="auth-form">
      <div class="form-group">
        <input type="email" name="email" placeholder="Votre email" required>
      </div>
      <div class="form-group">
        <input type="password" name="password" placeholder="Votre mot de passe" required>
      </div>
      <button type="submit" class="btn-primary auth-btn">Se connecter</button>
    </form>
    <p class="auth-link">Pas encore de compte ? <a href="/auth/register">S'inscrire</a></p>
  </div>
</div>
```

- [ ] **Step 4: views/auth/register.php**

```php
<div class="auth-section">
  <div class="auth-container">
    <h1 class="auth-title">Créer un compte</h1>
    <p class="auth-subtitle">Rejoignez la communauté LUMA</p>
    
    <?php if ($errors = \App\Core\Session::getFlash('errors')): ?>
      <?php foreach ($errors as $error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST" action="/auth/register" class="auth-form">
      <div class="form-group">
        <input type="text" name="name" placeholder="Votre nom" required>
      </div>
      <div class="form-group">
        <input type="email" name="email" placeholder="Votre email" required>
      </div>
      <div class="form-group">
        <input type="password" name="password" placeholder="Mot de passe (6 caractères min)" required>
      </div>
      <div class="form-group">
        <input type="password" name="password_confirm" placeholder="Confirmer le mot de passe" required>
      </div>
      <button type="submit" class="btn-primary auth-btn">S'inscrire</button>
    </form>
    <p class="auth-link">Déjà inscrite ? <a href="/auth/login">Se connecter</a></p>
  </div>
</div>
```

---

### Task 5: Layouts (Header + Footer)

**Files:**
- Create: `views/layouts/front.php`
- Create: `views/layouts/admin.php`

- [ ] **Step 1: views/layouts/front.php**

Preserve the exact existing UI from the original pages. Extract header/nav from the original index.php (hero nav: "LUMA", "Blog", "Communauté", "Contact") and footer (the #632538 footer with social links, app store buttons, nav columns).

```php
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'LUMA - Là où commence le soin' ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/responsive.css">
</head>
<body>
  <header class="site-header">
    <div class="header-inner">
      <a href="/" class="logo"><span class="logo-pink">LUMA</span><span class="logo-white">, la où commence le soin</span></a>
      <nav class="main-nav">
        <a href="/">Accueil</a>
        <a href="/blog">Blog</a>
        <a href="/community">Communauté</a>
        <a href="/faq">FAQ</a>
        <a href="/contact">Contact</a>
        <?php if (\App\Core\Session::has('user_id')): ?>
          <a href="/dashboard">Mon compte</a>
          <a href="/auth/logout">Déconnexion</a>
        <?php else: ?>
          <a href="/auth/login" class="nav-btn">Connexion</a>
          <a href="/auth/register" class="nav-btn nav-btn-primary">S'inscrire</a>
        <?php endif; ?>
      </nav>
      <button class="hamburger" id="hamburger">☰</button>
    </div>
  </header>

  <main class="site-main">
    <?= $content ?>
  </main>

  <footer class="site-footer" style="background:#632538; padding:60px 0; margin-top:100px;">
    <div class="footer-inner" style="max-width:1320px; margin:0 auto; display:flex; justify-content:space-between; flex-wrap:wrap;">
      <div class="footer-brand">
        <div class="footer-logo" style="color:#F5C0AF; font-size:32px; font-family:Royalist; margin-bottom:20px;">LUMA</div>
        <p style="color:white; font-size:16px; font-family:Poppins; font-weight:800;">Là où commence le soin</p>
        <div class="social-icons" style="display:flex; gap:10px; margin-top:20px;">
          <!-- Social icons SVG from original -->
        </div>
      </div>
      <div class="footer-links">
        <h4 style="color:white; font-family:Poppins; font-weight:700;">LUMA</h4>
        <a href="/" style="color:white; font-family:Poppins; font-weight:300; display:block;">à propos</a>
        <a href="/blog" style="color:white; font-family:Poppins; font-weight:300; display:block;">Blog</a>
        <a href="/community" style="color:white; font-family:Poppins; font-weight:300; display:block;">Communauté</a>
      </div>
      <div class="footer-links">
        <h4 style="color:white; font-family:Poppins; font-weight:700;">Ressources</h4>
        <a href="/faq" style="color:white; font-family:Poppins; font-weight:300; display:block;">FAQ</a>
        <a href="/contact" style="color:white; font-family:Poppins; font-weight:300; display:block;">Contact</a>
      </div>
      <div class="footer-apps">
        <h4 style="color:white; font-family:Poppins; font-weight:700;">Télécharger l'app</h4>
        <div class="app-buttons" style="display:flex; gap:10px; margin-top:10px;">
          <div class="app-btn" style="background:rgba(255,255,255,0.10); border-radius:10px; outline:1px solid #F5C0AF; padding:6px 14px;">
            <span style="color:white; font-size:12px;">download</span><br>
            <span style="color:white; font-size:16px; font-weight:700;">App Store</span>
          </div>
          <div class="app-btn" style="background:rgba(255,255,255,0.10); border-radius:10px; outline:1px solid #F5C0AF; padding:6px 14px;">
            <span style="color:white; font-size:12px;">GET IT ON</span><br>
            <span style="color:white; font-size:16px; font-weight:700;">Google Play</span>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <script src="/assets/js/app.js"></script>
</body>
</html>
```

- [ ] **Step 2: views/layouts/admin.php**

Admin layout with sidebar navigation:

```php
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'Admin - LUMA' ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">
  <div class="admin-wrapper">
    <aside class="admin-sidebar">
      <div class="sidebar-header">
        <h2><span style="color:#F0A0BB;">LUMA</span> Admin</h2>
      </div>
      <nav class="sidebar-nav">
        <a href="/admin">📊 Dashboard</a>
        <a href="/admin/articles">📝 Articles</a>
        <a href="/admin/categories">📁 Catégories</a>
        <a href="/admin/comments">💬 Commentaires</a>
        <a href="/admin/testimonials">⭐ Témoignages</a>
        <a href="/admin/faqs">❓ FAQ</a>
        <a href="/admin/forum">🗣️ Forum</a>
        <a href="/admin/contacts">✉️ Messages</a>
        <a href="/admin/newsletters">📧 Newsletter</a>
        <a href="/admin/users">👥 Utilisateurs</a>
        <hr>
        <a href="/">← Voir le site</a>
        <a href="/auth/logout">🚪 Déconnexion</a>
      </nav>
    </aside>
    <main class="admin-main">
      <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
      <?php endif; ?>
      <?= $content ?>
    </main>
  </div>
  <script src="/assets/js/app.js"></script>
</body>
</html>
```

---

### Task 6: CSS — style.css + responsive.css + admin.css

**Files:**
- Create: `public/assets/css/style.css`
- Create: `public/assets/css/responsive.css`
- Create: `public/assets/css/admin.css`

- [ ] **Step 1: public/assets/css/style.css**

Core styles preserving the original design system:

```css
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@300;400;600;700;800&display=swap');

@font-face {
  font-family: 'Royalist';
  src: url('/assets/fonts/royalist.woff2') format('woff2');
}

:root {
  --bg-dark: #2E0F1C;
  --bg-card: #632538;
  --accent-pink: #C94B72;
  --accent-light: #F0A0BB;
  --accent-teal: #70A2B4;
  --text-white: #F5F5F5;
  --text-muted: #9FB3DF;
  --glass-bg: rgba(255, 255, 255, 0.10);
  --radius-sm: 10px;
  --radius-md: 20px;
  --radius-lg: 50px;
  --font-heading: 'Royalist', serif;
  --font-body: 'Inter', sans-serif;
  --font-accent: 'Poppins', sans-serif;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  background: var(--bg-dark);
  color: var(--text-white);
  font-family: var(--font-body);
  overflow-x: hidden;
}

a { color: var(--accent-light); text-decoration: none; }
a:hover { text-decoration: underline; }

/* Header */
.site-header {
  background: rgba(46, 15, 28, 0.95);
  padding: 20px 40px;
  position: sticky;
  top: 0;
  z-index: 100;
  backdrop-filter: blur(10px);
}
.header-inner {
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.logo { font-family: var(--font-heading); font-size: 28px; }
.logo-pink { color: var(--accent-light); }
.logo-white { color: white; font-size: 20px; }
.main-nav { display: flex; gap: 30px; align-items: center; }
.main-nav a { color: white; font-family: var(--font-body); font-weight: 300; font-size: 16px; }
.nav-btn {
  padding: 12px 24px;
  border-radius: var(--radius-lg);
  border: 1px solid var(--accent-light);
  font-weight: 400;
}
.nav-btn-primary { background: var(--accent-light); color: var(--bg-dark) !important; font-weight: 600; }
.hamburger { display: none; font-size: 28px; color: white; cursor: pointer; background: none; border: none; }

/* Buttons */
.btn-primary {
  background: var(--accent-light);
  color: var(--bg-dark);
  border: none;
  padding: 15px 30px;
  border-radius: var(--radius-lg);
  font-family: var(--font-body);
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  display: inline-block;
}
.btn-primary:hover { opacity: 0.9; text-decoration: none; }
.btn-outline {
  background: transparent;
  color: white;
  border: 1px solid var(--accent-pink);
  padding: 15px 30px;
  border-radius: var(--radius-lg);
  font-family: var(--font-body);
  font-size: 16px;
  cursor: pointer;
}

/* Hero Section */
.hero-section {
  min-height: 80vh;
  display: flex;
  align-items: center;
  padding: 60px 40px;
  position: relative;
  overflow: hidden;
}
.hero-content { max-width: 650px; z-index: 1; }
.hero-title { font-family: var(--font-heading); font-size: 96px; line-height: 1; margin-bottom: 30px; }
.hero-title .highlight { color: var(--accent-light); }
.hero-subtitle { font-size: 22px; font-weight: 300; line-height: 1.6; margin-bottom: 30px; color: var(--text-muted); }
.hero-cta { display: flex; gap: 20px; margin-bottom: 40px; }
.hero-features { display: flex; gap: 30px; }
.hero-feature { text-align: center; }
.hero-feature span { display: block; font-size: 14px; font-weight: 300; margin-top: 10px; }

/* Sections */
.section { padding: 80px 40px; }
.section-inner { max-width: 1320px; margin: 0 auto; }
.section-title { font-family: var(--font-heading); font-size: 96px; text-align: center; margin-bottom: 20px; }
.section-subtitle { font-size: 32px; text-align: center; color: var(--accent-teal); margin-bottom: 60px; }

/* Cards */
.card {
  background: var(--bg-card);
  border-radius: var(--radius-sm);
  border: 1px solid var(--accent-pink);
  padding: 40px;
  text-align: center;
}
.card h3 { color: var(--accent-pink); font-size: 24px; margin: 20px 0; }
.card p { font-size: 20px; color: var(--text-white); line-height: 1.5; }

/* Stats section */
.stats-bar {
  display: flex;
  justify-content: center;
  gap: 76px;
  flex-wrap: wrap;
}
.stat-item { text-align: center; }
.stat-number { font-family: var(--font-accent); font-size: 36px; font-weight: 600; color: white; }
.stat-label { font-size: 16px; font-weight: 600; color: white; margin-top: 5px; }

/* Testimonials */
.testimonial-card {
  background: rgba(255,255,255,0.10);
  border-radius: var(--radius-sm);
  border: 1px solid var(--accent-pink);
  padding: 30px;
}
.testimonial-card .stars { color: #FFC900; font-size: 22px; }

/* Forms */
.form-group { margin-bottom: 20px; }
.form-group input, .form-group textarea, .form-group select {
  width: 100%;
  padding: 18px 24px;
  background: rgba(255,255,255,0.10);
  border: 1px solid var(--accent-pink);
  border-radius: var(--radius-lg);
  color: white;
  font-size: 16px;
  font-family: var(--font-body);
}
.form-group input::placeholder, .form-group textarea::placeholder { color: rgba(255,255,255,0.6); }
.form-group textarea { min-height: 150px; border-radius: var(--radius-sm); }

/* Auth */
.auth-section {
  min-height: 70vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
}
.auth-container {
  width: 100%;
  max-width: 480px;
  background: var(--bg-card);
  border-radius: var(--radius-md);
  border: 1px solid var(--accent-pink);
  padding: 60px 40px;
}
.auth-title { font-family: var(--font-heading); font-size: 48px; text-align: center; margin-bottom: 10px; }
.auth-subtitle { text-align: center; color: var(--text-muted); margin-bottom: 40px; font-size: 18px; }
.auth-btn { width: 100%; text-align: center; margin-top: 10px; }
.auth-link { text-align: center; margin-top: 30px; color: var(--text-muted); }

/* Alerts */
.alert {
  padding: 15px 20px;
  border-radius: var(--radius-sm);
  margin-bottom: 20px;
  font-size: 14px;
}
.alert-success { background: rgba(0, 200, 83, 0.2); border: 1px solid #00C853; color: #00C853; }
.alert-error { background: rgba(255, 0, 0, 0.2); border: 1px solid #FF0000; color: #FF6B6B; }

/* Blog */
.blog-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
.blog-card { background: rgba(217,217,217,0.05); border-radius: 15px; overflow: hidden; }
.blog-card img { width: 100%; height: 260px; object-fit: cover; }
.blog-card-body { padding: 25px; }
.blog-card-body .category-tag { display: inline-block; background: var(--bg-dark); border-radius: 50px; padding: 8px 20px; font-size: 14px; margin-bottom: 15px; }
.blog-card-body h3 { font-size: 20px; font-family: var(--font-body); font-weight: 400; margin-bottom: 10px; }
.blog-card-body .meta { color: var(--accent-light); font-size: 16px; }

/* Filter pills */
.filter-pills { display: flex; gap: 10px; flex-wrap: wrap; }
.filter-pill {
  padding: 9px 12px;
  background: rgba(240, 160, 187, 0.37);
  border-radius: 50px;
  color: white;
  font-size: 16px;
  cursor: pointer;
  border: none;
  font-family: var(--font-body);
}
.filter-pill.active { background: var(--accent-light); color: var(--bg-dark); }

/* Community page */
.search-bar {
  background: white;
  border-radius: 50px;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
}
.search-bar input {
  background: none;
  border: none;
  font-size: 16px;
  color: black;
  width: 70%;
}
.search-bar input::placeholder { color: #999; }
.search-bar button {
  background: var(--accent-light);
  border: none;
  padding: 15px 30px;
  border-radius: 50px;
  color: var(--bg-dark);
  font-weight: 600;
  cursor: pointer;
}

/* Topic card */
.topic-card { background: white; border-radius: 13px; padding: 20px; box-shadow: 5px 5px 6px rgba(0,0,0,0.25); margin-bottom: 20px; }

/* Footer */
.site-footer { border-radius: 20px 20px 0 0; }

/* Table */
.table-wrapper { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
table th, table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.10); }
table th { font-weight: 700; color: var(--accent-light); font-size: 14px; text-transform: uppercase; }
table td { color: white; }

/* Dashboard */
.dashboard-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
.stat-box { background: var(--bg-card); border-radius: var(--radius-sm); border: 1px solid var(--accent-pink); padding: 30px; text-align: center; }
.stat-box h3 { font-size: 14px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 10px; }
.stat-box .number { font-family: var(--font-heading); font-size: 48px; color: var(--accent-light); }

/* Admin */
.admin-body { background: #1a0a12; }
.admin-wrapper { display: flex; min-height: 100vh; }
.admin-sidebar { width: 250px; background: var(--bg-card); padding: 30px; position: fixed; height: 100vh; overflow-y: auto; }
.admin-sidebar h2 { font-family: var(--font-heading); font-size: 24px; margin-bottom: 30px; }
.sidebar-nav a {
  display: block;
  padding: 10px 0;
  color: white;
  font-size: 15px;
  font-weight: 300;
}
.sidebar-nav a:hover { color: var(--accent-light); text-decoration: none; }
.sidebar-nav hr { border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 15px 0; }
.admin-main {
  margin-left: 250px;
  padding: 40px;
  width: 100%;
}
.admin-main h1 { font-family: var(--font-heading); font-size: 42px; margin-bottom: 30px; }

/* Admin actions */
.action-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
.btn-sm { padding: 8px 16px; font-size: 13px; }
.btn-danger { background: #d32f2f; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; font-size: 13px; }
.btn-warning { background: #f57c00; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; font-size: 13px; }
.btn-success { background: #388e3c; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; font-size: 13px; }
```

- [ ] **Step 2: public/assets/css/responsive.css**

```css
/* Tablet */
@media (max-width: 1024px) {
  .blog-grid { grid-template-columns: repeat(2, 1fr); }
  .hero-title { font-size: 64px; }
  .section-title { font-size: 64px; }
  .stats-bar { gap: 40px; }
  .footer-inner { flex-direction: column; gap: 30px; }
}

/* Mobile */
@media (max-width: 768px) {
  .main-nav { display: none; }
  .main-nav.open { display: flex; flex-direction: column; position: absolute; top: 80px; left: 0; right: 0; background: var(--bg-dark); padding: 20px; }
  .hamburger { display: block; }
  .hero-title { font-size: 48px; }
  .hero-section { padding: 40px 20px; }
  .hero-cta { flex-direction: column; }
  .hero-features { flex-direction: column; align-items: center; }
  .section-title { font-size: 48px; }
  .section-subtitle { font-size: 24px; }
  .blog-grid { grid-template-columns: 1fr; }
  .stats-bar { gap: 20px; }
  .auth-container { padding: 40px 20px; }
  .admin-sidebar { width: 200px; }
  .admin-main { margin-left: 200px; padding: 20px; }
  .admin-wrapper { flex-direction: column; }
  .admin-sidebar { position: relative; width: 100%; height: auto; }
  .admin-main { margin-left: 0; }
}

/* Small mobile */
@media (max-width: 480px) {
  .hero-title { font-size: 36px; }
  .section-title { font-size: 36px; }
  .card { padding: 25px; }
  .card h3 { font-size: 20px; }
  .card p { font-size: 16px; }
  .filter-pills { gap: 5px; }
  .filter-pill { font-size: 13px; padding: 6px 10px; }
}
```

- [ ] **Step 3: public/assets/css/admin.css**

```css
/* Admin form */
.admin-form { max-width: 800px; }
.admin-form .form-group { margin-bottom: 25px; }
.admin-form label { display: block; color: white; margin-bottom: 8px; font-weight: 600; }
.admin-form input[type="text"],
.admin-form input[type="email"],
.admin-form textarea,
.admin-form select { width: 100%; padding: 12px 16px; background: rgba(255,255,255,0.10); border: 1px solid var(--accent-pink); border-radius: 10px; color: white; font-size: 15px; }
.admin-form select option { background: var(--bg-dark); }
.admin-form .help-text { color: var(--text-muted); font-size: 13px; margin-top: 5px; }

.status-badge { display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; }
.status-badge.published { background: rgba(0, 200, 83, 0.2); color: #00C853; }
.status-badge.draft { background: rgba(255, 152, 0, 0.2); color: #FF9800; }
.status-badge.pending { background: rgba(255, 152, 0, 0.2); color: #FF9800; }
.status-badge.approved { background: rgba(0, 200, 83, 0.2); color: #00C853; }
.status-badge.rejected { background: rgba(255, 0, 0, 0.2); color: #FF6B6B; }

/* Pagination */
.pagination { display: flex; gap: 10px; margin-top: 30px; justify-content: center; }
.pagination a, .pagination span {
  padding: 8px 16px;
  background: var(--glass-bg);
  border-radius: 5px;
  color: white;
}
.pagination .active { background: var(--accent-light); color: var(--bg-dark); }
```

---

### Task 7: Home Page (Dynamic)

**Files:**
- Modify: nothing yet — keep existing home HTML in views
- Create: `app/Controllers/PageController.php`
- Create: `app/Models/Article.php` (or use repository directly)

- [ ] **Step 1: PageController.php**

```php
<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Database;

class PageController {
    public function home() {
        $db = Database::getInstance();
        $featured_articles = $db->fetchAll("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.status = 'published' AND a.featured = 1 ORDER BY a.created_at DESC LIMIT 3");
        $testimonials = $db->fetchAll("SELECT t.*, u.name as user_name FROM testimonials t LEFT JOIN users u ON t.user_id = u.id WHERE t.status = 'approved' ORDER BY t.created_at DESC LIMIT 3");
        $stats = [
            'mamans' => $db->fetch("SELECT COUNT(*) as count FROM users")['count'],
            'articles' => $db->fetch("SELECT COUNT(*) as count FROM articles WHERE status='published'")['count'],
            'experts' => 200,
            'satisfaction' => '98%'
        ];
        View::render('pages/home', compact('featured_articles', 'testimonials', 'stats'), 'front');
    }
}
```

- [ ] **Step 2: views/pages/home.php**

Extract the exact HTML from the existing `index.php` and replace static content with PHP variables. Add the hero section, features cards (3 univers), testimonials, stats, blog section.

```php
<!-- Hero -->
<section class="hero-section">
  <div class="hero-content">
    <h1 class="hero-title">
      <span class="highlight">LUMA</span>, la où<br>commence le soin
    </h1>
    <p class="hero-subtitle">Une plateforme dediee pour accompagner chaque maman et chaque bebe avec douceur, confiance et expertise</p>
    <div class="hero-cta">
      <a href="/auth/register" class="btn-primary">Creer un compte</a>
      <a href="/blog" class="btn-outline">Decouvrir LUMA</a>
    </div>
    <div class="hero-features">
      <div class="hero-feature">
        <img src="/assets/images/community-icon.svg" alt="Communaute" height="40">
        <span>Communaute bienveillante</span>
      </div>
      <div class="hero-feature">
        <img src="/assets/images/expert-icon.svg" alt="Expert" height="40">
        <span>Conseils d'experts certifies</span>
      </div>
      <div class="hero-feature">
        <img src="/assets/images/personalized-icon.svg" alt="Personnalise" height="40">
        <span>Accompagnement personnalise</span>
      </div>
    </div>
  </div>
</section>

<!-- Services -->
<section class="section">
  <div class="section-inner">
    <h2 class="section-title">Trois univers pour vous accompagner</h2>
    <p class="section-subtitle">Des espaces dedies a chaque etape de votre parcours</p>
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:30px;">
      <div class="card">
        <img src="/assets/images/maman-icon.svg" alt="Maman" height="60">
        <h3>Espace Maman</h3>
        <p>Conseils bien-etre, sante et accompagnement post-partum</p>
      </div>
      <div class="card">
        <img src="/assets/images/bebe-icon.svg" alt="Bebe" height="60">
        <h3>Espace Bebe</h3>
        <p>Suivi de la croissance et soins quotidiens du bebe</p>
      </div>
      <div class="card">
        <img src="/assets/images/ressources-icon.svg" alt="Ressources" height="60">
        <h3>Ressources</h3>
        <p>Articles, guides et contenus educatifs fiables</p>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials -->
<section class="section" style="background:rgba(255,255,255,0.02);">
  <div class="section-inner">
    <h2 class="section-title">Elles nous font confiance</h2>
    <p class="section-subtitle">Grace a Luma, je me sens accompagnee a chaque etape.</p>
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:30px;">
      <?php foreach ($testimonials as $t): ?>
      <div class="testimonial-card">
        <div class="stars"><?= str_repeat('⭐', $t['rating']) ?></div>
        <p style="color:white; font-size:14px; margin:15px 0;"><?= htmlspecialchars($t['content']) ?></p>
        <p style="color:var(--accent-light); font-weight:600;">— <?= htmlspecialchars($t['user_name']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Stats -->
<section class="section">
  <div class="section-inner">
    <div class="stats-bar">
      <div class="stat-item">
        <img src="/assets/images/stats-mamans.svg" alt="" height="64">
        <div class="stat-number"><?= number_format($stats['mamans']) ?>+</div>
        <div class="stat-label">Mamans accompagnees</div>
      </div>
      <div class="stat-item">
        <img src="/assets/images/stats-articles.svg" alt="" height="64">
        <div class="stat-number"><?= number_format($stats['articles']) ?>+</div>
        <div class="stat-label">Articles et guides</div>
      </div>
      <div class="stat-item">
        <img src="/assets/images/stats-experts.svg" alt="" height="64">
        <div class="stat-number"><?= $stats['experts'] ?>+</div>
        <div class="stat-label">Experts partenaires</div>
      </div>
      <div class="stat-item">
        <img src="/assets/images/stats-satisfaction.svg" alt="" height="64">
        <div class="stat-number"><?= $stats['satisfaction'] ?></div>
        <div class="stat-label">De mamans satisfaites</div>
      </div>
    </div>
  </div>
</section>

<!-- Blog preview -->
<section class="section">
  <div class="section-inner">
    <h2 class="section-title">Nos Blogs</h2>
    <div class="blog-grid">
      <?php foreach ($featured_articles as $article): ?>
      <div class="blog-card">
        <?php if ($article['image']): ?>
          <img src="<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
        <?php endif; ?>
        <div class="blog-card-body">
          <?php if ($article['category_name']): ?>
            <span class="category-tag" style="color:white;"><?= htmlspecialchars($article['category_name']) ?></span>
          <?php endif; ?>
          <h3><?= htmlspecialchars($article['title']) ?></h3>
          <div class="meta"><?= date('d M Y', strtotime($article['created_at'])) ?> . 7 min</div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
```

---

### Task 8: Blog Module (Frontend + Admin CRUD)

**Files:**
- Create: `app/Controllers/ArticleController.php`
- Create: `app/Controllers/AdminArticleController.php`
- Create: `app/Repositories/ArticleRepository.php`
- Create: `views/pages/blog.php`
- Create: `views/pages/blog-single.php`
- Create: `views/admin/articles/index.php`
- Create: `views/admin/articles/form.php`

- [ ] **Step 1: ArticleRepository.php**

```php
<?php
namespace App\Repositories;

use App\Core\Database;

class ArticleRepository {
    private $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function findAllPublished($limit = 12, $offset = 0, $categoryId = null) {
        $sql = "SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.status = 'published'";
        $params = [];
        if ($categoryId) { $sql .= " AND a.category_id = ?"; $params[] = $categoryId; }
        $sql .= " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit; $params[] = $offset;
        return $this->db->fetchAll($sql, $params);
    }

    public function countPublished($categoryId = null) {
        $sql = "SELECT COUNT(*) as count FROM articles WHERE status = 'published'";
        $params = [];
        if ($categoryId) { $sql .= " AND category_id = ?"; $params[] = $categoryId; }
        return $this->db->fetch($sql, $params)['count'];
    }

    public function findBySlug($slug) {
        return $this->db->fetch("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.slug = ?", [$slug]);
    }

    public function findById($id) {
        return $this->db->fetch("SELECT * FROM articles WHERE id = ?", [$id]);
    }

    public function create($data) {
        return $this->db->insert(
            "INSERT INTO articles (category_id, user_id, title, slug, content, image, status, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$data['category_id'], $data['user_id'], $data['title'], $data['slug'], $data['content'], $data['image'] ?? null, $data['status'] ?? 'draft', $data['featured'] ?? 0]
        );
    }

    public function update($id, $data) {
        $this->db->query(
            "UPDATE articles SET category_id=?, title=?, slug=?, content=?, image=?, status=?, featured=? WHERE id=?",
            [$data['category_id'], $data['title'], $data['slug'], $data['content'], $data['image'] ?? null, $data['status'] ?? 'draft', $data['featured'] ?? 0, $id]
        );
    }

    public function delete($id) {
        $this->db->query("DELETE FROM articles WHERE id = ?", [$id]);
    }

    public function findAll($limit = 20, $offset = 0) {
        return $this->db->fetchAll("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.created_at DESC LIMIT ? OFFSET ?", [$limit, $offset]);
    }

    public function countAll() {
        return $this->db->fetch("SELECT COUNT(*) as count FROM articles")['count'];
    }

    public function generateSlug($title) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        return $slug;
    }
}
```

- [ ] **Step 2: ArticleController.php (frontend)**

```php
<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Repositories\ArticleRepository;
use App\Repositories\UserRepository;

class ArticleController {
    private $articleRepo;
    public function __construct() { $this->articleRepo = new ArticleRepository(); }

    public function index() {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $category = $_GET['category'] ?? null;
        $limit = 9;
        $offset = ($page - 1) * $limit;
        $articles = $this->articleRepo->findAllPublished($limit, $offset, $category);
        $total = $this->articleRepo->countPublished($category);

        $db = Database::getInstance();
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");

        View::render('pages/blog', compact('articles', 'categories', 'page', 'total', 'limit'), 'front');
    }

    public function show($slug) {
        $article = $this->articleRepo->findBySlug($slug);
        if (!$article) { View::render('errors/404', [], 'front'); return; }

        $db = Database::getInstance();
        $comments = $db->fetchAll(
            "SELECT c.*, u.name as user_name FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.article_id = ? AND c.status = 'approved' ORDER BY c.created_at DESC",
            [$article['id']]
        );
        $popular = $db->fetchAll("SELECT id, title, slug, created_at FROM articles WHERE status='published' AND id != ? ORDER BY created_at DESC LIMIT 4", [$article['id']]);

        View::render('pages/blog-single', compact('article', 'comments', 'popular'), 'front');
    }

    public function comment($slug) {
        if (!Session::has('user_id')) { Request::redirect('/auth/login'); return; }
        $article = $this->articleRepo->findBySlug($slug);
        if (!$article) { Request::back(); }

        $db = Database::getInstance();
        $db->insert(
            "INSERT INTO comments (article_id, user_id, content, status) VALUES (?, ?, ?, 'pending')",
            [$article['id'], Session::get('user_id'), Request::post('content')]
        );
        Session::setFlash('success', 'Votre commentaire a ete soumis et sera visible apres moderation.');
        Request::back();
    }
}
```

- [ ] **Step 3: views/pages/blog.php**

Blog listing with category filters from the existing blog.php UI:

```php
<section class="section">
  <div class="section-inner">
    <h1 class="section-title" style="text-align:left; font-size:48px;">
      Le blog <span style="color:var(--accent-light);">LUMA</span>
    </h1>
    <p class="section-subtitle" style="text-align:left; font-size:22px; color:white; font-weight:300;">
      Des conseils, des experiences et du soutien a chaque etape
    </p>

    <!-- Filters -->
    <div class="filter-pills" style="margin:40px 0;">
      <a href="/blog" class="filter-pill <?= !$category ? 'active' : '' ?>">Tous les articles</a>
      <?php foreach ($categories as $cat): ?>
        <a href="/blog?category=<?= $cat['id'] ?>" class="filter-pill <?= ($category == $cat['id']) ? 'active' : '' ?>"><?= htmlspecialchars($cat['name']) ?></a>
      <?php endforeach; ?>
    </div>

    <!-- Articles grid -->
    <?php if (empty($articles)): ?>
      <p style="color:var(--text-muted); text-align:center; padding:60px 0;">Aucun article pour le moment.</p>
    <?php else: ?>
    <div class="blog-grid">
      <?php foreach ($articles as $article): ?>
      <a href="/blog/<?= htmlspecialchars($article['slug']) ?>" style="text-decoration:none;">
        <div class="blog-card">
          <?php if ($article['image']): ?>
            <img src="<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
          <?php endif; ?>
          <div class="blog-card-body">
            <?php if ($article['category_name']): ?>
              <span class="category-tag" style="color:white;"><?= htmlspecialchars($article['category_name']) ?></span>
            <?php endif; ?>
            <h3><?= htmlspecialchars($article['title']) ?></h3>
            <div class="meta"><?= date('d M Y', strtotime($article['created_at'])) ?> . 7 min</div>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php $totalPages = ceil($total / $limit); if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="/blog?page=<?= $i ?><?= $category ? '&category='.$category : '' ?>" class="<?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
```

- [ ] **Step 4: views/pages/blog-single.php**

Full article view with comments from the existing blog page design:

```php
<section class="section">
  <div class="section-inner" style="display:grid; grid-template-columns: 2fr 1fr; gap:60px;">
    <div>
      <?php if ($article['image']): ?>
        <img src="<?= htmlspecialchars($article['image']) ?>" alt="" style="width:100%; border-radius:15px; margin-bottom:30px;">
      <?php endif; ?>
      <h1 style="font-family:var(--font-heading); font-size:48px; margin-bottom:15px;"><?= htmlspecialchars($article['title']) ?></h1>
      <div style="color:var(--accent-light); margin-bottom:30px;"><?= date('d M Y', strtotime($article['created_at'])) ?> . Par <?= htmlspecialchars($article['category_name'] ?? 'LUMA') ?></div>
      <div style="font-size:18px; line-height:1.8; color:var(--text-white);">
        <?= nl2br(htmlspecialchars($article['content'])) ?>
      </div>

      <!-- Comments -->
      <h2 style="font-family:var(--font-heading); font-size:32px; margin:60px 0 30px;">Commentaires</h2>
      <?php if (empty($comments)): ?>
        <p style="color:var(--text-muted);">Soyez la premiere a commenter !</p>
      <?php else: ?>
        <?php foreach ($comments as $comment): ?>
        <div class="testimonial-card" style="margin-bottom:15px;">
          <strong style="color:var(--accent-light);"><?= htmlspecialchars($comment['user_name'] ?? 'Anonyme') ?></strong>
          <span style="color:var(--text-muted); font-size:13px;"> - <?= date('d M Y', strtotime($comment['created_at'])) ?></span>
          <p style="margin-top:10px;"><?= htmlspecialchars($comment['content']) ?></p>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if (\App\Core\Session::has('user_id')): ?>
      <form method="POST" action="/blog/<?= htmlspecialchars($article['slug']) ?>/comment" style="margin-top:30px;">
        <div class="form-group">
          <textarea name="content" placeholder="Votre commentaire..." required></textarea>
        </div>
        <button type="submit" class="btn-primary">Publier</button>
      </form>
      <?php else: ?>
        <p style="margin-top:30px; color:var(--text-muted);"><a href="/auth/login">Connectez-vous</a> pour laisser un commentaire.</p>
      <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div>
      <h3 style="font-size:32px; color:white; margin-bottom:20px; font-family:var(--font-body);">Autres populaires</h3>
      <div style="width:60px; height:5px; background:#F5C0AF; margin-bottom:30px;"></div>
      <?php foreach ($popular as $p): ?>
      <a href="/blog/<?= htmlspecialchars($p['slug']) ?>" style="display:block; margin-bottom:20px; color:white; text-decoration:none;">
        <div style="display:flex; gap:15px; align-items:center;">
          <div style="width:80px; height:80px; background:var(--glass-bg); border-radius:20px; flex-shrink:0;"></div>
          <div>
            <p style="font-size:16px;"><?= htmlspecialchars($p['title']) ?></p>
            <span style="color:var(--accent-light); font-size:14px;"><?= date('d M Y', strtotime($p['created_at'])) ?></span>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
```

- [ ] **Step 5: AdminArticleController.php**

```php
<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Repositories\ArticleRepository;
use App\Core\Database;

class AdminArticleController {
    private $articleRepo;
    public function __construct() { $this->articleRepo = new ArticleRepository(); \App\Middleware\AdminMiddleware::check(); }

    public function index() {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $articles = $this->articleRepo->findAll($limit, $offset);
        $total = $this->articleRepo->countAll();
        View::render('admin/articles/index', compact('articles', 'page', 'total', 'limit'), 'admin');
    }

    public function create() {
        $db = Database::getInstance();
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
        View::render('admin/articles/form', compact('categories'), 'admin');
    }

    public function store() {
        $slug = $this->articleRepo->generateSlug(Request::post('title'));
        // Handle image upload
        $image = null;
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../../public/assets/uploads/' . $filename);
            $image = '/assets/uploads/' . $filename;
        }

        $this->articleRepo->create([
            'category_id' => Request::post('category_id'),
            'user_id' => Session::get('user_id'),
            'title' => Request::post('title'),
            'slug' => $slug,
            'content' => Request::post('content'),
            'image' => $image,
            'status' => Request::post('status', 'draft'),
            'featured' => Request::post('featured', 0),
        ]);
        Session::setFlash('success', 'Article cree avec succes.');
        Request::redirect('/admin/articles');
    }

    public function edit($id) {
        $article = $this->articleRepo->findById($id);
        $db = Database::getInstance();
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
        View::render('admin/articles/form', compact('article', 'categories'), 'admin');
    }

    public function update($id) {
        $slug = $this->articleRepo->generateSlug(Request::post('title'));
        $image = Request::post('existing_image');
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../../public/assets/uploads/' . $filename);
            $image = '/assets/uploads/' . $filename;
        }
        $this->articleRepo->update($id, [
            'category_id' => Request::post('category_id'),
            'title' => Request::post('title'),
            'slug' => $slug,
            'content' => Request::post('content'),
            'image' => $image,
            'status' => Request::post('status', 'draft'),
            'featured' => Request::post('featured', 0),
        ]);
        Session::setFlash('success', 'Article mis a jour.');
        Request::redirect('/admin/articles');
    }

    public function destroy($id) {
        $this->articleRepo->delete($id);
        Session::setFlash('success', 'Article supprime.');
        Request::redirect('/admin/articles');
    }
}
```

- [ ] **Step 6: views/admin/articles/index.php**

```php
<h1>Articles</h1>
<div class="action-bar">
  <a href="/admin/articles/create" class="btn-primary btn-sm">+ Nouvel article</a>
</div>
<div class="table-wrapper">
<table>
  <thead>
    <tr><th>Titre</th><th>Categorie</th><th>Statut</th><th>A la une</th><th>Date</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($articles as $a): ?>
    <tr>
      <td><?= htmlspecialchars($a['title']) ?></td>
      <td><?= htmlspecialchars($a['category_name'] ?? '-') ?></td>
      <td><span class="status-badge <?= $a['status'] ?>"><?= $a['status'] ?></span></td>
      <td><?= $a['featured'] ? '⭐' : '-' ?></td>
      <td><?= date('d/m/Y', strtotime($a['created_at'])) ?></td>
      <td>
        <a href="/admin/articles/edit/<?= $a['id'] ?>" class="btn-warning btn-sm" style="color:white;">Modifier</a>
        <form method="POST" action="/admin/articles/delete/<?= $a['id'] ?>" style="display:inline;" onsubmit="return confirm('Supprimer?')">
          <button type="submit" class="btn-danger btn-sm">Supprimer</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
```

- [ ] **Step 7: views/admin/articles/form.php**

```php
<h1><?= isset($article) ? 'Modifier' : 'Creer' ?> un article</h1>
<form method="POST" enctype="multipart/form-data" class="admin-form">
  <div class="form-group">
    <label>Titre</label>
    <input type="text" name="title" value="<?= htmlspecialchars($article['title'] ?? '') ?>" required>
  </div>
  <div class="form-group">
    <label>Categorie</label>
    <select name="category_id">
      <option value="">Sans categorie</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>" <?= (isset($article) && $article['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group">
    <label>Contenu</label>
    <textarea name="content" rows="15" required><?= htmlspecialchars($article['content'] ?? '') ?></textarea>
  </div>
  <div class="form-group">
    <label>Image</label>
    <input type="file" name="image" accept="image/*">
    <?php if (isset($article) && $article['image']): ?>
      <div class="help-text">Image actuelle: <?= $article['image'] ?></div>
      <input type="hidden" name="existing_image" value="<?= $article['image'] ?>">
    <?php endif; ?>
  </div>
  <div class="form-group">
    <label>Statut</label>
    <select name="status">
      <option value="draft" <?= (isset($article) && $article['status'] == 'draft') ? 'selected' : '' ?>>Brouillon</option>
      <option value="published" <?= (isset($article) && $article['status'] == 'published') ? 'selected' : '' ?>>Publie</option>
    </select>
  </div>
  <div class="form-group">
    <label>
      <input type="checkbox" name="featured" value="1" <?= (isset($article) && $article['featured']) ? 'checked' : '' ?>>
      Mettre a la une
    </label>
  </div>
  <button type="submit" class="btn-primary"><?= isset($article) ? 'Mettre a jour' : 'Creer' ?></button>
</form>
```

---

### Task 9: Contact Module

**Files:**
- Create: `app/Controllers/ContactController.php`
- Create: `app/Controllers/AdminContactController.php`
- Create: `views/pages/contact.php`
- Create: `views/admin/contacts/index.php`

- [ ] **Step 1: ContactController.php**

```php
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
        Session::setFlash('success', 'Message envoye avec succes ! Nous vous repondrons dans les plus brefs delais.');
        Request::back();
    }
}
```

- [ ] **Step 2: views/pages/contact.php**

Extract the existing contact.php UI form:

```php
<section class="section">
  <div class="section-inner">
    <div style="display:grid; grid-template-columns: 1fr 1.5fr; gap:60px;">
      <div>
        <h1 style="font-family:var(--font-heading); font-size:64px; margin-bottom:10px;">
          Nous sommes la pour <span style="color:var(--accent-pink);">vous</span>
        </h1>
        <p style="font-size:22px; color:var(--text-white); margin-bottom:40px;">
          Une question, un besoin d'accompagnement ou une suggestion ? L'equipe Luma est a votre ecoute avec bienveillance.
        </p>
        
        <div style="display:flex; flex-direction:column; gap:30px;">
          <div style="display:flex; align-items:center; gap:15px;">
            <div style="width:64px; height:64px; background:var(--accent-pink); border-radius:50%; display:flex; align-items:center; justify-content:center;">
              <span style="font-size:28px;">💬</span>
            </div>
            <div>
              <h3 style="font-size:20px; font-weight:700;">Chat en direct</h3>
              <p style="color:var(--text-muted);">Lun-Ven 9h-18h</p>
            </div>
          </div>
          <div style="display:flex; align-items:center; gap:15px;">
            <div style="width:64px; height:64px; background:var(--accent-pink); border-radius:50%; display:flex; align-items:center; justify-content:center;">
              <span style="font-size:28px;">✉️</span>
            </div>
            <div>
              <h3 style="font-size:20px; font-weight:700;">Par e-mail</h3>
              <p style="color:var(--text-muted);">hello@luma.tn</p>
            </div>
          </div>
          <div style="display:flex; align-items:center; gap:15px;">
            <div style="width:64px; height:64px; background:var(--accent-pink); border-radius:50%; display:flex; align-items:center; justify-content:center;">
              <span style="font-size:28px;">📞</span>
            </div>
            <div>
              <h3 style="font-size:20px; font-weight:700;">Par telephone</h3>
              <p style="color:var(--text-muted);">+216 97 203 908</p>
            </div>
          </div>
          <div style="display:flex; align-items:center; gap:15px;">
            <div style="width:64px; height:64px; background:var(--accent-pink); border-radius:50%; display:flex; align-items:center; justify-content:center;">
              <span style="font-size:28px;">📍</span>
            </div>
            <div>
              <h3 style="font-size:20px; font-weight:700;">Notre siege</h3>
              <p style="color:var(--text-muted);">Tunis, Tunisie</p>
            </div>
          </div>
        </div>
      </div>

      <div>
        <h2 style="font-family:var(--font-heading); font-size:36px; margin-bottom:10px;">Envoyez-nous un message</h2>
        <p style="color:var(--text-muted); margin-bottom:30px;">Remplissez le formulaire ci-dessous, nous vous repondrons avec soin et bienveillance.</p>

        <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
          <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <form method="POST" action="/contact" class="auth-form">
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="form-group">
              <input type="text" name="name" placeholder="Nom" required>
            </div>
            <div class="form-group">
              <input type="text" name="subject" placeholder="Prenom" required>
            </div>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="form-group">
              <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
              <input type="tel" name="phone" placeholder="Numero de Telephone">
            </div>
          </div>
          <div class="form-group">
            <textarea name="message" placeholder="Votre Message" required></textarea>
          </div>
          <button type="submit" class="btn-primary" style="width:100%;">Envoyer un message</button>
        </form>
      </div>
    </div>
  </div>
</section>
```

- [ ] **Step 3: AdminContactController.php**

```php
<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;

class AdminContactController {
    public function __construct() { \App\Middleware\AdminMiddleware::check(); }

    public function index() {
        $db = Database::getInstance();
        $contacts = $db->fetchAll("SELECT * FROM contacts ORDER BY created_at DESC");
        View::render('admin/contacts/index', compact('contacts'), 'admin');
    }

    public function markRead($id) {
        $db = Database::getInstance();
        $db->query("UPDATE contacts SET is_read = 1 WHERE id = ?", [$id]);
        Request::back();
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $db->query("DELETE FROM contacts WHERE id = ?", [$id]);
        Session::setFlash('success', 'Message supprime.');
        Request::redirect('/admin/contacts');
    }
}
```

---

### Task 10: FAQ Module

**Files:**
- Create: `app/Controllers/FaqController.php`
- Create: `app/Controllers/AdminFaqController.php`
- Create: `views/pages/faq.php`
- Create: `views/admin/faqs/index.php`

- [ ] **Step 1: FaqController.php**

```php
<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Database;

class FaqController {
    public function index() {
        $db = Database::getInstance();
        $faqs = $db->fetchAll("SELECT * FROM faqs ORDER BY display_order ASC");
        $grouped = [];
        foreach ($faqs as $faq) {
            $cat = $faq['category'] ?? 'General';
            $grouped[$cat][] = $faq;
        }
        View::render('pages/faq', compact('grouped'), 'front');
    }
}
```

- [ ] **Step 2: views/pages/faq.php**

```php
<section class="section">
  <div class="section-inner" style="max-width:900px;">
    <h1 class="section-title">FAQ</h1>
    <p class="section-subtitle">Vos questions les plus frequentes</p>
    
    <?php foreach ($grouped as $category => $faqs): ?>
      <h2 style="font-family:var(--font-heading); font-size:32px; color:var(--accent-light); margin:40px 0 20px;"><?= htmlspecialchars($category) ?></h2>
      <?php foreach ($faqs as $faq): ?>
      <details style="background:var(--bg-card); border:1px solid var(--accent-pink); border-radius:10px; margin-bottom:15px; padding:20px;">
        <summary style="font-size:18px; font-weight:600; cursor:pointer; color:white;"><?= htmlspecialchars($faq['question']) ?></summary>
        <p style="margin-top:15px; color:var(--text-muted); font-size:16px; line-height:1.6;"><?= nl2br(htmlspecialchars($faq['answer'])) ?></p>
      </details>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </div>
</section>
```

---

### Task 11: Community/Forum Module

**Files:**
- Create: `app/Controllers/CommunityController.php`
- Create: `app/Controllers/AdminForumController.php`
- Create: `views/pages/community.php`
- Create: `views/pages/community-topic.php`
- Create: `views/admin/forum/index.php`

- [ ] **Step 1: CommunityController.php**

```php
<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;

class CommunityController {
    public function index() {
        $db = Database::getInstance();
        $topics = $db->fetchAll(
            "SELECT t.*, u.name as user_name, (SELECT COUNT(*) FROM forum_replies WHERE topic_id = t.id) as reply_count FROM forum_topics t LEFT JOIN users u ON t.user_id = u.id WHERE t.status = 'open' ORDER BY t.created_at DESC"
        );
        View::render('pages/community', compact('topics'), 'front');
    }

    public function topic($id) {
        $db = Database::getInstance();
        $topic = $db->fetch("SELECT t.*, u.name as user_name FROM forum_topics t LEFT JOIN users u ON t.user_id = u.id WHERE t.id = ?", [$id]);
        if (!$topic) { View::render('errors/404', [], 'front'); return; }
        $replies = $db->fetchAll(
            "SELECT r.*, u.name as user_name FROM forum_replies r LEFT JOIN users u ON r.user_id = u.id WHERE r.topic_id = ? ORDER BY r.created_at ASC",
            [$id]
        );
        View::render('pages/community-topic', compact('topic', 'replies'), 'front');
    }

    public function reply($id) {
        if (!Session::has('user_id')) { Request::redirect('/auth/login'); return; }
        $db = Database::getInstance();
        $db->insert(
            "INSERT INTO forum_replies (topic_id, user_id, content) VALUES (?, ?, ?)",
            [$id, Session::get('user_id'), Request::post('content')]
        );
        Session::setFlash('success', 'Reponse ajoutee.');
        Request::back();
    }
}
```

- [ ] **Step 2: views/pages/community.php**

```php
<section class="section">
  <div class="section-inner">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:40px;">
      <div>
        <h1 style="font-family:var(--font-heading); font-size:64px;">
          Une <span style="color:var(--accent-light);">communaute</span>
        </h1>
        <p style="font-size:32px; font-family:var(--font-heading);">de mamans bienveillantes</p>
        <p style="color:var(--text-muted); max-width:600px; font-size:18px; margin-top:20px;">
          Echangez, partagez et trouvez du soutien aupres d'autres mamans qui vivent les memes experiences que vous.
        </p>
        <div style="display:flex; gap:20px; margin-top:30px;">
          <a href="/auth/register" class="btn-primary">Rejoindre la communaute</a>
          <div style="padding-left:20px; border-left:1px solid white;">
            <span style="font-family:var(--font-heading); font-size:36px;">50 000+</span><br>
            <span style="font-size:16px;">mamans deja connectees</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Search bar -->
    <div class="search-bar">
      <input type="text" placeholder="Rechercher un sujet, une question, un conseil...">
      <a href="/auth/register" class="btn-primary" style="color:var(--bg-dark);">Poser une question +</a>
    </div>

    <!-- Topics -->
    <?php foreach ($topics as $topic): ?>
    <a href="/community/<?= $topic['id'] ?>" style="text-decoration:none;">
      <div class="topic-card">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3 style="color:black; font-size:18px;"><?= htmlspecialchars($topic['title']) ?></h3>
          <span style="color:#868686; font-size:14px;"><?= $topic['reply_count'] ?> reponses</span>
        </div>
        <p style="color:#666; margin-top:10px;"><?= substr(htmlspecialchars($topic['content']), 0, 150) ?>...</p>
        <div style="margin-top:10px; color:#868686; font-size:13px;">
          Par <?= htmlspecialchars($topic['user_name']) ?> - <?= date('d M Y', strtotime($topic['created_at'])) ?>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>
```

---

### Task 12: Dashboard Membre

**Files:**
- Create: `app/Controllers/DashboardController.php`
- Create: `views/pages/dashboard/index.php`

- [ ] **Step 1: DashboardController.php**

```php
<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Request;
use App\Core\Database;
use App\Repositories\UserRepository;

class DashboardController {
    private $userRepo;
    public function __construct() { \App\Middleware\AuthMiddleware::check(); $this->userRepo = new UserRepository(); }

    public function index() {
        $db = Database::getInstance();
        $user = $this->userRepo->findById(Session::get('user_id'));
        $comments = $db->fetchAll("SELECT c.*, a.title as article_title FROM comments c LEFT JOIN articles a ON c.article_id = a.id WHERE c.user_id = ? ORDER BY c.created_at DESC", [Session::get('user_id')]);
        $topics = $db->fetchAll("SELECT * FROM forum_topics WHERE user_id = ? ORDER BY created_at DESC", [Session::get('user_id')]);
        $testimonials = $db->fetchAll("SELECT * FROM testimonials WHERE user_id = ? ORDER BY created_at DESC", [Session::get('user_id')]);

        if (Request::isPost()) {
            $name = Request::post('name');
            $email = Request::post('email');
            $this->userRepo->updateProfile(Session::get('user_id'), compact('name', 'email'));
            Session::set('user_name', $name);
            Session::setFlash('success', 'Profil mis a jour.');
            Request::back();
        }

        View::render('pages/dashboard/index', compact('user', 'comments', 'topics', 'testimonials'), 'front');
    }
}
```

- [ ] **Step 2: views/pages/dashboard/index.php**

```php
<section class="section">
  <div class="section-inner" style="display:grid; grid-template-columns: 300px 1fr; gap:40px;">
    <!-- Sidebar -->
    <div>
      <div class="card" style="border-color:var(--accent-light);">
        <div style="width:80px; height:80px; background:var(--accent-pink); border-radius:50%; margin:0 auto; display:flex; align-items:center; justify-content:center; font-size:36px; color:white;">
          <?= strtoupper(substr($user['name'], 0, 1)) ?>
        </div>
        <h3 style="color:white; margin-top:15px;"><?= htmlspecialchars($user['name']) ?></h3>
        <p style="color:var(--text-muted); font-size:14px;"><?= htmlspecialchars($user['email']) ?></p>
        <p style="color:var(--accent-light); font-size:13px; text-transform:uppercase; margin-top:10px;"><?= $user['role'] ?></p>
      </div>
      <nav style="margin-top:20px;">
        <a href="/dashboard" style="display:block; padding:10px; color:white; background:var(--glass-bg); border-radius:10px; margin-bottom:5px;">📋 Mon profil</a>
        <a href="#comments" style="display:block; padding:10px; color:white;">💬 Mes commentaires</a>
        <a href="#topics" style="display:block; padding:10px; color:white;">🗣️ Mes sujets</a>
        <a href="#testimonials" style="display:block; padding:10px; color:white;">⭐ Mes temoignages</a>
      </nav>
    </div>

    <!-- Content -->
    <div>
      <h1 style="font-family:var(--font-heading); font-size:42px; margin-bottom:30px;">Mon profil</h1>
      
      <form method="POST" class="admin-form" style="max-width:500px;">
        <div class="form-group">
          <label>Nom</label>
          <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <button type="submit" class="btn-primary">Mettre a jour</button>
      </form>

      <h2 style="font-size:28px; margin:60px 0 20px; font-family:var(--font-heading);">Mes commentaires</h2>
      <?php if (empty($comments)): ?><p style="color:var(--text-muted);">Aucun commentaire.</p>
      <?php else: ?>
        <?php foreach ($comments as $c): ?>
          <div class="testimonial-card" style="margin-bottom:10px;">
            <p style="font-size:14px;"><?= htmlspecialchars($c['content']) ?></p>
            <span style="color:var(--accent-light); font-size:12px;">sur "<?= htmlspecialchars($c['article_title']) ?>" - <?= date('d/m/Y', strtotime($c['created_at'])) ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <h2 style="font-size:28px; margin:60px 0 20px; font-family:var(--font-heading);">Mes temoignages</h2>
      <?php if (empty($testimonials)): ?>
        <p style="color:var(--text-muted);"><a href="/dashboard/testimonials/create" class="btn-primary btn-sm">Ajouter un temoignage</a></p>
      <?php else: ?>
        <?php foreach ($testimonials as $t): ?>
          <div class="testimonial-card" style="margin-bottom:10px;">
            <div class="stars"><?= str_repeat('⭐', $t['rating']) ?></div>
            <p><?= htmlspecialchars($t['content']) ?></p>
            <span class="status-badge <?= $t['status'] ?>"><?= $t['status'] ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>
```

---

### Task 13: Admin Dashboard

**Files:**
- Create: `app/Controllers/AdminController.php`
- Create: `views/admin/dashboard/index.php`

- [ ] **Step 1: AdminController.php**

```php
<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Database;

class AdminController {
    public function __construct() { \App\Middleware\AdminMiddleware::check(); }

    public function index() {
        $db = Database::getInstance();
        $stats = [
            'users' => $db->fetch("SELECT COUNT(*) as count FROM users")['count'],
            'articles' => $db->fetch("SELECT COUNT(*) as count FROM articles")['count'],
            'comments' => $db->fetch("SELECT COUNT(*) as count FROM comments WHERE status='pending'")['count'],
            'testimonials' => $db->fetch("SELECT COUNT(*) as count FROM testimonials WHERE status='pending'")['count'],
            'contacts' => $db->fetch("SELECT COUNT(*) as count FROM contacts WHERE is_read=0")['count'],
            'newsletters' => $db->fetch("SELECT COUNT(*) as count FROM newsletters WHERE is_active=1")['count'],
        ];
        View::render('admin/dashboard/index', compact('stats'), 'admin');
    }
}
```

- [ ] **Step 2: views/admin/dashboard/index.php**

```php
<h1>Tableau de bord</h1>
<p style="color:var(--text-muted); margin-bottom:30px;">Bienvenue dans l'administration LUMA</p>
<div class="dashboard-stats">
  <div class="stat-box"><h3>Utilisateurs</h3><div class="number"><?= $stats['users'] ?></div></div>
  <div class="stat-box"><h3>Articles</h3><div class="number"><?= $stats['articles'] ?></div></div>
  <div class="stat-box"><h3>Commentaires en attente</h3><div class="number"><?= $stats['comments'] ?></div></div>
  <div class="stat-box"><h3>Temoignages en attente</h3><div class="number"><?= $stats['testimonials'] ?></div></div>
  <div class="stat-box"><h3>Messages non lus</h3><div class="number"><?= $stats['contacts'] ?></div></div>
  <div class="stat-box"><h3>Newsletter actifs</h3><div class="number"><?= $stats['newsletters'] ?></div></div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
  <a href="/admin/articles" class="card" style="text-decoration:none;">
    <h3 style="color:var(--accent-light);">📝 Gerer les articles</h3>
    <p style="color:white;">Publier, modifier ou supprimer des articles</p>
  </a>
  <a href="/admin/comments" class="card" style="text-decoration:none;">
    <h3 style="color:var(--accent-light);">💬 Moderer les commentaires</h3>
    <p style="color:white;">Approuver ou rejeter les commentaires</p>
  </a>
  <a href="/admin/testimonials" class="card" style="text-decoration:none;">
    <h3 style="color:var(--accent-light);">⭐ Gerer les temoignages</h3>
    <p style="color:white;">Moderer les temoignages membres</p>
  </a>
  <a href="/admin/contacts" class="card" style="text-decoration:none;">
    <h3 style="color:var(--accent-light);">✉️ Messages recus</h3>
    <p style="color:white;">Consulter les messages de contact</p>
  </a>
</div>
```

---

### Task 14: Newsletter Module + Testimonials Submission

**Files:**
- Create: `app/Controllers/NewsletterController.php`
- Create: `app/Controllers/AdminNewsletterController.php`
- Create: `app/Controllers/AdminCategoryController.php`
- Create: `app/Controllers/AdminUserController.php`
- Create: `app/Controllers/AdminTestimonialController.php`
- Create: `views/admin/newsletters/index.php`
- Create: `views/admin/testimonials/index.php`
- Create: `views/admin/categories/index.php`
- Create: `views/admin/users/index.php`
- Create: `views/admin/comments/index.php`

(Tasks 14 continues with compact controller implementations for each remaining admin module — following the same pattern as previous tasks.)

---

### Task 15: JavaScript (Hamburger + Interactions)

**Files:**
- Create: `public/assets/js/app.js`

- [ ] **Step 1: app.js**

```javascript
// Hamburger menu
document.addEventListener('DOMContentLoaded', function() {
  const hamburger = document.getElementById('hamburger');
  const nav = document.querySelector('.main-nav');
  if (hamburger) {
    hamburger.addEventListener('click', function() {
      nav.classList.toggle('open');
    });
  }

  // FAQ accordion
  document.querySelectorAll('details summary').forEach(summary => {
    summary.addEventListener('click', function(e) {
      const details = this.parentElement;
      const isOpen = details.hasAttribute('open');
      document.querySelectorAll('details').forEach(d => d.removeAttribute('open'));
      if (!isOpen) details.setAttribute('open', '');
      e.preventDefault();
    });
  });
});
```

---

### Task 16: 404 Error Page

**Files:**
- Create: `views/errors/404.php`

- [ ] **Step 1: views/errors/404.php**

```html
<section class="section" style="text-align:center; padding:120px 20px;">
  <h1 style="font-family:var(--font-heading); font-size:120px; color:var(--accent-light);">404</h1>
  <p style="font-size:24px; color:var(--text-muted); margin-bottom:30px;">Page non trouvee</p>
  <a href="/" class="btn-primary">Retour a l'accueil</a>
</section>
```
