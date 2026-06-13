<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($mamans)): ?>
  <div class="empty-state-dashboard">
    <i class="bi bi-heart"></i>
    <h5>Aucune maman</h5>
    <p>Aucune maman inscrite pour le moment.</p>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table class="table-dashboard">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Email</th>
          <th>Date d'accouchement</th>
          <th>Semaines</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($mamans as $maman): ?>
        <tr>
          <td><?= htmlspecialchars($maman['name']) ?></td>
          <td class="td-muted"><?= htmlspecialchars($maman['email']) ?></td>
          <td class="td-muted"><?= htmlspecialchars($maman['due_date']) ?></td>
          <td><span class="badge-dashboard info"><?= htmlspecialchars($maman['weeks_gestation']) ?> sem.</span></td>
          <td class="actions-cell">
            <a href="/admin/mamans/<?= $maman['id'] ?>" class="btn-icon"><i class="bi bi-eye"></i></a>
            <a href="/admin/users/edit/<?= $maman['id'] ?>" class="btn-icon"><i class="bi bi-pencil"></i></a>
            <button type="button" class="btn-icon danger" data-bs-toggle="modal" data-bs-target="#deleteMamanModal<?= $maman['id'] ?>"><i class="bi bi-trash"></i></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <?php foreach ($mamans as $maman): ?>
  <div class="modal fade modal-dashboard" id="deleteMamanModal<?= $maman['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmer la suppression</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Supprimer la maman « <?= htmlspecialchars($maman['name']) ?> » ?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-dashboard btn-dashboard-outline" data-bs-dismiss="modal">Annuler</button>
          <form action="/admin/users/delete/<?= $maman['id'] ?>" method="post" class="inline-form">
            <?= \App\Core\Session::csrf_field() ?>
            <button type="submit" class="btn-dashboard btn-dashboard-primary">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
