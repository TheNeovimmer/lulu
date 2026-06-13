<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="row-dashboard">
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Ajouter un utilisateur</h5>
      </div>
      <div class="card-dashboard-body">
        <form action="/admin/utilisateurs/create" method="post" class="form-dashboard">
          <?= \App\Core\Session::csrf_field() ?>
          <div class="row g-3">
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" name="name" class="form-control" id="userName" required>
                <label for="userName">Nom</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="email" name="email" class="form-control" id="userEmail" required>
                <label for="userEmail">Email</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="password" name="password" class="form-control" id="userPassword" required minlength="6">
                <label for="userPassword">Mot de passe</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <select name="role_id" class="form-select" id="userRole" required>
                  <option value="">Sélectionner un rôle</option>
                  <?php foreach ($roles as $role): ?>
                  <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                  <?php endforeach; ?>
                </select>
                <label for="userRole">Rôle</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <select name="status" class="form-select" id="userStatus">
                  <option value="active">Actif</option>
                  <option value="suspended">Suspendu</option>
                </select>
                <label for="userStatus">Statut</label>
              </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
              <button type="submit" class="btn-dashboard btn-dashboard-primary w-100">Créer l'utilisateur</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="table-toolbar">
    <div class="table-toolbar-left">
      <a class="btn-dashboard btn-dashboard-outline btn-dashboard-sm <?= !isset($_GET['role']) ? 'active' : '' ?>" href="/admin/utilisateurs">Tous</a>
      <a class="btn-dashboard btn-dashboard-outline btn-dashboard-sm <?= ($_GET['role'] ?? '') === 'maman' ? 'active' : '' ?>" href="/admin/utilisateurs?role=maman">Mamans</a>
      <a class="btn-dashboard btn-dashboard-outline btn-dashboard-sm <?= ($_GET['role'] ?? '') === 'expert' ? 'active' : '' ?>" href="/admin/utilisateurs?role=expert">Experts</a>
      <a class="btn-dashboard btn-dashboard-outline btn-dashboard-sm <?= ($_GET['role'] ?? '') === 'ctt' ? 'active' : '' ?>" href="/admin/utilisateurs?role=ctt">CTT</a>
      <a class="btn-dashboard btn-dashboard-outline btn-dashboard-sm <?= ($_GET['role'] ?? '') === 'admin' ? 'active' : '' ?>" href="/admin/utilisateurs?role=admin">Admin</a>
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
          <td><span class="badge-dashboard info"><?= htmlspecialchars($user['role_name']) ?></span></td>
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
                <?php foreach ($roles as $role): ?>
                <li>
                  <form action="/admin/utilisateurs/toggle-role/<?= $user['id'] ?>" method="post" class="inline-form">
                    <?= \App\Core\Session::csrf_field() ?>
                    <input type="hidden" name="role" value="<?= $role['slug'] ?>">
                    <button type="submit" class="dropdown-item"><?= htmlspecialchars($role['name']) ?></button>
                  </form>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>
            <?php if ($user['status'] === 'active'): ?>
            <form action="/admin/utilisateurs/suspendre/<?= $user['id'] ?>" method="post" class="inline-form">
              <?= \App\Core\Session::csrf_field() ?>
              <button type="submit" class="btn-icon warning" onclick="return confirm('Suspendre cet utilisateur ?')"><i class="bi bi-pause-circle"></i></button>
            </form>
            <?php else: ?>
            <form action="/admin/utilisateurs/activate/<?= $user['id'] ?>" method="post" class="inline-form">
              <?= \App\Core\Session::csrf_field() ?>
              <button type="submit" class="btn-icon success" title="Réactiver"><i class="bi bi-play-circle"></i></button>
            </form>
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
          <form action="/admin/utilisateurs/delete/<?= $user['id'] ?>" method="post" class="inline-form">
            <?= \App\Core\Session::csrf_field() ?>
            <button type="submit" class="btn-dashboard btn-dashboard-primary">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
