<div class="row g-4">
  <div class="col-md-8" data-animate="fade-up">
    <h1 class="font-heading mb-4">Bonjour, <?= htmlspecialchars($user['first_name']) ?> <span class="text-light-pink">👋</span></h1>

    <?php if (!empty($pregnancy)): ?>
    <div class="card-luma p-4 mb-4" data-animate="fade-up">
      <div class="d-flex align-items-center gap-3 mb-3">
        <i class="bi bi-flower1 text-pink fs-1"></i>
        <div>
          <h5 class="mb-1 font-heading">Ma Grossesse</h5>
          <small class="text-white-50"><?= $pregnancy['weeks'] ?> semaines de grossesse</small>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-6">
          <div class="stat-card">
            <i class="stat-icon bi bi-calendar-heart"></i>
            <div class="stat-number"><?= date('d/m/Y', strtotime($pregnancy['due_date'])) ?></div>
            <div class="stat-label">Date prévue d'accouchement</div>
            <div class="stat-accent"></div>
          </div>
        </div>
        <div class="col-6">
          <div class="stat-card">
            <i class="stat-icon bi bi-clock"></i>
            <div class="stat-number text-pink"><?= $pregnancy['days_remaining'] ?> jours</div>
            <div class="stat-label">Jours restants</div>
            <div class="stat-accent"></div>
          </div>
        </div>
      </div>
      <a href="/dashboard/grossesse" class="btn btn-luma btn-sm mt-3">Voir détails</a>
    </div>
    <?php endif; ?>

    <?php if (!empty($baby)): ?>
    <div class="card-luma p-4 mb-4" data-animate="fade-up">
      <div class="d-flex align-items-center gap-3 mb-3">
        <i class="bi bi-emoji-smile text-pink fs-1"></i>
        <div>
          <h5 class="mb-1 font-heading">Mon Bébé</h5>
          <small class="text-white-50"><?= htmlspecialchars($baby['name']) ?></small>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-4">
          <div class="stat-card">
            <i class="stat-icon bi bi-calendar"></i>
            <div class="stat-number"><?= $baby['age_months'] ?> mois</div>
            <div class="stat-label">Âge</div>
            <div class="stat-accent"></div>
          </div>
        </div>
        <div class="col-4">
          <div class="stat-card">
            <i class="stat-icon bi bi-speedometer2"></i>
            <div class="stat-number"><?= number_format($baby['last_weight'], 2) ?> kg</div>
            <div class="stat-label">Poids</div>
            <div class="stat-accent"></div>
          </div>
        </div>
        <div class="col-4">
          <div class="stat-card">
            <i class="stat-icon bi bi-arrows-vertical"></i>
            <div class="stat-number"><?= number_format($baby['last_height'], 1) ?> cm</div>
            <div class="stat-label">Taille</div>
            <div class="stat-accent"></div>
          </div>
        </div>
      </div>
      <a href="/dashboard/bebe" class="btn btn-luma btn-sm mt-3">Voir détails</a>
    </div>
    <?php endif; ?>

    <div class="card-luma p-4 mb-4" data-animate="fade-up">
      <h5 class="font-heading mb-3"><i class="bi bi-journal-text me-2 text-pink"></i>Articles récents</h5>
      <?php if (!empty($articles)): ?>
        <?php foreach ($articles as $article): ?>
        <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom border-secondary">
          <div>
            <a href="/blog/<?= $article['id'] ?>" class="text-white text-decoration-none fw-semibold"><?= htmlspecialchars($article['title']) ?></a>
            <p class="text-white-50 small mb-0"><?= date('d/m/Y', strtotime($article['created_at'])) ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-state">
          <i class="bi bi-journal-text empty-state-icon"></i>
          <p class="text-white-50">Aucun article récent.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-md-4" data-animate="fade-up">
    <div class="card-luma p-4 mb-4">
      <h5 class="font-heading mb-3">Actions rapides</h5>
      <div class="d-grid gap-2 animate-stagger">
        <a href="/dashboard/grossesse" class="btn btn-luma" data-animate="fade-up"><i class="bi bi-flower1 me-2"></i>Suivi grossesse</a>
        <a href="/dashboard/bebe" class="btn btn-luma" data-animate="fade-up"><i class="bi bi-emoji-smile me-2"></i>Mon bébé</a>
        <a href="/dashboard/croissance" class="btn btn-luma" data-animate="fade-up"><i class="bi bi-graph-up me-2"></i>Croissance</a>
        <a href="/dashboard/vaccination" class="btn btn-luma" data-animate="fade-up"><i class="bi bi-shield-check me-2"></i>Vaccination</a>
      </div>
    </div>

    <div class="card-luma p-4" data-animate="fade-up">
      <h5 class="font-heading mb-3"><i class="bi bi-bell me-2 text-pink"></i>Notifications</h5>
      <?php if (isset($notification_count) && $notification_count > 0): ?>
        <p class="stat-number text-pink mb-2"><?= $notification_count ?> non lues</p>
        <a href="/dashboard/notifications" class="btn btn-outline-luma btn-sm w-100">Voir toutes</a>
      <?php else: ?>
        <div class="empty-state">
          <i class="bi bi-bell-slash empty-state-icon"></i>
          <p class="text-white-50 mb-0">Aucune notification.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
