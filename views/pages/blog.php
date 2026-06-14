<!-- Blog Hero -->
<section class="blog-hero position-relative overflow-hidden">
  <div class="blog-hero-bg" aria-hidden="true"></div>
  <div class="container position-relative" style="z-index:3;">
    <div class="row min-vh-100 align-items-center">
      <div class="col-lg-7">
        <div class="blog-hero-content">
          <h1 class="blog-hero-title">
            Le blog <span class="text-rose">LUMA</span>
          </h1>
          <p class="blog-hero-subtitle">
            Des conseils, des expériences <br>et du soutien à chaque étape
          </p>
          <p class="blog-hero-desc">
            Découvrez des articles rédigés par des experts et des mamans passionnées pour vous accompagner dans votre maternité en toute sérénité.
          </p>
          <a href="#blog-content" class="btn-cta btn-cta--ghost" aria-label="Découvrir">
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

<?php if (!empty($articles)): $featuredArticle = $articles[0]; ?>

<!-- A la une -->
<section class="blog-featured-section">
  <div class="container">
    <h2 class="blog-section-title">A la une</h2>
    <a href="/blog/<?= htmlspecialchars($featuredArticle['slug']) ?>" class="text-decoration-none">
      <div class="blog-featured-card">
        <div class="blog-featured-img" style="background-image:url(<?= htmlspecialchars($featuredArticle['image'] ?: 'https://static.codia.ai/image/2026-06-14/cgymZtr6xQ.png') ?>);"></div>
        <div class="blog-featured-body">
          <?php if ($featuredArticle['category_name']): ?>
            <span class="blog-category-pill"><?= htmlspecialchars($featuredArticle['category_name']) ?></span>
          <?php endif; ?>
          <h3 class="blog-featured-title"><?= htmlspecialchars($featuredArticle['title']) ?></h3>
          <p class="blog-featured-excerpt"><?= htmlspecialchars(mb_substr(strip_tags($featuredArticle['content'] ?? ''), 0, 200)) ?>...</p>
          <div class="blog-featured-meta">
            <?php
              $authorName = $featuredArticle['author_name'] ?? 'LUMA';
              $dateStr = date('d M Y', strtotime($featuredArticle['created_at']));
            ?>
            <div class="blog-featured-author">
              <div class="blog-featured-avatar"><?= strtoupper(mb_substr($authorName, 0, 1)) ?></div>
              <div>
                <span class="blog-featured-author-name">Par <?= htmlspecialchars($authorName) ?></span>
                <span class="blog-featured-date"><?= $dateStr ?> · <?= rand(4, 10) ?> min</span>
              </div>
            </div>
            <div class="blog-featured-arrow">
              <svg width="34" height="34" viewBox="0 0 34 34" fill="none">
                <circle cx="17" cy="17" r="16" stroke="#f0a0bb" stroke-width="1"/>
                <path d="M15 12l5 5-5 5" stroke="#f0a0bb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>
</section>

<?php endif; ?>

<!-- Blog Content -->
<section class="blog-posts-section" id="blog-content">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8">
        <!-- Category filters -->
        <div class="blog-filters mb-5">
          <a href="/blog" class="blog-filter-pill <?= !$category ? 'active' : '' ?>">Tous les articles</a>
          <?php foreach ($categories as $cat): ?>
            <a href="/blog?category=<?= $cat['id'] ?>" class="blog-filter-pill <?= ($category == $cat['id']) ? 'active' : '' ?>"><?= htmlspecialchars($cat['name']) ?></a>
          <?php endforeach; ?>
        </div>

        <?php if (empty($articles)): ?>
          <div class="empty-state">
            <p class="text-white-50">Aucun article pour le moment.</p>
          </div>
        <?php else: ?>
        <!-- Article grid -->
        <div class="row g-4">
          <?php foreach ($articles as $i => $article): ?>
          <div class="col-md-6">
            <a href="/blog/<?= htmlspecialchars($article['slug']) ?>" class="text-decoration-none">
              <article class="blog-card" style="--i:<?= $i ?>">
                <div class="blog-card-img" style="background-image:url(<?= htmlspecialchars($article['image'] ?: 'https://static.codia.ai/image/2026-06-14/N1uMHvLpN1.png') ?>);">
                  <?php if ($article['category_name']): ?>
                    <span class="blog-card-category"><?= htmlspecialchars($article['category_name']) ?></span>
                  <?php endif; ?>
                </div>
                <div class="blog-card-body">
                  <h4 class="blog-card-title"><?= htmlspecialchars($article['title']) ?></h4>
                  <span class="blog-card-date"><?= date('d M Y', strtotime($article['created_at'])) ?> · <?= rand(3, 8) ?> min</span>
                </div>
              </article>
            </a>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php $totalPages = ceil($total / $limit); if ($totalPages > 1): ?>
        <div class="blog-pagination mt-5">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="blog-page-link <?= $page == $i ? 'active' : '' ?>" href="/blog?page=<?= $i ?><?= $category ? '&category='.$category : '' ?>"><?= $i ?></a>
          <?php endfor; ?>
          <?php if ($page < $totalPages): ?>
            <a class="blog-page-link blog-page-next" href="/blog?page=<?= $page + 1 ?><?= $category ? '&category='.$category : '' ?>">
              <span>Charger plus</span>
              <svg width="20" height="12" viewBox="0 0 34 12" fill="none">
                <path d="M0 6H28M28 6L22 1M28 6L22 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <!-- Search -->
        <div class="blog-sidebar-card blog-sidebar-search">
          <h5 class="blog-sidebar-title">Rechercher</h5>
          <form method="GET" action="/blog" class="blog-search-form">
            <input type="text" name="q" class="blog-search-input" placeholder="Rechercher un article..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button type="submit" class="blog-search-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
              </svg>
            </button>
          </form>
        </div>

        <!-- Popular -->
        <?php if (!empty($popular)): ?>
        <div class="blog-sidebar-card">
          <h5 class="blog-sidebar-title">Articles populaires</h5>
          <div class="blog-popular-list">
            <?php foreach ($popular as $pop): ?>
            <a href="/blog/<?= htmlspecialchars($pop['slug']) ?>" class="blog-popular-item">
              <div class="blog-popular-thumb" style="background-image:url(<?= htmlspecialchars($pop['image'] ?: 'https://static.codia.ai/image/2026-06-14/wcQovh7rgV.png') ?>);"></div>
              <div class="blog-popular-info">
                <span class="blog-popular-title"><?= htmlspecialchars($pop['title']) ?></span>
                <span class="blog-popular-date"><?= date('d M Y', strtotime($pop['created_at'])) ?></span>
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
          <p class="blog-newsletter-text">Recevez nos nouveaux articles chaque semaine.</p>
          <p class="blog-newsletter-sub">Des conseils et ressources directement dans votre boite mail.</p>
          <form method="POST" action="/newsletter" class="blog-newsletter-form">
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
