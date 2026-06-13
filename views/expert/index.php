<div>
  <div class="welcome-card-dashboard">
    <h2>Bonjour, <?= htmlspecialchars($first_name ?? '') ?> 👋</h2>
    <p>Bienvenue sur votre tableau de bord.</p>
  </div>

  <div class="stats-row-dashboard">
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-question-circle"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $pendingQuestions ?? 0 ?></span>
        <span class="stat-card-label">Questions en attente</span>
      </div>
    </div>
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-file-text"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $articlesCount ?? 0 ?></span>
        <span class="stat-card-label">Articles publiés</span>
      </div>
    </div>
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-ticket"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $assignedTickets ?? 0 ?></span>
        <span class="stat-card-label">Tickets assignés</span>
      </div>
    </div>
  </div>

  <div class="row-dashboard">
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
