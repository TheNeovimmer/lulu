<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="card-dashboard">
    <div class="card-dashboard-body">
      <form action="/admin/articles/<?= isset($article) ? 'edit/' . $article['id'] : 'create' ?>" method="post" enctype="multipart/form-data" class="form-dashboard">
        <?= \App\Core\Session::csrf_field() ?>
        <div class="row g-4">
          <div class="col-md-8">
            <div class="form-floating">
              <input type="text" name="title" class="form-control" id="title" value="<?= isset($article) ? htmlspecialchars($article['title']) : '' ?>" required>
              <label for="title">Titre</label>
            </div>
            <div class="form-floating">
              <input type="text" name="slug" class="form-control" id="slug" value="<?= isset($article) ? htmlspecialchars($article['slug']) : '' ?>">
              <label for="slug">Slug</label>
            </div>
            <div class="form-floating">
              <textarea name="content" class="form-control" id="content" rows="12"><?= isset($article) ? htmlspecialchars($article['content']) : '' ?></textarea>
              <label for="content">Contenu</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <select name="category_id" class="form-select" id="category_id">
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (isset($article) && $article['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
              <label for="category_id">Catégorie</label>
            </div>
            <div class="form-floating">
              <select name="status" class="form-select" id="status">
                <option value="draft" <?= (isset($article) && $article['status'] === 'draft') ? 'selected' : '' ?>>Brouillon</option>
                <option value="published" <?= (isset($article) && $article['status'] === 'published') ? 'selected' : '' ?>>Publié</option>
              </select>
              <label for="status">Statut</label>
            </div>
            <div class="mb-3">
              <label class="form-label">Image</label>
              <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn-dashboard btn-dashboard-primary w-100">Enregistrer</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
