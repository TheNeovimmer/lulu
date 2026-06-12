<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="section-title text-white mb-1">Tableau de bord</h1>
      <p class="section-subtitle text-white-50 mb-0">Vue d'ensemble de votre plateforme</p>
    </div>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="row g-4 mb-4 animate-stagger" data-animate="fade-up">
    <div class="col-md-3">
      <div class="stat-card card card-luma text-center p-4">
        <div class="stat-icon text-light-pink">
          <i class="bi bi-people fs-3"></i>
        </div>
        <div class="stat-number fs-1 text-light-pink fw-bold"><?= htmlspecialchars($stats['users']) ?></div>
        <div class="stat-label text-white-50">Utilisateurs</div>
        <div class="stat-accent"></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card card card-luma text-center p-4">
        <div class="stat-icon text-light-pink">
          <i class="bi bi-file-text fs-3"></i>
        </div>
        <div class="stat-number fs-1 text-light-pink fw-bold"><?= htmlspecialchars($stats['articles']) ?></div>
        <div class="stat-label text-white-50">Articles</div>
        <div class="stat-accent"></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card card card-luma text-center p-4">
        <div class="stat-icon text-light-pink">
          <i class="bi bi-ticket fs-3"></i>
        </div>
        <div class="stat-number fs-1 text-light-pink fw-bold"><?= htmlspecialchars($stats['tickets_open']) ?></div>
        <div class="stat-label text-white-50">Tickets ouverts</div>
        <div class="stat-accent"></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card card card-luma text-center p-4">
        <div class="stat-icon text-light-pink">
          <i class="bi bi-envelope fs-3"></i>
        </div>
        <div class="stat-number fs-1 text-light-pink fw-bold"><?= htmlspecialchars($stats['contacts_unread']) ?></div>
        <div class="stat-label text-white-50">Messages non lus</div>
        <div class="stat-accent"></div>
      </div>
    </div>
  </div>

  <div class="divider-accent mb-4"></div>

  <div class="row g-4 mb-4" data-animate="fade-up">
    <div class="col-md-8">
      <div class="card card-luma">
        <div class="card-header bg-transparent border-pink d-flex justify-content-between align-items-center">
          <h5 class="text-white font-heading mb-0">Derniers utilisateurs</h5>
          <a href="/admin/users" class="btn btn-outline-luma btn-sm">Voir tout</a>
        </div>
        <div class="card-body p-0">
          <table class="table table-luma mb-0">
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
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><span class="badge bg-luma"><?= htmlspecialchars($user['role']) ?></span></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-luma h-100">
        <div class="card-header bg-transparent border-pink">
          <h5 class="text-white font-heading mb-0">Actions rapides</h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <a href="/admin/articles/create" class="btn btn-luma">Nouvel article</a>
            <a href="/admin/categories" class="btn btn-outline-luma">Gérer les catégories</a>
            <a href="/admin/tickets" class="btn btn-outline-luma">Voir les tickets</a>
            <a href="/admin/contacts" class="btn btn-outline-luma">Messages reçus</a>
            <a href="/admin/users" class="btn btn-outline-luma">Gérer les utilisateurs</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
