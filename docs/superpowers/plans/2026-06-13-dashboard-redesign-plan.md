# Dashboard Redesign — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign all 4 dashboards (Admin, Mama, Expert, CTT) with a white + rose professional theme, fixing sidebar layout and AJAX CRUD actions.

**Architecture:** Unified `dashboard.php` layout replaces 4 separate dark layouts. Shared `dashboard.css` + `dashboard.js` provide design system and interactions. Each role gets a sidebar template injected via PHP variable.

**Tech Stack:** PHP 8, Bootstrap 5, CSS3, vanilla JS, PDO/MySQL

---

### Task 0: Foundation — CSS Design System + Layout Structure

**Files:**
- Create: `public/assets/css/dashboard.css`
- Create: `public/assets/js/dashboard.js`
- Create: `views/layouts/dashboard.php`

**Prerequisites:**
- [ ] Read existing `views/layouts/admin.php`, `views/layouts/maman.php`, `views/layouts/expert.php`, `views/layouts/ctt.php` to understand current sidebar nav structures
- [ ] Read `public/assets/css/enhancements.css` to avoid conflicting selectors
- [ ] Read `public/assets/js/app.js` to understand existing JS patterns

**Step 1: Create `public/assets/css/dashboard.css`**
Design system with:
- CSS custom properties for the white+rose palette
- `.dashboard-layout` — flex container (sidebar + main)
- `.sidebar-dashboard` — fixed 240px white sidebar
- `.sidebar-dashboard .nav-link` — rose hover/active states
- `.sidebar-dashboard .nav-link.active` — 3px left rose border
- `.topbar-dashboard` — 64px white top bar
- `.main-dashboard` — scrollable content area
- `.stat-card-dashboard` — white card, rose icon circle, trend indicators
- `.table-dashboard` — clean table, sticky header, rose row hover
- `.badge-dashboard` — status badges (success/warning/danger/info)
- `.btn-icon` — icon-only circle buttons
- `.form-dashboard` — floating label forms
- `.toast-dashboard` — toast notifications
- `.modal-dashboard` — clean modals
- `.pagination-dashboard` — rose-themed pagination
- Responsive breakpoints (1200px, 992px, 768px)
- Sidebar collapsed state at 768px: `.sidebar-dashboard.collapsed` width 64px, text hidden

