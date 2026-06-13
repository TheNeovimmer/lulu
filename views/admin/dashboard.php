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
        <span class="stat-card-number"><?= htmlspecialchars($stats['users']) ?></span>
        <span class="stat-card-label">Utilisateurs</span>
      </div>
    </div>
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-file-text"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= htmlspecialchars($stats['articles']) ?></span>
        <span class="stat-card-label">Articles</span>
      </div>
    </div>
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-ticket"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= htmlspecialchars($stats['tickets_open']) ?></span>
        <span class="stat-card-label">Tickets ouverts</span>
      </div>
    </div>
    <div class="stat-card-dashboard">
      <div class="stat-card-icon"><i class="bi bi-envelope"></i></div>
      <div class="stat-card-info">
        <span class="stat-card-number"><?= htmlspecialchars($stats['contacts_unread']) ?></span>
        <span class="stat-card-label">Messages non lus</span>
      </div>
    </div>
  </div>

  <div class="row-dashboard">
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
              <th>Inscrit le</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentUsers as $user): ?>
            <tr>
              <td><?= htmlspecialchars($user['name']) ?></td>
              <td class="td-muted"><?= htmlspecialchars($user['email']) ?></td>
              <td><span class="badge-dashboard info"><?= htmlspecialchars($user['role']) ?></span></td>
              <td class="td-muted"><?= htmlspecialchars($user['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
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
          <span>Tickets</span>
        </a>
        <a href="/admin/contacts" class="quick-action-card">
          <i class="bi bi-envelope"></i>
          <span>Messages</span>
        </a>
        <a href="/admin/utilisateurs" class="quick-action-card">
          <i class="bi bi-people"></i>
          <span>Utilisateurs</span>
        </a>
      </div>
    </div>
  </div>
</div>
