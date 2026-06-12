<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'Admin - LUMA' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/admin.css">
  <link rel="stylesheet" href="/assets/css/animations.css">
  <link rel="stylesheet" href="/assets/css/enhancements.css">
</head>
<body>
  <div class="d-flex">
    <div class="sidebar-wrapper bg-luma-card p-3 d-flex flex-column" style="min-height:100vh;">
      <h3 class="font-heading text-light-pink mb-4"><i class="bi bi-shield-shaded me-2"></i>LUMA Admin</h3>
      <nav class="nav flex-column sidebar-nav flex-grow-1">
        <a class="nav-link" href="/admin"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a class="nav-link" href="/admin/articles"><i class="bi bi-file-text me-2"></i>Articles</a>
        <a class="nav-link" href="/admin/categories"><i class="bi bi-tags me-2"></i>Catégories</a>
        <a class="nav-link" href="/admin/utilisateurs"><i class="bi bi-people me-2"></i>Utilisateurs</a>
        <a class="nav-link" href="/admin/mamans"><i class="bi bi-heart me-2"></i>Mamans</a>
        <a class="nav-link" href="/admin/experts"><i class="bi bi-person-badge me-2"></i>Experts</a>
        <a class="nav-link" href="/admin/ressources"><i class="bi bi-book me-2"></i>Ressources</a>
        <a class="nav-link" href="/admin/communaute"><i class="bi bi-chat-dots me-2"></i>Communauté</a>
        <a class="nav-link" href="/admin/tickets"><i class="bi bi-ticket me-2"></i>Tickets</a>
        <a class="nav-link" href="/admin/comments"><i class="bi bi-chat-square-text me-2"></i>Commentaires</a>
        <a class="nav-link" href="/admin/testimonials"><i class="bi bi-star me-2"></i>Témoignages</a>
        <a class="nav-link" href="/admin/faqs"><i class="bi bi-question-circle me-2"></i>FAQ</a>
        <a class="nav-link" href="/admin/contacts"><i class="bi bi-envelope me-2"></i>Messages</a>
        <a class="nav-link" href="/admin/newsletters"><i class="bi bi-mailbox me-2"></i>Newsletter</a>
        <a class="nav-link" href="/admin/parametres"><i class="bi bi-gear me-2"></i>Paramètres</a>
      </nav>
      <hr class="text-white-50 my-2">
      <a class="nav-link text-white-50 small" href="/"><i class="bi bi-arrow-left me-2"></i>Voir le site</a>
      <a class="nav-link text-white-50 small" href="/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a>
    </div>
    <main class="flex-grow-1 p-4 page-entrance">
      <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>
      <?= $content ?>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/app.js"></script>
</body>
</html>
