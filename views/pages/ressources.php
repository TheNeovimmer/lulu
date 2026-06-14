<!-- Ressources Hero -->
<section class="blog-hero position-relative overflow-hidden">
  <div class="blog-hero-bg" aria-hidden="true"></div>
  <div class="container position-relative" style="z-index:3;">
    <div class="row min-vh-100 align-items-center">
      <div class="col-lg-7">
        <div class="blog-hero-content">
          <h1 class="blog-hero-title">
            Conseils & <span class="text-rose">ressources</span>
          </h1>
          <p class="blog-hero-subtitle">
            Des guides pratiques, des fiches conseils <br>et des documents à télécharger
          </p>
          <p class="blog-hero-desc">
            Accédez à une bibliothèque de ressources conçues par des experts pour vous accompagner à chaque étape de votre maternité.
          </p>
          <a href="#ressources-content" class="btn-cta btn-cta--ghost" aria-label="Découvrir">
            <span>Découvrir</span>
            <svg class="btn-arrow" width="34" height="12" viewBox="0 0 34 12" fill="none" aria-hidden="true">
              <path d="M0 6H28M28 6L22 1M28 6L22 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Ressources Content -->
<section class="blog-posts-section" id="ressources-content">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8">
        <!-- Category filters -->
        <div class="blog-filters mb-5">
          <a href="?" class="blog-filter-pill <?= empty($_GET['category']) ? 'active' : '' ?>">Toutes</a>
          <?php foreach ($categories as $cat): ?>
            <a href="?category=<?= $cat['id'] ?>" class="blog-filter-pill <?= ($_GET['category'] ?? '') == $cat['id'] ? 'active' : '' ?>"><?= htmlspecialchars($cat['name']) ?></a>
          <?php endforeach; ?>
        </div>

        <?php if (!empty($resources)): ?>
        <div class="row g-4">
          <?php foreach ($resources as $i => $r): ?>
          <div class="col-md-6">
            <article class="blog-card" style="--i:<?= $i ?>">
              <div class="blog-card-img" style="background:linear-gradient(135deg, #401326 0%, #2e0f1c 100%);">
                <?php if ($r['category_name']): ?>
                  <span class="blog-card-category"><?= htmlspecialchars($r['category_name']) ?></span>
                <?php endif; ?>
              </div>
              <div class="blog-card-body">
                <h4 class="blog-card-title"><?= htmlspecialchars($r['title']) ?></h4>
                <p class="blog-card-excerpt"><?= htmlspecialchars(mb_substr($r['description'] ?? '', 0, 120)) ?></p>
                <div class="blog-card-footer">
                  <span class="blog-card-date">
                    <i class="bi bi-download me-1"></i><?= htmlspecialchars($r['downloads_count'] ?? 0) ?> téléchargements
                  </span>
                  <a href="<?= $r['file_url'] ? htmlspecialchars($r['file_url']) : '#' ?>" class="blog-card-link" <?= strpos($r['file_url'] ?? '', '.pdf') !== false ? 'download' : '' ?>>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f0a0bb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                  </a>
                </div>
              </div>
            </article>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
          <i class="bi bi-journal-text text-pink fs-1 mb-3"></i>
          <p class="text-white-50">Aucune ressource disponible pour le moment.</p>
        </div>
        <?php endif; ?>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <!-- Search -->
        <div class="blog-sidebar-card blog-sidebar-search">
          <h5 class="blog-sidebar-title">Rechercher</h5>
          <form method="GET" action="/ressources" class="blog-search-form">
            <input type="text" name="q" class="blog-search-input" placeholder="Rechercher une ressource..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button type="submit" class="blog-search-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
              </svg>
            </button>
          </form>
        </div>

        <!-- Popular resources -->
        <?php if (!empty($popular)): ?>
        <div class="blog-sidebar-card">
          <h5 class="blog-sidebar-title">Les plus téléchargés</h5>
          <div class="blog-popular-list">
            <?php foreach ($popular as $pop): ?>
            <a href="<?= $pop['file_url'] ? htmlspecialchars($pop['file_url']) : '#' ?>" class="blog-popular-item">
              <div class="blog-popular-thumb" style="background:linear-gradient(135deg, #401326 0%, #2e0f1c 100%);">
                <i class="bi bi-file-text" style="color:#f0a0bb;font-size:1.2rem;"></i>
              </div>
              <div class="blog-popular-info">
                <span class="blog-popular-title"><?= htmlspecialchars($pop['title']) ?></span>
                <span class="blog-popular-date"><?= htmlspecialchars($pop['downloads_count'] ?? 0) ?> téléchargements</span>
              </div>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Newsletter -->
        <div class="blog-sidebar-card blog-sidebar-newsletter">
          <div class="blog-newsletter-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f0a0bb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
            </svg>
          </div>
          <h5 class="blog-sidebar-title">Newsletter</h5>
          <p class="blog-newsletter-text">Recevez nos nouvelles ressources chaque semaine.</p>
          <p class="blog-newsletter-sub">Des conseils et documents directement dans votre boite mail.</p>
          <form method="POST" action="/newsletter/subscribe" class="blog-newsletter-form">
            <?= \App\Core\Session::csrf_field() ?>
            <input type="email" name="email" class="blog-newsletter-input" placeholder="Votre e-mail" required>
            <button type="submit" class="blog-newsletter-btn">S'abonner</button>
          </form>
          <p class="blog-newsletter-disclaimer">Pas de spam, désinscription à tout moment.</p>
        </div>
      </div>
    </div>
  </div>
</section>
