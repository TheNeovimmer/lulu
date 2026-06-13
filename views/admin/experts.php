<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($experts)): ?>
  <div class="empty-state-dashboard">
    <i class="bi bi-person-badge"></i>
    <h5>Aucun expert</h5>
    <p>Aucun expert inscrit pour le moment.</p>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table class="table-dashboard">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Email</th>
          <th>Expertise</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($experts as $expert): ?>
        <tr>
          <td><?= htmlspecialchars($expert['name']) ?></td>
          <td class="td-muted"><?= htmlspecialchars($expert['email']) ?></td>
          <td class="td-muted"><?= htmlspecialchars($expert['specialty']) ?></td>
          <td>
            <?php if ($expert['is_verified']): ?>
            <span class="badge-dashboard success">Validé</span>
            <?php else: ?>
            <span class="badge-dashboard warning">En attente</span>
            <?php endif; ?>
          </td>
          <td class="actions-cell">
            <?php if (!$expert['is_verified']): ?>
            <form action="/admin/experts/validate/<?= $expert['id'] ?>" method="post" class="d-inline">
              <?= \App\Core\Session::csrf_field() ?>
              <button type="submit" class="btn-dashboard btn-dashboard-primary btn-dashboard-sm">Valider</button>
            </form>
            <?php endif; ?>
            <button type="button" class="btn-icon danger" data-bs-toggle="modal" data-bs-target="#deleteExpertModal<?= $expert['id'] ?>"><i class="bi bi-trash"></i></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <?php foreach ($experts as $expert): ?>
  <div class="modal fade modal-dashboard" id="deleteExpertModal<?= $expert['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmer la suppression</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Supprimer l'expert « <?= htmlspecialchars($expert['name']) ?> » ?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-dashboard btn-dashboard-outline" data-bs-dismiss="modal">Annuler</button>
          <form action="/admin/experts/delete/<?= $expert['id'] ?>" method="post" class="inline-form">
            <?= \App\Core\Session::csrf_field() ?>
            <button type="submit" class="btn-dashboard btn-dashboard-primary">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
