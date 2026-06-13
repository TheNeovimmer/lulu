<div>
  <div class="welcome-card-dashboard">
    <h2>Bonjour, <?= htmlspecialchars($user['first_name']) ?> 👋</h2>
    <p>Bienvenue sur votre tableau de bord.</p>
  </div>

  <div class="stats-row-dashboard">
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-question-circle"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $stats['pending_questions'] ?? 0 ?></span>
        <span class="stat-card-label">Questions en attente</span>
      </div>
    </div>
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-file-text"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $stats['published_articles'] ?? 0 ?></span>
        <span class="stat-card-label">Articles publiés</span>
      </div>
    </div>
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-ticket"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $stats['assigned_tickets'] ?? 0 ?></span>
        <span class="stat-card-label">Tickets assignés</span>
      </div>
    </div>
  </div>

  <div class="row-dashboard">
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title"><i class="bi bi-question-circle me-2"></i>Questions récentes</h5>
      </div>
      <div class="card-dashboard-body">
        <?php if (!empty($questions)): ?>
          <?php foreach ($questions as $q): ?>
          <div class="border-bottom pb-3 mb-3">
            <a href="/expert/questions#question-<?= $q['id'] ?>" class="fw-semibold text-dark text-decoration-none"><?= htmlspecialchars($q['title']) ?></a>
            <p class="text-muted small mb-0">Par <?= htmlspecialchars($q['author_name']) ?> — <?= date('d/m/Y', strtotime($q['created_at'])) ?></p>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state-dashboard">
          <i class="bi bi-check-circle"></i>
          <p>Aucune question en attente.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Actions rapides</h5>
      </div>
      <div class="card-dashboard-body">
        <div class="quick-actions-dashboard">
          <a href="/expert/questions" class="quick-action-card">
            <i class="bi bi-question-circle"></i>
            <span>Répondre aux questions</span>
          </a>
          <a href="/expert/articles" class="quick-action-card">
            <i class="bi bi-pencil"></i>
            <span>Écrire un article</span>
          </a>
          <a href="/expert/ressources" class="quick-action-card">
            <i class="bi bi-book"></i>
            <span>Voir ressources</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
