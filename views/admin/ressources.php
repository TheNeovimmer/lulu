<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="page-header-dashboard">
    <h1 class="page-title-dashboard">Ressources</h1>
    <div class="page-actions-dashboard">
      <a href="/admin/ressources/create" class="btn-dashboard btn-dashboard-primary">Nouvelle ressource</a>
    </div>
  </div>

  <?php if (empty($resources)): ?>
  <div class="empty-state-dashboard">
    <i class="bi bi-file-earmark-pdf"></i>
    <h5>Aucune ressource</h5>
    <p>Aucune ressource disponible pour le moment.</p>
    <a href="/admin/ressources/create" class="btn-dashboard btn-dashboard-primary">Créer une ressource</a>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table class="table-dashboard">
      <thead>
        <tr>
          <th>Titre</th>
          <th>Catégorie</th>
          <th>Expert</th>
          <th>Téléchargements</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($resources as $ressource): ?>
        <tr>
          <td><?= htmlspecialchars($ressource['title']) ?></td>
          <td class="td-muted"><?= htmlspecialchars($ressource['category_name']) ?></td>
          <td class="td-muted"><?= htmlspecialchars($ressource['expert_name'] ?? '—') ?></td>
          <td class="td-muted"><?= htmlspecialchars($ressource['downloads_count'] ?? 0) ?></td>
          <td class="actions-cell">
            <button type="button" class="btn-icon danger" data-bs-toggle="modal" data-bs-target="#deleteRessourceModal<?= $ressource['id'] ?>"><i class="bi bi-trash"></i></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <?php foreach ($resources as $ressource): ?>
  <div class="modal fade modal-dashboard" id="deleteRessourceModal<?= $ressource['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmer la suppression</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Supprimer la ressource « <?= htmlspecialchars($ressource['title']) ?> » ?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-dashboard btn-dashboard-outline" data-bs-dismiss="modal">Annuler</button>
          <form action="/admin/ressources/delete/<?= $ressource['id'] ?>" method="post" class="inline-form">
            <?= \App\Core\Session::csrf_field() ?>
            <button type="submit" class="btn-dashboard btn-dashboard-primary">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
