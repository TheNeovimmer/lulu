<div class="container py-5" style="max-width:900px;" data-animate="fade-up">
  <a href="/ressources" class="text-light-pink text-decoration-none mb-3 d-inline-block"><i class="bi bi-arrow-left me-1"></i>Retour aux ressources</a>

  <?php if ($resource['category_name']): ?>
  <span class="badge bg-pink mb-3"><?= htmlspecialchars($resource['category_name']) ?></span>
  <?php endif; ?>

  <h1 class="font-heading" style="font-size:48px;"><?= htmlspecialchars($resource['title']) ?></h1>
  <p class="text-white-50 mb-4"><?= htmlspecialchars($resource['description'] ?? '') ?></p>

  <?php if ($resource['file_url']): ?>
  <a href="<?= htmlspecialchars($resource['file_url']) ?>" class="btn btn-luma btn-lg" target="_blank" <?= strpos($resource['file_url'] ?? '', '.pdf') !== false ? 'download' : '' ?>>
    <i class="bi bi-download me-2"></i>Télécharger la ressource
  </a>
  <?php endif; ?>
</div>