**Step 2: Create `public/assets/js/dashboard.js`**
```javascript
// Sidebar toggle for mobile
function initSidebar() {
  const toggle = document.querySelector('.sidebar-toggle');
  const sidebar = document.querySelector('.sidebar-dashboard');
  const overlay = document.querySelector('.sidebar-overlay');
  if (!toggle || !sidebar) return;
  toggle.addEventListener('click', () => {
    if (window.innerWidth < 768) {
      sidebar.classList.toggle('show');
      overlay?.classList.toggle('show');
    } else {
      sidebar.classList.toggle('collapsed');
      document.querySelector('.main-dashboard')?.classList.toggle('expanded');
    }
  });
  overlay?.addEventListener('click', () => {
    sidebar?.classList.remove('show');
    overlay.classList.remove('show');
  });
}
// Auto-highlight active nav link based on URL
function initActiveNav() {
  const path = window.location.pathname;
  document.querySelectorAll('.sidebar-dashboard .nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (href && path.startsWith(href) && href !== '/') {
      link.classList.add('active');
    }
  });
}
// AJAX action handler
function initActions() {
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;
    e.preventDefault();
    const action = btn.dataset.action;
    const id = btn.dataset.id;
    const url = btn.dataset.url;
    const confirmMsg = btn.dataset.confirm;
    if (confirmMsg && !confirm(confirmMsg)) return;
    btn.disabled = true;
    try {
      const res = await fetch(url, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const data = await res.json();
      if (data.success) {
        showToast(data.message || 'Action effectuée', 'success');
        if (data.row) {
          const row = btn.closest('tr');
          if (data.row.remove) { row?.remove(); }
          else if (data.row.badge) { row?.querySelector('.badge-dashboard')?.replaceWith(data.row.badge); }
        }
      } else {
        showToast(data.message || 'Erreur', 'error');
      }
    } catch (err) {
      showToast('Erreur réseau', 'error');
    } finally {
      btn.disabled = false;
    }
  });
}
// Toast notification
function showToast(message, type = 'success') {
  const container = document.querySelector('.toast-container-dashboard') || (() => {
    const c = document.createElement('div'); c.className = 'toast-container-dashboard'; document.body.appendChild(c); return c;
  })();
  const toast = document.createElement('div');
  toast.className = `toast-dashboard ${type}`;
  toast.innerHTML = `<i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'}"></i><span>${message}</span>`;
  container.appendChild(toast);
  setTimeout(() => { toast.classList.add('fade'); setTimeout(() => toast.remove(), 300); }, 3000);
}
// Delete confirmation via modal
function confirmDelete(url, name) {
  if (confirm(`Voulez-vous vraiment supprimer ${name} ?`)) {
    const form = document.createElement('form'); form.method = 'POST'; form.action = url; document.body.appendChild(form); form.submit();
  }
}
// Init all
document.addEventListener('DOMContentLoaded', () => { initSidebar(); initActiveNav(); initActions(); });
window.addEventListener('resize', () => {
  const sidebar = document.querySelector('.sidebar-dashboard');
  if (window.innerWidth >= 768) sidebar?.classList.remove('show');
  else sidebar?.classList.remove('collapsed');
});
```

**Step 3: Create `views/layouts/dashboard.php`**
- Unified layout with `$sidebarLinks` and `$pageTitle` variables
- Full HTML structure: topbar → sidebar → main → footer
- Topbar: hamburger toggle, breadcrumb, user dropdown
- Sidebar: logo, nav links from `$sidebarLinks`, bottom actions
- Main: `<?= $content ?>` with page-title header
- Include: `dashboard.css`, `dashboard.js`, Bootstrap, Bootstrap Icons, Inter font

```php
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'LUMA' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>
<body>
<div class="dashboard-layout">
  <!-- Sidebar -->
  <aside class="sidebar-dashboard" id="sidebar">
    <div class="sidebar-header">
      <a href="/" class="sidebar-logo">
        <img src="/assets/images/home/logo.svg" alt="LUMA" height="32">
      </a>
      <button class="sidebar-close d-md-none" id="sidebarClose"><i class="bi bi-x-lg"></i></button>
    </div>
    <nav class="sidebar-nav">
      <?php foreach ($sidebarLinks as $link): ?>
        <a href="<?= $link['url'] ?>" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], $link['url']) === 0 && $link['url'] !== '/' || ($link['url'] === '/' . $_SESSION['user_role_slug'] ?? '' . '/dashboard' && $_SERVER['REQUEST_URI'] === '/' . ($_SESSION['user_role_slug'] ?? '') . '/dashboard' || $_SERVER['REQUEST_URI'] === '/' . ($_SESSION['user_role_slug'] ?? '')))) ? 'active' : '' ?>">
          <i class="bi <?= $link['icon'] ?>"></i>
          <span><?= $link['label'] ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
      <hr>
      <a href="/" class="nav-link"><i class="bi bi-arrow-left"></i><span>Voir le site</span></a>
      <a href="/auth/logout" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Déconnexion</span></a>
    </div>
  </aside>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Main -->
  <div class="main-dashboard">
    <!-- Top Bar -->
    <header class="topbar-dashboard">
      <button class="sidebar-toggle btn btn-light" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <div class="topbar-breadcrumb"><?= $pageTitle ?? 'Dashboard' ?></div>
      <div class="topbar-right">
        <div class="dropdown">
          <button class="dropdown-toggle topbar-avatar" data-bs-toggle="dropdown">
            <img src="<?= $_SESSION['user_avatar'] ?? '/assets/images/default-avatar.png' ?>" alt="" width="32" height="32" class="rounded-circle">
            <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur') ?></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
          </ul>
        </div>
      </div>
    </header>

    <!-- Content -->
    <main class="content-dashboard">
      <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>
      <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>
      <?= $content ?>
    </main>

    <footer class="footer-dashboard">
      <div class="footer-dashboard-inner">
        <span>&copy; <?= date('Y') ?> LUMA. Tous droits réservés.</span>
      </div>
    </footer>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/dashboard.js"></script>
