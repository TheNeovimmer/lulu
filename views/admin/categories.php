<div class="container-fluid py-4">
  <div data-animate="fade-up">
    <h1 class="section-title text-white mb-1">Catégories</h1>
    <p class="section-subtitle text-white-50 mb-4">Organisez vos articles par catégories</p>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($categories)): ?>
  <div class="empty-state card card-luma p-5 text-center" data-animate="fade-up">
    <div class="stat-icon text-light-pink mb-3">
      <i class="bi bi-tags fs-1"></i>
    </div>
    <h4 class="text-white mb-2">Aucune catégorie</h4>
    <p class="text-white-50 mb-0">Créez votre première catégorie ci-contre.</p>
  </div>
  <?php endif; ?>

  <div class="row g-4" data-animate="fade-up">
    <div class="col-md-5">
      <div class="card card-luma">
        <div class="card-header bg-transparent border-pink">
          <h5 class="text-white font-heading mb-0">Ajouter une catégorie</h5>
        </div>
        <div class="card-body">
          <form action="/admin/categories/create" method="post">
            <div class="mb-3">
              <label class="form-label text-white-50">Nom</label>
              <input type="text" name="name" class="form-control form-control-luma" required>
            </div>
            <button type="submit" class="btn btn-luma">Ajouter</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-7">
      <div class="card card-luma">
        <div class="card-header bg-transparent border-pink">
          <h5 class="text-white font-heading mb-0">Catégories existantes</h5>
        </div>
        <div class="card-body p-0">
          <table class="table table-luma mb-0">
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
                <td>
                  <button type="button" class="btn btn-outline-danger-luma btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $cat['id'] ?>">Supprimer</button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php foreach ($categories as $cat): ?>
  <div class="modal fade" id="deleteModal<?= $cat['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content bg-luma-glass">
        <div class="modal-header border-pink">
          <h5 class="modal-title text-white font-heading">Confirmer la suppression</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-white">
          Supprimer la catégorie « <?= htmlspecialchars($cat['name']) ?> » ?
        </div>
        <div class="modal-footer border-pink">
          <button type="button" class="btn btn-outline-luma" data-bs-dismiss="modal">Annuler</button>
          <form action="/admin/categories/delete/<?= $cat['id'] ?>" method="post" class="d-inline">
            <button type="submit" class="btn btn-danger">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
