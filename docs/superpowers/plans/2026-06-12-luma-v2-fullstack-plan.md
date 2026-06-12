# LUMA v2 — Full-Stack Expansion (Phase 1) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Expand LUMA from 2-role/10-table app to 4-role/22-table full platform with RBAC, Bootstrap 5, and complete dashboards.

**Architecture:** Extend existing vanilla PHP MVC. Add RBAC layer (roles/permissions tables + middleware), replace custom CSS with Bootstrap 5 dark theme, add new modules (resources, community posts, tickets, notifications). All existing features preserved.

**Tech Stack:** PHP 8.3+, MySQL/MariaDB, Bootstrap 5, vanilla JS + AJAX

---

### Task 1: RBAC — Database Migration (new tables + ALTER users)

**Files:**
- Modify: `migrations/001_create_tables.sql` (replace with full 22-table schema)
- Re-run: migration on DDEV

- [ ] **Step 1: Replace migration file with complete schema**

Write the full 22-table migration (from spec) to `migrations/v2_create_tables.sql`. Includes:
- `roles`, `permissions`, `role_permissions`
- Modified `users` (role_id FK, phone, status)
- `mothers`, `babies`, `pregnancies`, `growth_records`, `vaccinations`
- Modified `articles` (tags, views_count, excerpt)
- `resources`
- `community_posts`, `community_comments`
- `tickets`, `notifications`, `settings`, `activity_logs`
- Existing tables kept: `categories`, `comments`, `testimonials`, `faqs`, `contacts`, `newsletters`
- Seed data: 4 roles, all permissions, role_permissions for each role, admin user

- [ ] **Step 2: Backup and reimport database**

```bash
ddev mysql luma < migrations/v2_create_tables.sql
```

- [ ] **Step 3: Verify all tables and seed data**

```bash
ddev mysql luma -e "SHOW TABLES;"
ddev mysql luma -e "SELECT COUNT(*) as roles FROM roles;"
ddev mysql luma -e "SELECT COUNT(*) as permissions FROM permissions;"
ddev mysql luma -e "SELECT name, email, role_id FROM users;"
```

---

### Task 2: RBAC — PermissionMiddleware + RoleMiddleware

**Files:**
- Create: `app/Middleware/PermissionMiddleware.php`
- Create: `app/Middleware/RoleMiddleware.php`
- Modify: `app/Middleware/AdminMiddleware.php` (use PermissionMiddleware internally)
- Create: `app/Repositories/PermissionRepository.php`

- [ ] **Step 1: PermissionRepository.php**

```php
<?php
namespace App\Repositories;

use App\Core\Database;

class PermissionRepository {
    private $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function hasPermission($userId, $permissionSlug) {
        return $this->db->fetch(
            "SELECT 1 FROM users u
             JOIN role_permissions rp ON u.role_id = rp.role_id
             JOIN permissions p ON rp.permission_id = p.id
             WHERE u.id = ? AND p.slug = ?",
            [$userId, $permissionSlug]
        ) !== false;
    }

    public function getUserPermissions($userId) {
        return $this->db->fetchAll(
            "SELECT p.slug, p.name FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             JOIN users u ON u.role_id = rp.role_id
             WHERE u.id = ?", [$userId]
        );
    }

    public function getRolePermissions($roleId) {
        return $this->db->fetchAll(
            "SELECT p.* FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             WHERE rp.role_id = ?", [$roleId]
        );
    }

    public function getAllPermissions() {
        return $this->db->fetchAll("SELECT * FROM permissions ORDER BY group_name, name");
    }

    public function getAllRoles() {
        return $this->db->fetchAll("SELECT * FROM roles ORDER BY name");
    }
}
```

- [ ] **Step 2: PermissionMiddleware.php**

```php
<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Request;
use App\Repositories\PermissionRepository;

class PermissionMiddleware {
    public static function check($permissionSlug) {
        if (!Session::has('user_id')) {
            Request::redirect('/auth/login');
        }
        $repo = new PermissionRepository();
        if (!$repo->hasPermission(Session::get('user_id'), $permissionSlug)) {
            http_response_code(403);
            die("Accès refusé.");
        }
    }
}
```

