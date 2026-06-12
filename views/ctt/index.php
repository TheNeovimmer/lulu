<div class="row g-4">
  <div class="col-12">
    <h1 class="font-heading mb-4">Tableau de bord — Support <span class="text-light-pink">👋</span></h1>
  </div>

  <div class="col-md-4" data-animate="fade-up">
    <div class="stat-card">
      <i class="stat-icon bi bi-ticket"></i>
      <div class="stat-number"><?= $stats['open_tickets'] ?? 0 ?></div>
      <div class="stat-label">Tickets ouverts</div>
      <div class="stat-accent"></div>
    </div>
  </div>
  <div class="col-md-4" data-animate="fade-up">
    <div class="stat-card">
      <i class="stat-icon bi bi-check-circle"></i>
      <div class="stat-number"><?= $stats['resolved_today'] ?? 0 ?></div>
      <div class="stat-label">Résolus aujourd'hui</div>
      <div class="stat-accent"></div>
    </div>
  </div>
  <div class="col-md-4" data-animate="fade-up">
    <div class="stat-card">
      <i class="stat-icon bi bi-question-circle"></i>
      <div class="stat-number"><?= $stats['faq_entries'] ?? 0 ?></div>
      <div class="stat-label">Entrées FAQ</div>
      <div class="stat-accent"></div>
    </div>
  </div>

  <div class="col-md-8" data-animate="fade-up">
    <div class="card-luma p-4">
      <h5 class="section-title"><i class="bi bi-ticket me-2 text-pink"></i>Tickets récents</h5>
      <div class="divider-accent"></div>
      <?php if (!empty($recent_tickets)): ?>
      <div class="table-responsive">
        <table class="table table-luma">
          <thead>
            <tr>
              <th>#</th>
              <th>Sujet</th>
              <th>De</th>
              <th>Priorité</th>
              <th>Statut</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_tickets as $t): ?>
            <tr>
              <td><?= $t['id'] ?></td>
              <td><?= htmlspecialchars($t['subject']) ?></td>
              <td><?= htmlspecialchars($t['user_name'] ?? '-') ?></td>
              <td>
                <?php if ($t['priority'] === 'urgent'): ?>
                  <span class="badge bg-danger">Urgent</span>
                <?php else: ?>
                  <span class="badge bg-secondary">Normal</span>
                <?php endif; ?>
              </td>
              <td>
                <?php $statusClasses = ['ouvert' => 'bg-success', 'en_cours' => 'bg-warning text-dark', 'résolu' => 'bg-info', 'fermé' => 'bg-secondary']; ?>
                <span class="badge <?= $statusClasses[$t['status']] ?? 'bg-secondary' ?>"><?= ucfirst($t['status']) ?></span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-ticket empty-state-icon"></i>
        <p class="text-white-50">Aucun ticket récent.</p>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-md-4" data-animate="fade-up">
    <div class="card-luma p-4">
      <h5 class="section-title">Actions rapides</h5>
      <div class="divider-accent"></div>
      <div class="d-grid gap-2">
        <a href="/ctt/tickets" class="btn btn-luma"><i class="bi bi-ticket me-2"></i>Voir tickets</a>
        <a href="/ctt/faq" class="btn btn-luma"><i class="bi bi-question-circle me-2"></i>Gérer FAQ</a>
        <a href="/ctt/rapports" class="btn btn-luma"><i class="bi bi-bar-chart me-2"></i>Rapports</a>
      </div>
    </div>
  </div>
</div>
