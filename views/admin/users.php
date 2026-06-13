<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="table-toolbar">
    <div class="table-toolbar-left">
      <a class="btn-dashboard btn-dashboard-outline btn-dashboard-sm <?= !isset($_GET['role']) ? 'active' : '' ?>" href="/admin/users">Tous</a>
      <a class="btn-dashboard btn-dashboard-outline btn-dashboard-sm <?= ($_GET['role'] ?? '') === 'maman' ? 'active' : '' ?>" href="/admin/users?role=maman">Mamans</a>
      <a class="btn-dashboard btn-dashboard-outline btn-dashboard-sm <?= ($_GET['role'] ?? '') === 'expert' ? 'active' : '' ?>" href="/admin/users?role=expert">Experts</a>
      <a class="btn-dashboard btn-dashboard-outline btn-dashboard-sm <?= ($_GET['role'] ?? '') === 'ctt' ? 'active' : '' ?>" href="/admin/users?role=ctt">CTT</a>
      <a class="btn-dashboard btn-dashboard-outline btn-dashboard-sm <?= ($_GET['role'] ?? '') === 'admin' ? 'active' : '' ?>" href="/admin/users?role=admin">Admin</a>
    </div>
  </div>

  <?php if (empty($users)): ?>
  <div class="empty-state-dashboard">
    <i class="bi bi-people"></i>
    <h5>Aucun utilisateur</h5>
    <p>Aucun utilisateur trouvé pour ce filtre.</p>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table class="table-dashboard">
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
          <td class="td-muted"><?= htmlspecialchars($user['email']) ?></td>
          <td><span class="badge-dashboard info"><?= htmlspecialchars($user['role']) ?></span></td>
          <td>
            <?php if ($user['status'] === 'active'): ?>
            <span class="badge-dashboard success">Actif</span>
            <?php else: ?>
            <span class="badge-dashboard">Suspendu</span>
            <?php endif; ?>
          </td>
          <td class="td-muted"><?= htmlspecialchars($user['created_at']) ?></td>
          <td class="actions-cell">
            <div class="dropdown d-inline">
              <button class="btn-icon" data-bs-toggle="dropdown"><i class="bi bi-shuffle"></i></button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/admin/users/role/<?= $user['id'] ?>?role=maman">Maman</a></li>
                <li><a class="dropdown-item" href="/admin/users/role/<?= $user['id'] ?>?role=expert">Expert</a></li>
                <li><a class="dropdown-item" href="/admin/users/role/<?= $user['id'] ?>?role=ctt">CTT</a></li>
                <li><a class="dropdown-item" href="/admin/users/role/<?= $user['id'] ?>?role=admin">Admin</a></li>
              </ul>
            </div>
            <?php if ($user['status'] === 'active'): ?>
            <a href="/admin/users/suspend/<?= $user['id'] ?>" class="btn-icon warning" onclick="return confirm('Suspendre cet utilisateur ?')"><i class="bi bi-pause-circle"></i></a>
            <?php endif; ?>
            <button type="button" class="btn-icon danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal<?= $user['id'] ?>"><i class="bi bi-trash"></i></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <?php foreach ($users as $user): ?>
  <div class="modal fade modal-dashboard" id="deleteUserModal<?= $user['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmer la suppression</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Supprimer définitivement l'utilisateur « <?= htmlspecialchars($user['name']) ?> » ?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-dashboard btn-dashboard-outline" data-bs-dismiss="modal">Annuler</button>
          <form action="/admin/users/delete/<?= $user['id'] ?>" method="post" class="inline-form">
            <button type="submit" class="btn-dashboard btn-dashboard-primary">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
