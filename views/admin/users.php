<div class="container-fluid py-4">
  <div data-animate="fade-up">
    <h1 class="section-title text-white mb-1">Utilisateurs</h1>
    <p class="section-subtitle text-white-50 mb-4">Gérez les comptes utilisateurs de la plateforme</p>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="d-flex flex-wrap gap-2 mb-4" data-animate="fade-up">
    <a class="filter-pill btn btn-outline-luma btn-sm <?= !isset($_GET['role']) ? 'active' : '' ?>" href="/admin/users">Tous</a>
    <a class="filter-pill btn btn-outline-luma btn-sm <?= ($_GET['role'] ?? '') === 'maman' ? 'active' : '' ?>" href="/admin/users?role=maman">Mamans</a>
    <a class="filter-pill btn btn-outline-luma btn-sm <?= ($_GET['role'] ?? '') === 'expert' ? 'active' : '' ?>" href="/admin/users?role=expert">Experts</a>
    <a class="filter-pill btn btn-outline-luma btn-sm <?= ($_GET['role'] ?? '') === 'ctt' ? 'active' : '' ?>" href="/admin/users?role=ctt">CTT</a>
    <a class="filter-pill btn btn-outline-luma btn-sm <?= ($_GET['role'] ?? '') === 'admin' ? 'active' : '' ?>" href="/admin/users?role=admin">Admin</a>
  </div>

  <?php if (empty($users)): ?>
  <div class="empty-state card card-luma p-5 text-center" data-animate="fade-up">
    <div class="stat-icon text-light-pink mb-3">
      <i class="bi bi-people fs-1"></i>
    </div>
    <h4 class="text-white mb-2">Aucun utilisateur</h4>
    <p class="text-white-50 mb-0">Aucun utilisateur trouvé pour ce filtre.</p>
  </div>
  <?php else: ?>
  <div class="card card-luma" data-animate="fade-up">
    <div class="card-body p-0">
      <table class="table table-luma mb-0">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Statut</th>
            <th>Inscrit le</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
          <tr>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><span class="badge bg-luma"><?= htmlspecialchars($user['role']) ?></span></td>
            <td>
              <?php if ($user['status'] === 'active'): ?>
              <span class="badge bg-success">Actif</span>
              <?php else: ?>
              <span class="badge bg-secondary">Suspendu</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($user['created_at']) ?></td>
            <td>
              <div class="dropdown">
                <button class="btn btn-outline-luma btn-sm dropdown-toggle" data-bs-toggle="dropdown">Changer rôle</button>
                <ul class="dropdown-menu dropdown-menu-dark">
                  <li><a class="dropdown-item" href="/admin/users/role/<?= $user['id'] ?>?role=maman">Maman</a></li>
                  <li><a class="dropdown-item" href="/admin/users/role/<?= $user['id'] ?>?role=expert">Expert</a></li>
                  <li><a class="dropdown-item" href="/admin/users/role/<?= $user['id'] ?>?role=ctt">CTT</a></li>
                  <li><a class="dropdown-item" href="/admin/users/role/<?= $user['id'] ?>?role=admin">Admin</a></li>
                </ul>
              </div>
              <?php if ($user['status'] === 'active'): ?>
              <a href="/admin/users/suspend/<?= $user['id'] ?>" class="btn btn-outline-danger-luma btn-sm" onclick="return confirm('Suspendre cet utilisateur ?')">Suspendre</a>
              <?php endif; ?>
              <button type="button" class="btn btn-outline-danger-luma btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUserModal<?= $user['id'] ?>">Supprimer</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <?php foreach ($users as $user): ?>
  <div class="modal fade" id="deleteUserModal<?= $user['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content bg-luma-glass">
        <div class="modal-header border-pink">
          <h5 class="modal-title text-white font-heading">Confirmer la suppression</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-white">
          Supprimer définitivement l'utilisateur « <?= htmlspecialchars($user['name']) ?> » ?
        </div>
        <div class="modal-footer border-pink">
          <button type="button" class="btn btn-outline-luma" data-bs-dismiss="modal">Annuler</button>
          <form action="/admin/users/delete/<?= $user['id'] ?>" method="post" class="d-inline">
            <button type="submit" class="btn btn-danger">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
