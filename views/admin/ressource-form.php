<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="card-dashboard">
    <div class="card-dashboard-header">
      <h5 class="card-dashboard-title"><?= isset($resource) ? 'Modifier la ressource' : 'Nouvelle ressource' ?></h5>
    </div>
    <div class="card-dashboard-body">
      <form action="/admin/ressources/<?= isset($resource) ? 'edit/' . $resource['id'] : 'create' ?>" method="post" enctype="multipart/form-data" class="form-dashboard">
        <?= \App\Core\Session::csrf_field() ?>
        <div class="row g-4">
          <div class="col-md-8">
            <div class="form-floating">
              <input type="text" name="title" class="form-control" id="title" value="<?= isset($resource) ? htmlspecialchars($resource['title']) : '' ?>" required>
              <label for="title">Titre</label>
            </div>
            <div class="form-floating">
              <textarea name="description" class="form-control" id="description" rows="8"><?= isset($resource) ? htmlspecialchars($resource['description'] ?? '') : '' ?></textarea>
              <label for="description">Description</label>
            </div>
            <input type="hidden" name="file_url" value="<?= isset($resource) ? htmlspecialchars($resource['file_url'] ?? '') : '' ?>">
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <select name="type" class="form-select" id="type">
                <option value="pdf" <?= (isset($resource) && $resource['type'] === 'pdf') ? 'selected' : '' ?>>PDF</option>
                <option value="ebook" <?= (isset($resource) && $resource['type'] === 'ebook') ? 'selected' : '' ?>>Ebook</option>
                <option value="video" <?= (isset($resource) && $resource['type'] === 'video') ? 'selected' : '' ?>>Vidéo</option>
                <option value="guide" <?= (isset($resource) && $resource['type'] === 'guide') ? 'selected' : '' ?>>Guide</option>
              </select>
              <label for="type">Type</label>
            </div>
            <div class="form-floating">
              <select name="category_id" class="form-select" id="category_id">
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (isset($resource) && $resource['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
              <label for="category_id">Catégorie</label>
            </div>
            <div class="form-floating">
              <select name="expert_id" class="form-select" id="expert_id">
                <option value="">Sélectionner un expert</option>
                <?php foreach ($experts as $exp): ?>
                <option value="<?= $exp['id'] ?>" <?= (isset($resource) && $resource['user_id'] == $exp['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($exp['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
              <label for="expert_id">Expert</label>
            </div>
            <button type="submit" class="btn-dashboard btn-dashboard-primary w-100"><?= isset($resource) ? 'Enregistrer' : 'Créer la ressource' ?></button>
            <?php if (isset($resource)): ?>
            <a href="/admin/ressources" class="btn-dashboard btn-dashboard-outline w-100 mt-2">Annuler</a>
            <?php endif; ?>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
