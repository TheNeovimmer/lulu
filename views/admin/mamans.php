<div class="container-fluid py-4">
  <div data-animate="fade-up">
    <h1 class="section-title text-white mb-1">Mamans</h1>
    <p class="section-subtitle text-white-50 mb-4">Liste des mamans inscrites sur la plateforme</p>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($mamans)): ?>
  <div class="empty-state card card-luma p-5 text-center" data-animate="fade-up">
    <div class="stat-icon text-light-pink mb-3">
      <i class="bi bi-heart fs-1"></i>
    </div>
    <h4 class="text-white mb-2">Aucune maman</h4>
    <p class="text-white-50 mb-0">Aucune maman inscrite pour le moment.</p>
  </div>
  <?php else: ?>
  <div class="card card-luma" data-animate="fade-up">
    <div class="card-body p-0">
      <table class="table table-luma mb-0">
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
            <td><?= htmlspecialchars($maman['email']) ?></td>
            <td><?= htmlspecialchars($maman['due_date']) ?></td>
            <td><span class="badge bg-luma"><?= htmlspecialchars($maman['weeks']) ?> sem.</span></td>
            <td>
              <a href="/admin/users/edit/<?= $maman['id'] ?>" class="btn btn-outline-luma btn-sm">Modifier</a>
              <button type="button" class="btn btn-outline-danger-luma btn-sm" data-bs-toggle="modal" data-bs-target="#deleteMamanModal<?= $maman['id'] ?>">Supprimer</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <?php foreach ($mamans as $maman): ?>
  <div class="modal fade" id="deleteMamanModal<?= $maman['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content bg-luma-glass">
        <div class="modal-header border-pink">
          <h5 class="modal-title text-white font-heading">Confirmer la suppression</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-white">
          Supprimer la maman « <?= htmlspecialchars($maman['name']) ?> » ?
        </div>
        <div class="modal-footer border-pink">
          <button type="button" class="btn btn-outline-luma" data-bs-dismiss="modal">Annuler</button>
          <form action="/admin/users/delete/<?= $maman['id'] ?>" method="post" class="d-inline">
            <button type="submit" class="btn btn-danger">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
