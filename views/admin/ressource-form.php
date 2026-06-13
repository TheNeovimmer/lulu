<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="card-dashboard">
    <div class="card-dashboard-body">
      <form action="/admin/ressources/create" method="post" enctype="multipart/form-data" class="form-dashboard">
        <div class="row g-4">
          <div class="col-md-8">
            <div class="form-floating">
              <input type="text" name="title" class="form-control" id="title" required>
              <label for="title">Titre</label>
            </div>
            <div class="form-floating">
              <textarea name="description" class="form-control" id="description" rows="8"></textarea>
              <label for="description">Description</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <select name="category_id" class="form-select" id="category_id">
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
              </select>
              <label for="category_id">Catégorie</label>
            </div>
            <div class="mb-3">
              <label class="form-label">Fichier PDF</label>
              <input type="file" name="pdf" class="form-control" accept=".pdf" required>
            </div>
            <button type="submit" class="btn-dashboard btn-dashboard-primary w-100">Créer la ressource</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
