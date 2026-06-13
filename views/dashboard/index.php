<div class="row g-4">
  <div class="col-md-8">
    <div class="welcome-card-dashboard mb-4">
      <h2>Bonjour, <?= htmlspecialchars($user['first_name']) ?></h2>
      <p>Bienvenue sur votre espace personnel LUMA.</p>
    </div>

    <?php if (!empty($pregnancy)): ?>
    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title"><i class="bi bi-flower1 me-2"></i>Ma Grossesse</h5>
        <small style="color: var(--dtext-muted);"><?= $pregnancy['weeks'] ?> semaines de grossesse</small>
      </div>
      <div class="card-dashboard-body">
        <div class="row g-3">
          <div class="col-6">
            <div class="stat-card-dashboard">
              <div class="stat-card-icon"><i class="bi bi-calendar-heart"></i></div>
              <div class="stat-card-info">
                <span class="stat-card-number"><?= date('d/m/Y', strtotime($pregnancy['due_date'])) ?></span>
                <span class="stat-card-label">Date prévue d'accouchement</span>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="stat-card-dashboard">
              <div class="stat-card-icon"><i class="bi bi-clock"></i></div>
              <div class="stat-card-info">
                <span class="stat-card-number" style="color: var(--dprimary);"><?= $pregnancy['days_remaining'] ?> jours</span>
                <span class="stat-card-label">Jours restants</span>
              </div>
            </div>
          </div>
        </div>
        <a href="/dashboard/grossesse" class="btn-dashboard btn-dashboard-primary btn-dashboard-sm mt-3">Voir détails</a>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($baby)): ?>
    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title"><i class="bi bi-emoji-smile me-2"></i>Mon Bébé</h5>
        <small style="color: var(--dtext-muted);"><?= htmlspecialchars($baby['name']) ?></small>
      </div>
      <div class="card-dashboard-body">
        <div class="row g-3">
          <div class="col-4">
            <div class="stat-card-dashboard">
              <div class="stat-card-icon"><i class="bi bi-calendar"></i></div>
              <div class="stat-card-info">
                <span class="stat-card-number"><?= $baby['age_months'] ?> mois</span>
                <span class="stat-card-label">Âge</span>
              </div>
            </div>
          </div>
          <div class="col-4">
            <div class="stat-card-dashboard">
              <div class="stat-card-icon"><i class="bi bi-speedometer2"></i></div>
              <div class="stat-card-info">
                <span class="stat-card-number"><?= number_format($baby['last_weight'], 2) ?> kg</span>
                <span class="stat-card-label">Poids</span>
              </div>
            </div>
          </div>
          <div class="col-4">
            <div class="stat-card-dashboard">
              <div class="stat-card-icon"><i class="bi bi-arrows-vertical"></i></div>
              <div class="stat-card-info">
                <span class="stat-card-number"><?= number_format($baby['last_height'], 1) ?> cm</span>
                <span class="stat-card-label">Taille</span>
              </div>
            </div>
          </div>
        </div>
        <a href="/dashboard/bebe" class="btn-dashboard btn-dashboard-primary btn-dashboard-sm mt-3">Voir détails</a>
      </div>
    </div>
    <?php endif; ?>

    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title"><i class="bi bi-journal-text me-2"></i>Articles récents</h5>
      </div>
      <div class="card-dashboard-body">
        <?php if (!empty($articles)): ?>
          <?php foreach ($articles as $article): ?>
          <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom" style="border-color: var(--dborder-light) !important;">
            <div>
              <a href="/blog/<?= htmlspecialchars($article['slug'] ?? $article['id']) ?>" class="text-decoration-none fw-semibold" style="color: var(--dtext-dark);"><?= htmlspecialchars($article['title']) ?></a>
              <p style="color: var(--dtext-muted); font-size: 0.85rem; margin-bottom: 0;"><?= date('d/m/Y', strtotime($article['created_at'])) ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state-dashboard">
            <i class="bi bi-journal-text"></i>
            <p>Aucun article récent.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Actions rapides</h5>
      </div>
      <div class="card-dashboard-body">
        <div class="quick-actions-dashboard">
          <a href="/dashboard/grossesse" class="quick-action-card"><i class="bi bi-flower1"></i><span>Suivi grossesse</span></a>
          <a href="/dashboard/bebe" class="quick-action-card"><i class="bi bi-emoji-smile"></i><span>Mon bébé</span></a>
          <a href="/dashboard/croissance" class="quick-action-card"><i class="bi bi-graph-up"></i><span>Croissance</span></a>
          <a href="/dashboard/vaccination" class="quick-action-card"><i class="bi bi-shield-check"></i><span>Vaccination</span></a>
        </div>
      </div>
    </div>

    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title"><i class="bi bi-bell me-2"></i>Notifications</h5>
      </div>
      <div class="card-dashboard-body">
        <?php if (isset($notifCount) && $notifCount > 0): ?>
          <span class="stat-card-number" style="color: var(--dprimary);"><?= $notifCount ?> non lues</span>
          <a href="/dashboard/notifications" class="btn-dashboard btn-dashboard-outline btn-dashboard-sm w-100 mt-2">Voir toutes</a>
        <?php else: ?>
          <div class="empty-state-dashboard">
            <i class="bi bi-bell-slash"></i>
            <p class="mb-0">Aucune notification.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
