<div class="container py-5" data-animate="fade-up">
  <h1 class="font-heading mb-2">Ressources</h1>
  <p class="text-white-50 mb-4">Guides, fiches pratiques et documents téléchargeables pour vous accompagner.</p>

  <div class="d-flex flex-wrap gap-2 mb-4" data-animate="fade-up">
    <a href="?" class="filter-pill <?= empty($_GET['category']) ? 'active' : '' ?>">Toutes</a>
    <?php foreach ($categories as $cat): ?>
      <a href="?category=<?= $cat['id'] ?>" class="filter-pill <?= ($_GET['category'] ?? '') == $cat['id'] ? 'active' : '' ?>"><?= htmlspecialchars($cat['name']) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (!empty($resources)): ?>
  <div class="animate-stagger row g-4">
    <?php foreach ($resources as $r): ?>
    <div class="col-md-6 col-lg-4">
      <div class="card-luma p-4 h-100 d-flex flex-column">
        <div class="mb-2">
          <span class="badge bg-pink"><?= htmlspecialchars($r['category_name'] ?? 'Général') ?></span>
        </div>
        <h5 class="font-heading"><?= htmlspecialchars($r['title']) ?></h5>
        <p class="text-white-50 small flex-grow-1"><?= htmlspecialchars($r['description']) ?></p>
        <a href="<?= $r['file_url'] ? htmlspecialchars($r['file_url']) : '#' ?>" class="btn btn-luma btn-sm" target="_blank" <?= strpos($r['file_url'] ?? '', '.pdf') !== false ? 'download' : '' ?>>
          <i class="bi bi-download me-1"></i><?= $r['file_url'] ? 'Télécharger' : 'Voir la ressource' ?>
        </a>
      </div>
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