- [ ] **Step 3: RoleMiddleware.php**

```php
<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Request;

class RoleMiddleware {
    public static function check($roleSlug) {
        if (!Session::has('user_id') || Session::get('user_role_slug') !== $roleSlug) {
            Request::redirect('/auth/login');
        }
    }
}
```

- [ ] **Step 4: Modify AdminMiddleware.php to use PermissionMiddleware**

```php
<?php
namespace App\Middleware;

class AdminMiddleware {
    public static function check() {
        PermissionMiddleware::check('admin');
    }
}
```

- [ ] **Step 5: Store role_slug in session**

Modify `app/Controllers/AuthController.php`:
- After login, store `user_role_slug` in session (fetch role slug from DB)
- Update `Session::set('user_role_slug', ...)` after login and register

---

### Task 3: Bootstrap 5 — Asset Setup

**Files:**
- Modify: `public/assets/css/style.css` (rewrite for Bootstrap 5 dark theme)
- Modify: `public/assets/css/admin.css` (Bootstrap overrides)
- Delete: `public/assets/css/responsive.css` (Bootstrap handles this)
- Create: `public/assets/js/bootstrap.bundle.min.js` (or CDN)
- Modify: `public/assets/js/app.js`

- [ ] **Step 1: Rewrite style.css for Bootstrap 5 dark theme**

```css
/* LUMA Bootstrap 5 Dark Theme - Custom overrides */
:root {
  --luma-bg: #2E0F1C;
  --luma-card: #632538;
  --luma-pink: #C94B72;
  --luma-light: #F0A0BB;
  --luma-teal: #70A2B4;
  --luma-text: #F5F5F5;
  --luma-muted: #9FB3DF;
  --luma-glass: rgba(255, 255, 255, 0.10);
}

[data-bs-theme="dark"] {
  --bs-body-bg: #2E0F1C;
  --bs-body-color: #F5F5F5;
  --bs-primary: #F0A0BB;
  --bs-secondary: #632538;
  --bs-card-bg: #632538;
  --bs-card-border-color: #C94B72;
  --bs-border-color: #C94B72;
  --bs-navbar-bg: rgba(46, 15, 28, 0.95);
}

@font-face {
  font-family: 'Royalist';
  src: url('/assets/fonts/royalist.woff2') format('woff2');
}

.font-heading { font-family: 'Royalist', serif; }
.text-pink { color: var(--luma-pink) !important; }
.text-light-pink { color: var(--luma-light) !important; }
.text-teal { color: var(--luma-teal) !important; }
.bg-luma { background-color: var(--luma-bg) !important; }
.bg-luma-card { background-color: var(--luma-card) !important; }
.border-pink { border-color: var(--luma-pink) !important; }

.btn-luma {
  background: var(--luma-light);
  color: var(--luma-bg);
  border: none;
  border-radius: 50px;
  padding: 12px 30px;
  font-weight: 600;
}
.btn-luma:hover { opacity: 0.9; color: var(--luma-bg); }
.btn-outline-luma {
  background: transparent;
  color: white;
  border: 1px solid var(--luma-pink);
  border-radius: 50px;
  padding: 12px 30px;
}
.btn-outline-luma:hover { background: var(--luma-pink); color: white; }

.hero-title { font-family: 'Royalist', serif; font-size: 96px; line-height: 1; }
.hero-subtitle { font-size: 22px; font-weight: 300; color: var(--luma-muted); }

.section-title { font-family: 'Royalist', serif; font-size: 64px; }
.section-subtitle { font-size: 32px; color: var(--luma-teal); }

.card-luma {
  background: var(--luma-card);
  border: 1px solid var(--luma-pink);
  border-radius: 10px;
}

.card-testimonial {
  background: var(--luma-glass);
  border: 1px solid var(--luma-pink);
  border-radius: 10px;
  padding: 30px;
}

.form-control-luma {
  background: var(--luma-glass);
  border: 1px solid var(--luma-pink);
  border-radius: 50px;
  color: white;
  padding: 18px 24px;
}
.form-control-luma:focus {
  background: var(--luma-glass);
  border-color: var(--luma-light);
  color: white;
  box-shadow: 0 0 0 0.2rem rgba(240, 160, 187, 0.25);
}

.stat-number { font-size: 36px; font-weight: 600; }

.filter-pill {
  padding: 9px 12px;
  background: rgba(240, 160, 187, 0.37);
  border-radius: 50px;
  color: white;
  border: none;
  cursor: pointer;
}
.filter-pill.active, .filter-pill:hover { background: var(--luma-light); color: var(--luma-bg); }

.sidebar-nav .nav-link {
  color: white;
  padding: 10px 0;
  font-weight: 300;
}
.sidebar-nav .nav-link:hover { color: var(--luma-light); }

.status-badge { display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; }
.status-badge.published { background: rgba(0, 200, 83, 0.2); color: #00C853; }
.status-badge.draft { background: rgba(255, 152, 0, 0.2); color: #FF9800; }
.status-badge.pending { background: rgba(255, 152, 0, 0.2); color: #FF9800; }
.status-badge.approved { background: rgba(0, 200, 83, 0.2); color: #00C853; }
.status-badge.rejected { background: rgba(255, 0, 0, 0.2); color: #FF6B6B; }

.sidebar-wrapper { width: 250px; min-height: 100vh; }
```

