<div class="container-fluid py-4">
  <div data-animate="fade-up">
    <h1 class="section-title text-white mb-1"><?= isset($article) ? 'Modifier' : 'Nouvel' ?> article</h1>
    <p class="section-subtitle text-white-50 mb-4"><?= isset($article) ? 'Modifiez les informations de l\'article' : 'Rédigez un nouvel article pour le blog' ?></p>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="card card-luma animate-scale-in" data-animate="fade-up">
    <div class="card-body">
      <form action="/admin/articles/<?= isset($article) ? 'edit/' . $article['id'] : 'create' ?>" method="post" enctype="multipart/form-data">
        <div class="row g-4">
          <div class="col-md-8">
            <div class="mb-3">
              <label class="form-label text-white-50">Titre</label>
              <input type="text" name="title" class="form-control form-control-luma" value="<?= isset($article) ? htmlspecialchars($article['title']) : '' ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label text-white-50">Slug</label>
              <input type="text" name="slug" class="form-control form-control-luma" value="<?= isset($article) ? htmlspecialchars($article['slug']) : '' ?>">
              <div class="form-text text-white-50">Laissez vide pour générer automatiquement depuis le titre.</div>
            </div>
            <div class="mb-3">
              <label class="form-label text-white-50">Contenu</label>
              <textarea name="content" class="form-control form-control-luma" rows="12"><?= isset($article) ? htmlspecialchars($article['content']) : '' ?></textarea>
            </div>
          </div>
          <div class="col-md-4">
            <div class="mb-3">
              <label class="form-label text-white-50">Catégorie</label>
              <select name="category_id" class="form-control form-control-luma">
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (isset($article) && $article['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label text-white-50">Statut</label>
              <select name="status" class="form-control form-control-luma">
                <option value="draft" <?= (isset($article) && $article['status'] === 'draft') ? 'selected' : '' ?>>Brouillon</option>
                <option value="published" <?= (isset($article) && $article['status'] === 'published') ? 'selected' : '' ?>>Publié</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label text-white-50">Image</label>
              <input type="file" name="image" class="form-control form-control-luma" accept="image/*">
            </div>
            <button type="submit" class="btn btn-luma w-100">Enregistrer</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
