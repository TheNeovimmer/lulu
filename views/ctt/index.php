<div>
  <div class="welcome-card-dashboard">
    <h2>Tableau de bord — Support</h2>
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
      <div class="stat-card-icon"><i class="bi bi-play-fill"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $stats['in_progress'] ?? 0 ?></span>
        <span class="stat-card-label">En cours</span>
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
      <div class="stat-card-icon"><i class="bi bi-check-all"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $stats['resolved_this_month'] ?? 0 ?></span>
        <span class="stat-card-label">Résolus ce mois</span>
      </div>
    </div>
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-clock-history"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number" style="font-size:1rem"><?= $stats['avg_response_time'] ?? 'N/A' ?></span>
        <span class="stat-card-label">Temps réponse moyen</span>
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

  <?php if (!empty($monthlyTickets)): ?>
  <div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
      <h5 class="card-dashboard-title"><i class="bi bi-bar-chart me-2"></i>Tickets par mois</h5>
    </div>
    <div class="card-dashboard-body">
      <div style="display:flex;align-items:end;gap:10px;height:100px;">
        <?php $maxCount = max(array_column($monthlyTickets, 'count')); ?>
        <?php foreach ($monthlyTickets as $m): ?>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;height:100%;justify-content:end;">
          <div style="width:100%;background:var(--dprimary);border-radius:4px 4px 0 0;min-height:3px;height:<?= $maxCount > 0 ? max(3, round($m['count'] / $maxCount * 100)) : 3 ?>%;"
               title="<?= $m['month'] ?>: <?= $m['count'] ?> tickets"></div>
          <small style="font-size:9px;color:var(--dtext-muted);margin-top:4px;"><?= substr($m['month'], 5, 2) ?></small>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-md-8">
      <div class="card-dashboard">
        <div class="card-dashboard-header">
          <h5 class="card-dashboard-title"><i class="bi bi-ticket me-2"></i>Tickets récents</h5>
          <a href="/ctt/tickets" class="btn-dashboard btn-dashboard-outline btn-dashboard-sm">Voir tout</a>
        </div>
        <div class="card-dashboard-body">
          <?php if (!empty($recentTickets)): ?>
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
                <?php foreach ($recentTickets as $t): ?>
                <tr>
                  <td><?= $t['id'] ?></td>
                  <td style="font-weight:600"><?= htmlspecialchars($t['subject']) ?></td>
                  <td><?= htmlspecialchars($t['user_name'] ?? '-') ?></td>
                  <td>
                    <?php if ($t['priority'] === 'high'): ?>
                      <span class="badge-dashboard danger">Haute</span>
                    <?php elseif ($t['priority'] === 'low'): ?>
                      <span class="badge-dashboard info">Basse</span>
                    <?php else: ?>
                      <span class="badge-dashboard warning">Normale</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php
                    $statusClasses = ['open' => 'danger', 'in_progress' => 'warning', 'closed' => 'success'];
                    $statusLabels = ['open' => 'Ouvert', 'in_progress' => 'En cours', 'closed' => 'Fermé'];
                    ?>
                    <span class="badge-dashboard <?= $statusClasses[$t['status']] ?? 'info' ?>">
                      <?= $statusLabels[$t['status']] ?? ucfirst($t['status']) ?>
                    </span>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php else: ?>
          <div class="empty-state-dashboard">
            <i class="bi bi-ticket"></i>
            <p>Aucun ticket récent.</p>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card-dashboard mb-4">
        <div class="card-dashboard-header">
          <h5 class="card-dashboard-title">Priorités des tickets ouverts</h5>
        </div>
        <div class="card-dashboard-body">
          <?php $openTotal = max(1, ($stats['high_priority'] ?? 0) + ($stats['medium_priority'] ?? 0) + ($stats['low_priority'] ?? 0)); ?>
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <small style="color:#dc3545">Haute</small>
              <strong><?= $stats['high_priority'] ?? 0 ?></strong>
            </div>
            <div class="progress" style="height:8px;background:var(--dborder-light)">
              <div class="progress-bar bg-danger" style="width:<?= round(($stats['high_priority'] ?? 0) / $openTotal * 100) ?>%"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <small style="color:#ffc107">Normale</small>
              <strong><?= $stats['medium_priority'] ?? 0 ?></strong>
            </div>
            <div class="progress" style="height:8px;background:var(--dborder-light)">
              <div class="progress-bar bg-warning" style="width:<?= round(($stats['medium_priority'] ?? 0) / $openTotal * 100) ?>%"></div>
            </div>
          </div>
          <div>
            <div class="d-flex justify-content-between mb-1">
              <small style="color:#0dcaf0">Basse</small>
              <strong><?= $stats['low_priority'] ?? 0 ?></strong>
            </div>
            <div class="progress" style="height:8px;background:var(--dborder-light)">
              <div class="progress-bar bg-info" style="width:<?= round(($stats['low_priority'] ?? 0) / $openTotal * 100) ?>%"></div>
            </div>
          </div>
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
</div>
