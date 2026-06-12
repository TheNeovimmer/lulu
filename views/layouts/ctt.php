<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'Support - LUMA' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/animations.css">
  <link rel="stylesheet" href="/assets/css/enhancements.css">
</head>
<body>
  <div class="d-flex">
    <div class="sidebar-wrapper bg-luma-card p-3 d-flex flex-column">
      <h3 class="font-heading text-light-pink mb-4"><i class="bi bi-headset me-2"></i>CTT Support</h3>
      <nav class="nav flex-column sidebar-nav flex-grow-1">
        <a class="nav-link" href="/ctt/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a class="nav-link" href="/ctt/tickets"><i class="bi bi-ticket me-2"></i>Gestion Tickets</a>
        <a class="nav-link" href="/ctt/tickets?type=maman"><i class="bi bi-heart me-2"></i>Support Mamans</a>
        <a class="nav-link" href="/ctt/tickets?type=expert"><i class="bi bi-person-badge me-2"></i>Support Experts</a>
        <a class="nav-link" href="/ctt/faq"><i class="bi bi-question-circle me-2"></i>FAQ</a>
        <a class="nav-link" href="/ctt/historique"><i class="bi bi-clock-history me-2"></i>Historique</a>
        <a class="nav-link" href="/ctt/rapports"><i class="bi bi-bar-chart me-2"></i>Rapports</a>
        <a class="nav-link" href="/ctt/notifications"><i class="bi bi-bell me-2"></i>Notifications</a>
      </nav>
      <hr class="text-white-50 my-2">
      <a class="nav-link text-white-50 small" href="/"><i class="bi bi-arrow-left me-2"></i>Voir le site</a>
      <a class="nav-link text-white-50 small" href="/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a>
    </div>
    <main class="flex-grow-1 p-4 page-entrance">
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