- [ ] **Step 2: Update admin.css** (keep admin-specific overrides)

- [ ] **Step 3: Update app.js** (Bootstrap init + custom JS)

```javascript
document.addEventListener('DOMContentLoaded', function() {
  // Bootstrap tooltips
  const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  if (tooltips.length) tooltips.forEach(el => new bootstrap.Tooltip(el));

  // Confirm dialogs
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function(e) {
      if (!confirm(this.dataset.confirm)) e.preventDefault();
    });
  });

  // Auto-dismiss alerts
  document.querySelectorAll('.alert-dismissible').forEach(el => {
    setTimeout(() => { el.remove(); }, 5000);
  });
});
```

---

### Task 4: Bootstrap 5 — Rewrite Layouts

**Files:**
- Modify: `views/layouts/front.php` (Bootstrap 5 + dark theme)
- Modify: `views/layouts/admin.php` (Bootstrap 5)
- Create: `views/layouts/maman.php`
- Create: `views/layouts/expert.php`
- Create: `views/layouts/ctt.php`

- [ ] **Step 1: Rewrite front.php layout**

```php
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'LUMA - Là où commence le soin' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg sticky-top" style="background: rgba(46,15,28,0.95); backdrop-filter: blur(10px);">
    <div class="container">
      <a class="navbar-brand fw-bold" href="/">
        <span class="text-light-pink font-heading" style="font-size:28px;">LUMA</span>
        <small class="text-white d-none d-md-inline">, là où commence le soin</small>
      </a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
          <li class="nav-item"><a class="nav-link text-white" href="/">Accueil</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="/blog">Blog</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="/ressources">Ressources</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="/communaute">Communauté</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="/faq">FAQ</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="/contact">Contact</a></li>
          <?php if (\App\Core\Session::has('user_id')): ?>
            <li class="nav-item"><a class="nav-link text-light-pink" href="/dashboard">Mon compte</a></li>
            <li class="nav-item"><a class="btn btn-outline-light btn-sm rounded-pill px-3" href="/auth/logout">Déconnexion</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="btn btn-outline-light btn-sm rounded-pill px-3" href="/auth/login">Connexion</a></li>
            <li class="nav-item"><a class="btn btn-luma btn-sm rounded-pill px-3" href="/auth/register">S'inscrire</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <main>
    <?= $content ?>
  </main>

  <footer class="bg-luma-card text-white py-5" style="border-radius: 20px 20px 0 0;">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="font-heading text-light-pink" style="font-size:32px;">LUMA</div>
          <p class="fw-bold mt-2">Là où commence le soin</p>
          <div class="d-flex gap-2 mt-3">
            <a href="#" class="text-white"><i class="bi bi-facebook fs-4"></i></a>
            <a href="#" class="text-white"><i class="bi bi-instagram fs-4"></i></a>
            <a href="#" class="text-white"><i class="bi bi-youtube fs-4"></i></a>
          </div>
        </div>
        <div class="col-md-2"><h6 class="fw-bold text-white">LUMA</h6>
          <a href="/" class="d-block text-white-50 text-decoration-none">À propos</a>
          <a href="/blog" class="d-block text-white-50 text-decoration-none">Blog</a>
          <a href="/communaute" class="d-block text-white-50 text-decoration-none">Communauté</a>
        </div>
        <div class="col-md-2"><h6 class="fw-bold text-white">Ressources</h6>
          <a href="/faq" class="d-block text-white-50 text-decoration-none">FAQ</a>
          <a href="/contact" class="d-block text-white-50 text-decoration-none">Contact</a>
          <a href="/ressources" class="d-block text-white-50 text-decoration-none">Guides</a>
        </div>
        <div class="col-md-4"><h6 class="fw-bold text-white">Télécharger l'app</h6>
          <div class="d-flex gap-2 mt-2">
            <div class="border border-light-pink rounded-3 p-2" style="background:var(--luma-glass);">
              <small class="text-white-50">download</small><br>
              <strong>App Store</strong>
            </div>
            <div class="border border-light-pink rounded-3 p-2" style="background:var(--luma-glass);">
              <small class="text-white-50">GET IT ON</small><br>
              <strong>Google Play</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/app.js"></script>
</body>
</html>
```

