<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4" data-animate="fade-up">
    <div>
      <h1 class="section-title text-white mb-1">Ressources</h1>
      <p class="section-subtitle text-white-50 mb-0">Gérez les ressources PDF téléchargeables</p>
    </div>
    <a href="/admin/ressources/create" class="btn btn-luma">Nouvelle ressource</a>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($ressources)): ?>
  <div class="empty-state card card-luma p-5 text-center" data-animate="fade-up">
    <div class="stat-icon text-light-pink mb-3">
      <i class="bi bi-file-earmark-pdf fs-1"></i>
    </div>
    <h4 class="text-white mb-2">Aucune ressource</h4>
    <p class="text-white-50 mb-4">Aucune ressource disponible pour le moment.</p>
    <a href="/admin/ressources/create" class="btn btn-luma">Créer une ressource</a>
  </div>
  <?php else: ?>
  <div class="card card-luma" data-animate="fade-up">
    <div class="card-body p-0">
      <table class="table table-luma mb-0">
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
          <?php foreach ($ressources as $ressource): ?>
          <tr>
            <td><?= htmlspecialchars($ressource['title']) ?></td>
            <td><?= htmlspecialchars($ressource['category_name']) ?></td>
            <td><?= htmlspecialchars($ressource['expert_name']) ?></td>
            <td><?= htmlspecialchars($ressource['downloads']) ?></td>
            <td>
              <button type="button" class="btn btn-outline-danger-luma btn-sm" data-bs-toggle="modal" data-bs-target="#deleteRessourceModal<?= $ressource['id'] ?>">Supprimer</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <?php foreach ($ressources as $ressource): ?>
  <div class="modal fade" id="deleteRessourceModal<?= $ressource['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content bg-luma-glass">
        <div class="modal-header border-pink">
          <h5 class="modal-title text-white font-heading">Confirmer la suppression</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-white">
          Supprimer la ressource « <?= htmlspecialchars($ressource['title']) ?> » ?
        </div>
        <div class="modal-footer border-pink">
          <button type="button" class="btn btn-outline-luma" data-bs-dismiss="modal">Annuler</button>
          <form action="/admin/ressources/delete/<?= $ressource['id'] ?>" method="post" class="d-inline">
            <button type="submit" class="btn btn-danger">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
