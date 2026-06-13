<div>
  <div class="welcome-card-dashboard">
    <h2>Tableau de bord — Support 👋</h2>
    <p>Vue d'ensemble de l'activité support.</p>
  </div>

  <div class="stats-row-dashboard">
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-ticket"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $stats['open_tickets'] ?? 0 ?></span>
        <span class="stat-card-label">Tickets ouverts</span>
      </div>
    </div>
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-check-circle"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $stats['resolved_today'] ?? 0 ?></span>
        <span class="stat-card-label">Résolus aujourd'hui</span>
      </div>
    </div>
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-question-circle"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $stats['faq_entries'] ?? 0 ?></span>
        <span class="stat-card-label">Entrées FAQ</span>
      </div>
    </div>
  </div>

  <div class="row-dashboard">
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title"><i class="bi bi-ticket me-2"></i>Tickets récents</h5>
      </div>
      <div class="card-dashboard-body">
        <?php if (!empty($recent_tickets)): ?>
        <div class="table-wrapper">
          <table class="table-dashboard">
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
                    <span class="badge-dashboard danger">Urgent</span>
                  <?php else: ?>
                    <span class="badge-dashboard info">Normal</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php $statusClasses = ['ouvert' => 'success', 'en_cours' => 'warning', 'résolu' => 'info', 'fermé' => 'danger']; ?>
                  <span class="badge-dashboard <?= $statusClasses[$t['status']] ?? 'info' ?>"><?= ucfirst($t['status']) ?></span>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div class="empty-state-dashboard">
          <i class="bi bi-ticket"></i>
          <h5>Aucun ticket</h5>
          <p>Aucun ticket récent.</p>
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
          <a href="/ctt/tickets" class="quick-action-card">
            <i class="bi bi-ticket"></i>
            <span>Voir tickets</span>
          </a>
          <a href="/ctt/faq" class="quick-action-card">
            <i class="bi bi-question-circle"></i>
            <span>Gérer FAQ</span>
          </a>
          <a href="/ctt/rapports" class="quick-action-card">
            <i class="bi bi-bar-chart"></i>
            <span>Rapports</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