- [ ] **Step 2: Rewrite admin.php layout**

Bootstrap 5 sidebar layout with dark theme:
```php
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
  ...
</head>
<body>
  <div class="d-flex">
    <div class="sidebar-wrapper bg-luma-card p-3 d-flex flex-column">
      <h3 class="font-heading text-light-pink mb-4">LUMA Admin</h3>
      <nav class="nav flex-column sidebar-nav">
        <a class="nav-link" href="/admin"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a class="nav-link" href="/admin/articles"><i class="bi bi-file-text me-2"></i>Articles</a>
        <a class="nav-link" href="/admin/utilisateurs"><i class="bi bi-people me-2"></i>Utilisateurs</a>
        <a class="nav-link" href="/admin/mamans"><i class="bi bi-heart me-2"></i>Mamans</a>
        <a class="nav-link" href="/admin/experts"><i class="bi bi-person-badge me-2"></i>Experts</a>
        <a class="nav-link" href="/admin/ressources"><i class="bi bi-book me-2"></i>Ressources</a>
        <a class="nav-link" href="/admin/communaute"><i class="bi bi-chat-dots me-2"></i>Communauté</a>
        <a class="nav-link" href="/admin/tickets"><i class="bi bi-ticket me-2"></i>Tickets</a>
        <a class="nav-link" href="/admin/temoignages"><i class="bi bi-star me-2"></i>Témoignages</a>
        <a class="nav-link" href="/admin/faq"><i class="bi bi-question-circle me-2"></i>FAQ</a>
        <a class="nav-link" href="/admin/contacts"><i class="bi bi-envelope me-2"></i>Messages</a>
        <a class="nav-link" href="/admin/newsletter"><i class="bi bi-mailbox me-2"></i>Newsletter</a>
        <a class="nav-link" href="/admin/parametres"><i class="bi bi-gear me-2"></i>Paramètres</a>
        <hr class="text-white-50 my-2">
        <a class="nav-link" href="/"><i class="bi bi-arrow-left me-2"></i>Voir le site</a>
        <a class="nav-link" href="/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a>
      </nav>
    </div>
    <main class="flex-grow-1 p-4">
      <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>
      <?= $content ?>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/app.js"></script>
</body>
</html>
```

- [ ] **Step 3: Create maman.php layout**

Same structure as admin but with maman sidebar:
- Dashboard, Mon Profil, Ma Grossesse, Mon Bébé, Croissance, Vaccination, Blog, Ressources, Communauté, Questions Experts, Support, Notifications, Paramètres

- [ ] **Step 4: Create expert.php layout**

Expert sidebar: Dashboard, Profil Professionnel, Questions Mamans, Articles, Ressources, Communauté, Notifications, Paramètres

- [ ] **Step 5: Create ctt.php layout**

CTT sidebar: Dashboard, Gestion Tickets, Support Mamans, Support Experts, FAQ, Historique, Rapports, Notifications

---

