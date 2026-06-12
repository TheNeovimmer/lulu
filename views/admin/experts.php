<div class="container-fluid py-4">
  <div data-animate="fade-up">
    <h1 class="section-title text-white mb-1">Experts</h1>
    <p class="section-subtitle text-white-50 mb-4">Validez et gérez les experts de la plateforme</p>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($experts)): ?>
  <div class="empty-state card card-luma p-5 text-center" data-animate="fade-up">
    <div class="stat-icon text-light-pink mb-3">
      <i class="bi bi-person-badge fs-1"></i>
    </div>
    <h4 class="text-white mb-2">Aucun expert</h4>
    <p class="text-white-50 mb-0">Aucun expert inscrit pour le moment.</p>
  </div>
  <?php else: ?>
  <div class="card card-luma" data-animate="fade-up">
    <div class="card-body p-0">
      <table class="table table-luma mb-0">
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
            <td><?= htmlspecialchars($expert['email']) ?></td>
            <td><?= htmlspecialchars($expert['expertise']) ?></td>
            <td>
              <?php if ($expert['validated']): ?>
              <span class="badge bg-success">Validé</span>
              <?php else: ?>
              <span class="badge bg-warning text-dark">En attente</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!$expert['validated']): ?>
              <a href="/admin/experts/validate/<?= $expert['id'] ?>" class="btn btn-luma btn-sm">Valider</a>
              <?php endif; ?>
              <button type="button" class="btn btn-outline-danger-luma btn-sm" data-bs-toggle="modal" data-bs-target="#deleteExpertModal<?= $expert['id'] ?>">Supprimer</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <?php foreach ($experts as $expert): ?>
  <div class="modal fade" id="deleteExpertModal<?= $expert['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content bg-luma-glass">
        <div class="modal-header border-pink">
          <h5 class="modal-title text-white font-heading">Confirmer la suppression</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-white">
          Supprimer l'expert « <?= htmlspecialchars($expert['name']) ?> » ?
        </div>
        <div class="modal-footer border-pink">
          <button type="button" class="btn btn-outline-luma" data-bs-dismiss="modal">Annuler</button>
          <form action="/admin/experts/delete/<?= $expert['id'] ?>" method="post" class="d-inline">
            <button type="submit" class="btn btn-danger">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
