<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($categories)): ?>
  <div class="empty-state-dashboard">
    <i class="bi bi-tags"></i>
    <h5>Aucune catégorie</h5>
    <p>Créez votre première catégorie ci-contre.</p>
  </div>
  <?php endif; ?>

  <div class="row-dashboard">
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Ajouter une catégorie</h5>
      </div>
      <div class="card-dashboard-body">
        <form action="/admin/categories/create" method="post" class="form-dashboard">
          <div class="form-floating">
            <input type="text" name="name" class="form-control" id="catName" required>
            <label for="catName">Nom</label>
          </div>
          <button type="submit" class="btn-dashboard btn-dashboard-primary">Ajouter</button>
        </form>
      </div>
    </div>
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Catégories existantes</h5>
      </div>
      <div class="table-wrapper">
        <table class="table-dashboard">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categories as $cat): ?>
            <tr>
              <td><?= htmlspecialchars($cat['name']) ?></td>
              <td class="actions-cell">
                <button type="button" class="btn-icon danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $cat['id'] ?>"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <?php foreach ($categories as $cat): ?>
  <div class="modal fade modal-dashboard" id="deleteModal<?= $cat['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmer la suppression</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Supprimer la catégorie « <?= htmlspecialchars($cat['name']) ?> » ?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-dashboard btn-dashboard-outline" data-bs-dismiss="modal">Annuler</button>
          <form action="/admin/categories/delete/<?= $cat['id'] ?>" method="post" class="inline-form">
            <button type="submit" class="btn-dashboard btn-dashboard-primary">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