### Task 5: Bootstrap 5 — Rewrite Auth Views

**Files:**
- Modify: `views/auth/login.php`
- Modify: `views/auth/register.php`

- [ ] **Step 1: login.php (Bootstrap 5)**

```php
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card card-luma p-4">
        <h1 class="font-heading text-center mb-1" style="font-size:48px;">Connexion</h1>
        <p class="text-center text-white-50 mb-4">Retrouvez votre espace LUMA</p>

        <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
          <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <form method="POST" action="/auth/login">
          <div class="mb-3">
            <input type="email" name="email" class="form-control form-control-luma" placeholder="Votre email" required>
          </div>
          <div class="mb-3">
            <input type="password" name="password" class="form-control form-control-luma" placeholder="Mot de passe" required>
          </div>
          <button type="submit" class="btn btn-luma w-100">Se connecter</button>
        </form>
        <p class="text-center text-white-50 mt-4">Pas encore de compte ? <a href="/auth/register" class="text-light-pink">S'inscrire</a></p>
      </div>
    </div>
  </div>
</div>
```

- [ ] **Step 2: register.php (Bootstrap 5 + role selection)**

Add role selection dropdown (maman/expert) to registration form:

```php
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card card-luma p-4">
        <h1 class="font-heading text-center mb-1" style="font-size:48px;">Créer un compte</h1>
        <p class="text-center text-white-50 mb-4">Rejoignez la communauté LUMA</p>

        <?php if ($errors = \App\Core\Session::getFlash('errors')): ?>
          <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
          <?php endforeach; ?>
        <?php endif; ?>

        <form method="POST" action="/auth/register">
          <div class="mb-3">
            <input type="text" name="name" class="form-control form-control-luma" placeholder="Nom complet" required>
          </div>
          <div class="mb-3">
            <input type="email" name="email" class="form-control form-control-luma" placeholder="Email" required>
          </div>
          <div class="mb-3">
            <select name="role" class="form-control form-control-luma" required>
              <option value="">Je suis...</option>
              <option value="maman">Future maman / Maman</option>
              <option value="expert">Expert (sage-femme, pédiatre...)</option>
            </select>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <input type="password" name="password" class="form-control form-control-luma" placeholder="Mot de passe (6 min)" required>
            </div>
            <div class="col-md-6">
              <input type="password" name="password_confirm" class="form-control form-control-luma" placeholder="Confirmer" required>
            </div>
          </div>
          <button type="submit" class="btn btn-luma w-100">S'inscrire</button>
        </form>
        <p class="text-center text-white-50 mt-4">Déjà inscrite ? <a href="/auth/login" class="text-light-pink">Se connecter</a></p>
      </div>
    </div>
  </div>
</div>
```

---

### Task 6: Bootstrap 5 — Rewrite Public Page Views

**Files:**
- Modify: `views/pages/home.php`
- Modify: `views/pages/blog.php`
- Modify: `views/pages/blog-single.php`
- Modify: `views/pages/contact.php`
- Modify: `views/pages/faq.php`
- Create: `views/pages/about.php`

All views must use Bootstrap 5 grid/component classes and the LUMA dark theme CSS classes.

- [ ] **Step 1-6: Rewrite each public page view** (follow same pattern as layouts — replace custom HTML/CSS with Bootstrap 5 components + luma CSS classes)

---

### Task 7: Bootstrap 5 — Rewrite Admin Views

**Files:**
- Modify: `views/admin/dashboard/index.php`
- Modify: `views/admin/articles/index.php`
- Modify: `views/admin/articles/form.php`
- Modify: `views/admin/categories/index.php`
- Modify: `views/admin/users/index.php`
- Modify: `views/admin/comments/index.php`
- Modify: `views/admin/contacts/index.php`
- Modify: `views/admin/testimonials/index.php`
- Modify: `views/admin/faqs/index.php`
- Modify: `views/admin/newsletters/index.php`
- Modify: `views/admin/forum/index.php`

All admin views rewritten with Bootstrap 5 tables, cards, forms, badges, buttons.

---

### Task 8: RBAC — Modify AuthController

**Files:**
- Modify: `app/Controllers/AuthController.php`

