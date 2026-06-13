<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'LUMA') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/dashboard.css">
  <link rel="stylesheet" href="/assets/css/animations.css">
  <link rel="stylesheet" href="/assets/css/enhancements.css">
</head>
<body>
<div class="dashboard-layout">
  <!-- Sidebar -->
  <aside class="sidebar-dashboard" id="sidebar">
    <div class="sidebar-header">
      <a href="/" class="sidebar-logo">
        <img src="/assets/images/home/logo.svg" alt="LUMA" height="32">
      </a>
      <button class="sidebar-close" id="sidebarClose" aria-label="Fermer"><i class="bi bi-x-lg"></i></button>
    </div>
    <nav class="sidebar-nav">
      <?php foreach ($sidebarLinks as $link): ?>
        <a href="<?= htmlspecialchars($link['url']) ?>" class="nav-link">
          <i class="bi <?= htmlspecialchars($link['icon']) ?>"></i>
          <span><?= htmlspecialchars($link['label']) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
      <div class="sidebar-footer-label">Actions</div>
      <a href="/" class="footer-link footer-link-site"><i class="bi bi-globe2"></i><span>Voir le site</span></a>
      <a href="/auth/logout" class="footer-link footer-link-logout"><i class="bi bi-box-arrow-right"></i><span>Déconnexion</span></a>
    </div>
  </aside>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Main -->
  <div class="main-dashboard">
    <header class="topbar-dashboard">
      <button class="sidebar-toggle" id="sidebarToggle" aria-label="Menu"><i class="bi bi-list"></i></button>
      <div class="topbar-breadcrumb"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></div>
      <div class="topbar-right">
        <div class="dropdown">
          <button class="dropdown-toggle topbar-avatar" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? '/assets/images/default-avatar.png') ?>" alt="">
            <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur') ?></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
          </ul>
        </div>
      </div>
    </header>

    <main class="content-dashboard">
      <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>
      <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>
      <?= $content ?>
    </main>

    <footer class="footer-dashboard">
      <div class="container-fluid">
        <div class="footer-dashboard-inner">
          <span>&copy; <?= date('Y') ?> LUMA. Tous droits réservés.</span>
        </div>
      </div>
    </footer>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/dashboard.js"></script>
</body>
</html>
