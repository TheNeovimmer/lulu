<section class="py-5" data-animate="fade-up">
  <div class="container">
    <h1 class="section-title" style="text-align:left;">Le blog <span class="text-light-pink">LUMA</span></h1>
    <p class="section-subtitle" style="text-align:left; font-size:22px; color:white; font-weight:300;">
      Des conseils, des expériences et du soutien à chaque étape
    </p>

    <div class="d-flex gap-2 flex-wrap mb-4" data-animate="fade-up">
      <a href="/blog" class="filter-pill <?= !$category ? 'active' : '' ?>">Tous les articles</a>
      <?php foreach ($categories as $cat): ?>
        <a href="/blog?category=<?= $cat['id'] ?>" class="filter-pill <?= ($category == $cat['id']) ? 'active' : '' ?>"><?= htmlspecialchars($cat['name']) ?></a>
      <?php endforeach; ?>
    </div>

    <?php if (empty($articles)): ?>
      <div class="empty-state">
        <p class="text-white-50">Aucun article pour le moment.</p>
      </div>
    <?php else: ?>
    <div class="animate-stagger row g-4">
      <?php foreach ($articles as $article): ?>
      <div class="col-md-4">
        <a href="/blog/<?= htmlspecialchars($article['slug']) ?>" class="text-decoration-none">
          <div class="card bg-luma-glass border-pink h-100 blog-card">
            <?php if ($article['image']): ?>
              <img src="<?= htmlspecialchars($article['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($article['title']) ?>">
            <?php endif; ?>
            <div class="card-body">
              <?php if ($article['category_name']): ?>
                <span class="badge bg-luma text-white mb-2"><?= htmlspecialchars($article['category_name']) ?></span>
              <?php endif; ?>
              <h5 class="card-title text-white"><?= htmlspecialchars($article['title']) ?></h5>
              <small class="text-light-pink"><?= date('d M Y', strtotime($article['created_at'])) ?></small>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>

    <?php $totalPages = ceil($total / $limit); if ($totalPages > 1): ?>
    <nav class="mt-4">
      <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= $page == $i ? 'active' : '' ?>">
            <a class="page-link bg-luma-card border-pink text-white" href="/blog?page=<?= $i ?><?= $category ? '&category='.$category : '' ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
