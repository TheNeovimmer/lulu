<div class="row-dashboard">
  <div>
    <div class="card-dashboard form-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Modifier l'article</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/expert/articles/update/<?= $article['id'] ?>">
          <?= \App\Core\Session::csrf_field() ?>
          <div class="form-floating">
            <input type="text" name="title" class="form-control" placeholder="Titre" value="<?= htmlspecialchars($article['title']) ?>" required>
            <label>Titre</label>
          </div>
          <div class="form-floating">
            <textarea name="content" class="form-control" rows="12" placeholder="Contenu" required><?= htmlspecialchars($article['content']) ?></textarea>
            <label>Contenu</label>
          </div>
          <div class="form-floating">
            <select name="category_id" class="form-select" required>
              <option value="">Choisir...</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $article['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <label>Catégorie</label>
          </div>
          <div class="form-floating">
            <select name="status" class="form-select">
              <option value="draft" <?= $article['status'] === 'draft' ? 'selected' : '' ?>>Brouillon</option>
              <option value="published" <?= $article['status'] === 'published' ? 'selected' : '' ?>>Publié</option>
            </select>
            <label>Statut</label>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-dashboard btn-dashboard-primary">Enregistrer</button>
            <a href="/expert/articles" class="btn btn-dashboard btn-dashboard-secondary">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