- [ ] **Step 1: Update AuthController for RBAC**

Changes:
- After login, fetch role slug from DB and store in session as `user_role_slug`
- Registration: accept `role` field, look up role_id by slug (maman or expert)
- After login, redirect based on role:
  - admin → `/admin`
  - maman → `/dashboard`
  - expert → `/expert/dashboard`
  - ctt → `/ctt/dashboard`

---

### Task 9: RBAC — Routes Update

**Files:**
- Modify: `routes.php`

- [ ] **Step 1: Update routes.php**

Replace the old routes with the new Phase 1 routes. Add routes for:
- `/a-propos` → PageController@about
- `/ressources` → ResourceController@index
- `/ressources/{slug}` → ResourceController@show
- `/communaute` → CommunityController@index
- `/communaute/{id}` → CommunityController@show
- `/communaute/{id}/comment` → CommunityController@comment
- `/communaute/{id}/like` → CommunityController@like (AJAX)
- `/dashboard/*` → Maman dashboard routes
- `/expert/dashboard` → ExpertController@index
- `/ctt/dashboard` → CttController@index
- `/tickets/*` → TicketController routes
- `/notifications/*` → NotificationController routes
- Admin sub-routes for new modules

---

### Task 10: New Controllers — Resources, Community, Tickets

**Files:**
- Create: `app/Controllers/ResourceController.php`
- Create: `app/Controllers/CommunityController.php` (replace existing)
- Create: `app/Controllers/TicketController.php`
- Create: `app/Controllers/NotificationController.php`

Each handles standard CRUD with RBAC permission checks.

---

### Task 11: Dashboards — Maman, Expert, CTT

**Files:**
- Modify: `app/Controllers/DashboardController.php` (maman dashboard)
- Create: `app/Controllers/ExpertController.php`
- Create: `app/Controllers/CttController.php`
- Create: `views/pages/dashboard/*.php` (maman sub-views)
- Create: `views/expert/*.php`
- Create: `views/ctt/*.php`

- [ ] **Step 1-3: Implement each dashboard controller**

Each dashboard controller:
- Checks role via RoleMiddleware
- Fetches stats and data for widgets
- Renders the role-specific layout

---

### Task 12: Admin Sub-Controllers

**Files:**
- Create: `app/Controllers/Admin/AdminUserController.php` (moved from root)
- Create: `app/Controllers/Admin/AdminResourceController.php`
- Create: `app/Controllers/Admin/AdminExpertController.php`
- Create: `app/Controllers/Admin/AdminTicketController.php`
- Create: `app/Controllers/Admin/AdminCommunityController.php`
- Create: `app/Controllers/Admin/AdminSettingsController.php`

Organize admin controllers into `app/Controllers/Admin/` namespace for cleanliness.

---

### Task 13: Verify & Test

- [ ] **Step 1: Run PHP syntax check**

```bash
find . -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors"
```

- [ ] **Step 2: Reimport database and test routes**

```bash
ddev mysql luma < migrations/v2_create_tables.sql
curl -s -o /dev/null -w "%{http_code} " https://luma.ddev.site/ && echo ""
```

- [ ] **Step 3: Test auth for each role**

```bash
# Test login as admin
curl -s -L -c /tmp/luma_cookies -b /tmp/luma_cookies -X POST -d "email=admin@luma.tn&password=password" https://luma.ddev.site/auth/login -o /dev/null -w "%{http_code}"
# Verify redirect to /admin
curl -s -L -b /tmp/luma_cookies https://luma.ddev.site/admin -o /dev/null -w "%{http_code}"
```

- [ ] **Step 4: Test all admin sub-routes return 200**

```bash
for route in admin admin/articles admin/utilisateurs admin/ressources admin/communaute admin/tickets admin/temoignages admin/faq admin/contacts admin/parametres; do
  curl -s -L -b /tmp/luma_cookies https://luma.ddev.site/$route -o /dev/null -w "%{http_code} $route\n"
done
```

- [ ] **Step 5: Test public routes**

```bash
for route in "" blog contact faq ressources communaute a-propos; do
  curl -s -o /dev/null -w "%{http_code} /$route\n" https://luma.ddev.site/$route
done
```
