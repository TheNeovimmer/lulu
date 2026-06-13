<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="welcome-card-dashboard">
    <h2>Bonjour, Admin</h2>
    <p>Vue d'ensemble de votre plateforme LUMA</p>
  </div>

  <div class="stats-row-dashboard">
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-people"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $stats['users_total'] ?></span>
        <span class="stat-card-label">Utilisateurs total</span>
      </div>
    </div>
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-person-check"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $stats['mamans'] ?></span>
        <span class="stat-card-label">Mamans inscrites</span>
      </div>
    </div>
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-flower1"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $stats['mamans_pregnant'] ?></span>
        <span class="stat-card-label">Grossesses actives</span>
      </div>
    </div>
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-ticket"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= $stats['tickets_open'] ?></span>
        <span class="stat-card-label">Tickets ouverts</span>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card-dashboard h-100">
        <div class="card-dashboard-header">
          <h5 class="card-dashboard-title"><i class="bi bi-graph-up me-2"></i>Activité</h5>
        </div>
        <div class="card-dashboard-body">
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <small>Articles publiés</small>
              <strong><?= $stats['articles_published'] ?> / <?= $stats['articles'] ?></strong>
            </div>
            <div class="progress" style="height:6px;background:var(--dborder-light)">
              <div class="progress-bar" style="width:<?= $stats['articles'] > 0 ? round($stats['articles_published'] / $stats['articles'] * 100) : 0 ?>%;background:var(--dprimary)"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <small>Rendez-vous en attente</small>
              <strong><?= $stats['appointments_pending'] ?> / <?= $stats['appointments_total'] ?></strong>
            </div>
            <div class="progress" style="height:6px;background:var(--dborder-light)">
              <div class="progress-bar bg-warning" style="width:<?= $stats['appointments_total'] > 0 ? round($stats['appointments_pending'] / $stats['appointments_total'] * 100) : 0 ?>%"></div>
            </div>
          </div>
          <div>
            <div class="d-flex justify-content-between mb-1">
              <small>Messages non lus</small>
              <strong><?= $stats['contacts_unread'] ?></strong>
            </div>
          </div>
          <div class="mt-3 pt-3 border-top" style="border-color:var(--dborder-light)">
            <div class="d-flex justify-content-between">
              <small>Inscrits ce mois</small>
              <strong style="color:var(--dprimary)">+<?= $stats['registrations_this_month'] ?></strong>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card-dashboard h-100">
        <div class="card-dashboard-header">
          <h5 class="card-dashboard-title"><i class="bi bi-calendar3 me-2"></i>Inscriptions (12 mois)</h5>
        </div>
        <div class="card-dashboard-body">
          <?php if (!empty($monthlyRegistrations)): ?>
          <div style="display:flex;align-items:end;gap:6px;height:120px;padding-top:10px;">
            <?php $maxCount = max(array_column($monthlyRegistrations, 'count')); ?>
            <?php foreach ($monthlyRegistrations as $m): ?>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;height:100%;justify-content:end;">
              <div style="width:100%;background:var(--dprimary);border-radius:4px 4px 0 0;transition:height .3s;min-height:2px;"
                   title="<?= $m['month'] ?>: <?= $m['count'] ?>"
                   data-count="<?= $m['count'] ?>"
                   data-max="<?= $maxCount ?>"
                   style="height: <?= $maxCount > 0 ? max(2, round($m['count'] / $maxCount * 100)) : 2 ?>%;"
                   onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
              </div>
              <small style="font-size:9px;color:var(--dtext-muted);margin-top:4px;writing-mode:vertical-lr;text-orientation:mixed;transform:rotate(180deg);"><?= substr($m['month'], 5, 2) ?></small>
            </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <div class="empty-state-dashboard">
            <i class="bi bi-bar-chart"></i>
            <p>Aucune donnée d'inscription sur les 12 derniers mois.</p>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-7">
      <div class="card-dashboard">
        <div class="card-dashboard-header">
          <h5 class="card-dashboard-title">Derniers utilisateurs</h5>
          <a href="/admin/utilisateurs" class="btn-dashboard btn-dashboard-outline btn-dashboard-sm">Voir tout</a>
        </div>
        <div class="table-wrapper">
          <table class="table-dashboard">
            <thead>
              <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Inscrit le</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($recentUsers)): ?>
              <?php foreach ($recentUsers as $user): ?>
              <tr>
                <td style="font-weight:600"><?= htmlspecialchars($user['name']) ?></td>
                <td class="td-muted"><?= htmlspecialchars($user['email']) ?></td>
                <td><span class="badge-dashboard info"><?= htmlspecialchars($user['role_name'] ?? '-') ?></span></td>
                <td>
                  <?php if ($user['status'] === 'active'): ?>
                    <span class="badge-dashboard success">Actif</span>
                  <?php elseif ($user['status'] === 'suspended'): ?>
                    <span class="badge-dashboard danger">Suspendu</span>
                  <?php else: ?>
                    <span class="badge-dashboard warning"><?= htmlspecialchars($user['status']) ?></span>
                  <?php endif; ?>
                </td>
                <td class="td-muted"><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
              <?php else: ?>
              <tr><td colspan="5" class="text-center text-muted py-3">Aucun utilisateur</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <div class="card-dashboard">
        <div class="card-dashboard-header">
          <h5 class="card-dashboard-title">Actions rapides</h5>
        </div>
        <div class="quick-actions-dashboard">
          <a href="/admin/articles/create" class="quick-action-card">
            <i class="bi bi-file-text"></i>
            <span>Nouvel article</span>
          </a>
          <a href="/admin/categories" class="quick-action-card">
            <i class="bi bi-tags"></i>
            <span>Catégories</span>
          </a>
          <a href="/admin/tickets" class="quick-action-card">
            <i class="bi bi-ticket"></i>
            <span>Tickets (<?= $stats['tickets_open'] ?> ouverts)</span>
          </a>
          <a href="/admin/contacts" class="quick-action-card">
            <i class="bi bi-envelope"></i>
            <span>Messages (<?= $stats['contacts_unread'] ?> non lus)</span>
          </a>
          <a href="/admin/utilisateurs" class="quick-action-card">
            <i class="bi bi-people"></i>
            <span>Utilisateurs</span>
          </a>
          <a href="/admin/experts" class="quick-action-card">
            <i class="bi bi-person-badge"></i>
            <span>Experts</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
