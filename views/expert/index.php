<div class="row g-4">
  <div class="col-12">
    <h1 class="font-heading mb-4">Bonjour, <?= htmlspecialchars($user['first_name']) ?> <span class="text-light-pink">👋</span></h1>
  </div>

  <div class="col-md-4" data-animate="fade-up">
    <div class="stat-card">
      <i class="stat-icon bi bi-question-circle"></i>
      <div class="stat-number"><?= $stats['pending_questions'] ?? 0 ?></div>
      <div class="stat-label">Questions en attente</div>
      <div class="stat-accent"></div>
    </div>
  </div>
  <div class="col-md-4" data-animate="fade-up">
    <div class="stat-card">
      <i class="stat-icon bi bi-file-text"></i>
      <div class="stat-number"><?= $stats['published_articles'] ?? 0 ?></div>
      <div class="stat-label">Articles publiés</div>
      <div class="stat-accent"></div>
    </div>
  </div>
  <div class="col-md-4" data-animate="fade-up">
    <div class="stat-card">
      <i class="stat-icon bi bi-ticket"></i>
      <div class="stat-number"><?= $stats['assigned_tickets'] ?? 0 ?></div>
      <div class="stat-label">Tickets assignés</div>
      <div class="stat-accent"></div>
    </div>
  </div>

  <div class="col-md-8" data-animate="fade-up">
    <div class="card-luma p-4">
      <h5 class="section-title"><i class="bi bi-question-circle me-2 text-pink"></i>Questions récentes</h5>
      <div class="divider-accent"></div>
      <?php if (!empty($questions)): ?>
        <?php foreach ($questions as $q): ?>
        <div class="border-bottom border-secondary pb-3 mb-3">
          <a href="/expert/questions#question-<?= $q['id'] ?>" class="text-white text-decoration-none fw-semibold"><?= htmlspecialchars($q['title']) ?></a>
          <p class="text-white-50 small mb-0">Par <?= htmlspecialchars($q['author_name']) ?> — <?= date('d/m/Y', strtotime($q['created_at'])) ?></p>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-check-circle empty-state-icon"></i>
        <p class="text-white-50">Aucune question en attente.</p>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-md-4" data-animate="fade-up">
    <div class="card-luma p-4">
      <h5 class="section-title">Actions rapides</h5>
      <div class="divider-accent"></div>
      <div class="d-grid gap-2">
        <a href="/expert/questions" class="btn btn-luma"><i class="bi bi-question-circle me-2"></i>Répondre aux questions</a>
        <a href="/expert/articles" class="btn btn-luma"><i class="bi bi-pencil me-2"></i>Écrire un article</a>
        <a href="/expert/ressources" class="btn btn-luma"><i class="bi bi-book me-2"></i>Voir ressources</a>
      </div>
    </div>
  </div>
</div>
