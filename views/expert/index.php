<div>
  <div class="welcome-card-dashboard">
    <h2>Bonjour, <?= htmlspecialchars($firstName) ?></h2>
    <p>Bienvenue sur votre tableau de bord.</p>
  </div>

  <div class="stats-row-dashboard">
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-calendar-check"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $appointmentsCount ?? 0 ?></span>
        <span class="stat-card-label">Rendez-vous en attente</span>
      </div>
    </div>
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
        <span class="stat-card-label">Tickets actifs</span>
      </div>
    </div>
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-eye"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= number_format($totalViews ?? 0) ?></span>
        <span class="stat-card-label">Vues totales articles</span>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <?php if (!empty($upcomingAppointments)): ?>
    <div class="col-md-7">
      <div class="card-dashboard">
        <div class="card-dashboard-header">
          <h5 class="card-dashboard-title"><i class="bi bi-calendar-week me-2"></i>Prochains rendez-vous</h5>
          <a href="/expert/agenda" class="btn-dashboard btn-dashboard-outline btn-dashboard-sm">Voir tout</a>
        </div>
        <div class="card-dashboard-body">
          <div class="table-wrapper">
            <table class="table-dashboard">
              <thead>
                <tr>
                  <th>Maman</th>
                  <th>Date</th>
                  <th>Type</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($upcomingAppointments as $a): ?>
                <tr>
                  <td style="font-weight:600"><?= htmlspecialchars($a['mother_name'] ?? 'Maman') ?></td>
                  <td><?= date('d/m/Y H:i', strtotime($a['appointment_date'])) ?></td>
                  <td>
                    <span class="badge-dashboard <?= $a['type'] === 'online' ? 'info' : '' ?>">
                      <?= $a['type'] === 'online' ? 'En ligne' : 'Cabinet' ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($a['status'] === 'pending'): ?>
                      <span class="badge-dashboard warning">En attente</span>
                    <?php else: ?>
                      <span class="badge-dashboard success">Confirmé</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div class="col-md-<?= !empty($upcomingAppointments) ? 5 : 12 ?>">
      <?php if (!empty($monthlyAppointments)): ?>
      <div class="card-dashboard h-100">
        <div class="card-dashboard-header">
          <h5 class="card-dashboard-title"><i class="bi bi-bar-chart me-2"></i>Consultations (6 mois)</h5>
        </div>
        <div class="card-dashboard-body">
          <div style="display:flex;align-items:end;gap:8px;height:100px;">
            <?php $maxCount = max(array_column($monthlyAppointments, 'count')); ?>
            <?php foreach ($monthlyAppointments as $m): ?>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;height:100%;justify-content:end;">
              <div style="width:100%;background:var(--dprimary);border-radius:4px 4px 0 0;height:<?= $maxCount > 0 ? max(3, round($m['count'] / $maxCount * 100)) : 3 ?>%;">
              </div>
              <small style="font-size:9px;color:var(--dtext-muted);margin-top:4px;"><?= substr($m['month'], 5, 2) ?></small>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="row-dashboard">
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Actions rapides</h5>
      </div>
      <div class="card-dashboard-body">
        <div class="quick-actions-dashboard">
          <a href="/expert/agenda" class="quick-action-card">
            <i class="bi bi-calendar-check"></i>
            <span>Gérer agenda</span>
          </a>
          <a href="/expert/disponibilites" class="quick-action-card">
            <i class="bi bi-clock"></i>
            <span>Mes disponibilités</span>
          </a>
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
