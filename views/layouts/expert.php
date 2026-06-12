<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'Espace Expert - LUMA' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/animations.css">
  <link rel="stylesheet" href="/assets/css/enhancements.css">
</head>
<body>
  <div class="d-flex">
    <div class="sidebar-wrapper bg-luma-card p-3 d-flex flex-column">
      <h3 class="font-heading text-light-pink mb-4"><i class="bi bi-person-badge me-2"></i>Expert LUMA</h3>
      <nav class="nav flex-column sidebar-nav flex-grow-1">
        <a class="nav-link" href="/expert/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a class="nav-link" href="/expert/profil"><i class="bi bi-file-earmark-person me-2"></i>Profil Professionnel</a>
        <a class="nav-link" href="/expert/questions"><i class="bi bi-question-circle me-2"></i>Questions Mamans</a>
        <a class="nav-link" href="/expert/articles"><i class="bi bi-file-text me-2"></i>Articles</a>
        <a class="nav-link" href="/expert/ressources"><i class="bi bi-book me-2"></i>Ressources</a>
        <a class="nav-link" href="/communaute"><i class="bi bi-chat-dots me-2"></i>Communauté</a>
        <a class="nav-link" href="/expert/notifications"><i class="bi bi-bell me-2"></i>Notifications</a>
        <a class="nav-link" href="/expert/parametres"><i class="bi bi-gear me-2"></i>Paramètres</a>
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
