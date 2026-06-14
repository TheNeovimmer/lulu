<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'LUMA - Là où commence le soin' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;800&family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&family=Montserrat:wght@500;600&family=Karma:wght@400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/animations.css">
  <link rel="stylesheet" href="/assets/css/enhancements.css">
</head>
<body>
  <?php if (!isset($heroFull) || !$heroFull): ?>
  <nav class="navbar navbar-expand-lg sticky-top" style="background:transparent; padding-top:0;">
    <div class="container">
      <div class="nav-pill-luma d-flex align-items-center w-100">
        <a class="navbar-brand fw-bold me-4" href="/" style="line-height:1;">
          <img src="/assets/images/home/logo.svg" alt="LUMA" height="34" style="display:block;">
        </a>
        <button class="navbar-toggler border-0 ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" style="color:rgba(255,255,255,0.8);">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
          <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
            <li class="nav-item"><a class="nav-link" href="/">Accueil</a></li>
            <li class="nav-item"><a class="nav-link" href="/ressources">Conseils</a></li>
            <li class="nav-item"><a class="nav-link" href="/communaute">Communauté</a></li>
            <li class="nav-item"><a class="nav-link" href="/blog">Blog</a></li>
            <li class="nav-item"><a class="nav-link" href="/a-propos">A propos</a></li>
            <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
          </ul>
          <div class="ms-lg-3 mt-2 mt-lg-0">
            <?php if (\App\Core\Session::has('user_id')): ?>
              <?php $role = \App\Core\Session::get('user_role_slug'); ?>
              <a class="btn-mon-compte text-decoration-none" href="<?= $role === 'admin' ? '/admin' : ($role === 'expert' ? '/expert/dashboard' : ($role === 'ctt' ? '/ctt/dashboard' : '/dashboard')) ?>">
                <i class="bi bi-person-fill me-1"></i>Mon compte
              </a>
            <?php else: ?>
              <a class="btn-mon-compte text-decoration-none" href="/auth/login">
                <i class="bi bi-person-fill me-1"></i>Mon compte
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </nav>
  <?php endif; ?>

  <main class="page-entrance">
    <?= $content ?>
  </main>

  <?php require __DIR__ . '/../partials/footer-front.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/app.js"></script>
</body>
</html>