</body>
</html>
```

- [ ] **Step 4: Redirect existing dashboard layouts to the new one**
  - Modify `views/layouts/admin.php` → thin wrapper that sets `$sidebarLinks` and calls dashboard layout
  - Modify `views/layouts/maman.php` → same pattern
  - Modify `views/layouts/expert.php` → same pattern
  - Modify `views/layouts/ctt.php` → same pattern
  
  Each existing layout becomes:
  ```php
  <?php
  $pageTitle = $title ?? 'Dashboard';
  $sidebarLinks = [...]; // role-specific links
  require __DIR__ . '/dashboard.php';
  ```
  But wait — the current View::render() does `require __DIR__ . "/../../views/{$view}.php"` to get content, then `require __DIR__ . "/../../views/layouts/{$layout}.php"` for layout. The layout file echoes `$content`.

  So the old layout files need to be the ones that set up sidebarLinks, then require dashboard.php. Since View::render loads the layout after the view, the file referenced as `$layout` needs to be the entry point. I'll modify each layout file to:
  ```php
  <?php
  $pageTitle = $title ?? 'Dashboard';
  $sidebarLinks = [...]; // role-specific array
  require __DIR__ . '/dashboard.php';
  ```
  And `dashboard.php` will do the HTML shell.

  Actually, there's a naming conflict — `dashboard.php` is the layout, but there's also `views/dashboard/` for mama views. The layout path would be `views/layouts/dashboard.php`. Let me verify View::render path resolution:

  View::render does:
  ```
  require __DIR__ . "/../../views/{$view}.php";  // captures $content
  require __DIR__ . "/../../views/layouts/{$layout}.php";  // layout file
  ```

  So `$layout = 'dashboard'` → loads `views/layouts/dashboard.php`. That works. The old layouts (`admin.php`, `maman.php`, etc.) are what get passed as `$layout` parameter. If I change the controllers to pass `'dashboard'` as layout instead, I need to update all controllers. 

  Better approach: Keep each old layout file as thin shim that sets up sidebar data and loads dashboard.php:

  `views/layouts/admin.php`:
  ```php
  <?php
  $pageTitle = $title ?? 'Dashboard Admin';
  $sidebarLinks = [
    ['url' => '/admin', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
    ['url' => '/admin/articles', 'icon' => 'bi-file-text', 'label' => 'Articles'],
    ['url' => '/admin/categories', 'icon' => 'bi-tags', 'label' => 'Catégories'],
    ['url' => '/admin/utilisateurs', 'icon' => 'bi-people', 'label' => 'Utilisateurs'],
    ['url' => '/admin/mamans', 'icon' => 'bi-heart', 'label' => 'Mamans'],
    ['url' => '/admin/experts', 'icon' => 'bi-person-badge', 'label' => 'Experts'],
    ['url' => '/admin/ressources', 'icon' => 'bi-book', 'label' => 'Ressources'],
    ['url' => '/admin/communaute', 'icon' => 'bi-chat-dots', 'label' => 'Communauté'],
    ['url' => '/admin/tickets', 'icon' => 'bi-ticket', 'label' => 'Tickets'],
    ['url' => '/admin/comments', 'icon' => 'bi-chat-square-text', 'label' => 'Commentaires'],
    ['url' => '/admin/testimonials', 'icon' => 'bi-star', 'label' => 'Témoignages'],
    ['url' => '/admin/faqs', 'icon' => 'bi-question-circle', 'label' => 'FAQ'],
    ['url' => '/admin/contacts', 'icon' => 'bi-envelope', 'label' => 'Messages'],
    ['url' => '/admin/newsletters', 'icon' => 'bi-mailbox', 'label' => 'Newsletter'],
    ['url' => '/admin/parametres', 'icon' => 'bi-gear', 'label' => 'Paramètres'],
  ];
  require __DIR__ . '/dashboard.php';
  ```

  The `$content` variable set by View::render will be available in dashboard.php. This works because the old layout file (which View::render includes) only sets up vars and delegates to dashboard.php, which has the full HTML shell and echoes `$content`.

- [ ] **Step 5: Verify foundation renders correctly**
  - Navigate to `/admin` → page loads with white/rose layout
  - Navigate to `/dashboard` → same layout with mama links
  - Sidebar toggle works on mobile
  - Active nav link auto-highlights

---

### Task 2: Admin Dashboard Views

**Files:**
- Modify: All files in `views/admin/` (17 files)
- Reference: Existing controllers in `app/Controllers/Admin*.php`

- [ ] **Step 1: Redesign `views/admin/dashboard.php`** — stat cards row + recent items + quick actions
- [ ] **Step 2: Redesign `views/admin/users.php`** — table with role toggle, suspend, delete
- [ ] **Step 3: Redesign `views/admin/articles.php`** — table with publish/draft, edit, delete
- [ ] **Step 4: Redesign `views/admin/article-form.php`** — create/edit form with floating labels
- [ ] **Step 5: Redesign `views/admin/categories.php`** — list with create/delete
- [ ] **Step 6: Redesign `views/admin/experts.php`** — table with approve/reject
- [ ] **Step 7: Redesign `views/admin/mamans.php`** — table list
- [ ] **Step 8: Redesign `views/admin/ressources.php`** — table list
- [ ] **Step 9: Redesign `views/admin/ressource-form.php`** — create form
- [ ] **Step 10: Redesign `views/admin/communaute.php`** — posts table with hide/delete
- [ ] **Step 11: Redesign `views/admin/tickets.php`** — tickets table with assign/close
- [ ] **Step 12: Redesign `views/admin/comments.php`** — comments table with approve/reject
- [ ] **Step 13: Redesign `views/admin/testimonials.php`** — testimonials table with approve/reject
- [ ] **Step 14: Redesign `views/admin/faqs.php`** — FAQ list with add/delete
- [ ] **Step 15: Redesign `views/admin/contacts.php`** — messages table with mark-read/delete
- [ ] **Step 16: Redesign `views/admin/newsletters.php`** — subscribers table
- [ ] **Step 17: Redesign `views/admin/settings.php`** — settings form

---

### Task 3: Mama Dashboard Views

**Files:**
- Modify: All files in `views/dashboard/` (12 files)

- [ ] **Step 1: Redesign `views/dashboard/index.php`** — welcome card, baby info, articles, community
- [ ] **Step 2: Redesign `views/dashboard/profil.php`** — profile form
- [ ] **Step 3: Redesign `views/dashboard/grossesse.php`** — pregnancy tracking
- [ ] **Step 4: Redesign `views/dashboard/bebe.php`** — baby info + memories + milestones
- [ ] **Step 5: Redesign `views/dashboard/croissance.php`** — growth chart
- [ ] **Step 6: Redesign `views/dashboard/vaccination.php`** — vaccination records
- [ ] **Step 7: Redesign `views/dashboard/tickets.php`** — support tickets
- [ ] **Step 8: Redesign `views/dashboard/notifications.php`** — notifications
- [ ] **Step 9: Redesign `views/dashboard/parametres.php`** — password change
- [ ] **Step 10: Redesign `views/dashboard/appointments.php`** — appointments
- [ ] **Step 11: Redesign `views/dashboard/messages.php`** — messaging
- [ ] **Step 12: Redesign `views/dashboard/agenda.php`** — agenda

---

### Task 4: Expert Dashboard Views

**Files:**
- Modify: All files in `views/expert/` (7 files)

- [ ] **Step 1: Redesign `views/expert/index.php`** — stats + questions + articles
- [ ] **Step 2: Redesign `views/expert/profil.php`** — profile form
- [ ] **Step 3: Redesign `views/expert/questions.php`** — questions list + answer
- [ ] **Step 4: Redesign `views/expert/articles.php`** — articles list + create
- [ ] **Step 5: Redesign `views/expert/ressources.php`** — resources list
- [ ] **Step 6: Redesign `views/expert/notifications.php`** — notifications
- [ ] **Step 7: Redesign `views/expert/parametres.php`** — password change

---

### Task 5: CTT Dashboard Views

**Files:**
- Modify: All files in `views/ctt/` (6 files)

- [ ] **Step 1: Redesign `views/ctt/index.php`** — stats + tickets
- [ ] **Step 2: Redesign `views/ctt/tickets.php`** — tickets management
- [ ] **Step 3: Redesign `views/ctt/faq.php`** — FAQ management
- [ ] **Step 4: Redesign `views/ctt/historique.php`** — history
- [ ] **Step 5: Redesign `views/ctt/rapports.php`** — reports
- [ ] **Step 6: Redesign `views/ctt/notifications.php`** — notifications

---

### Task 6: Verification

- [ ] **Step 1: Check admin CRUD flows** — create/edit/delete articles, approve/reject experts, manage users
- [ ] **Step 2: Check mama flows** — profile update, baby tracker, community, tickets
- [ ] **Step 3: Check expert flows** — answer questions, create articles, manage profile
- [ ] **Step 4: Check CTT flows** — manage tickets, FAQ, view reports
- [ ] **Step 5: Check responsive** — mobile sidebar overlay, table scroll, stat cards wrapping
- [ ] **Step 6: Commit**
