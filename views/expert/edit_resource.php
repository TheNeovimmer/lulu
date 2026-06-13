<div class="row-dashboard">
  <div>
    <div class="card-dashboard form-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Modifier la ressource</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/expert/ressources/update/<?= $resource['id'] ?>" enctype="multipart/form-data">
          <?= \App\Core\Session::csrf_field() ?>
          <div class="form-floating">
            <input type="text" name="title" class="form-control" placeholder="Titre" value="<?= htmlspecialchars($resource['title']) ?>" required>
            <label>Titre</label>
          </div>
          <div class="form-floating">
            <textarea name="description" class="form-control" rows="5" placeholder="Description" required><?= htmlspecialchars($resource['description']) ?></textarea>
            <label>Description</label>
          </div>
          <div class="form-floating">
            <select name="type" class="form-select" required>
              <option value="guide" <?= $resource['type'] === 'guide' ? 'selected' : '' ?>>Guide</option>
              <option value="pdf" <?= $resource['type'] === 'pdf' ? 'selected' : '' ?>>PDF</option>
              <option value="ebook" <?= $resource['type'] === 'ebook' ? 'selected' : '' ?>>E-book</option>
              <option value="video" <?= $resource['type'] === 'video' ? 'selected' : '' ?>>Vidéo</option>
            </select>
            <label>Type</label>
          </div>
          <div class="form-floating">
            <select name="category_id" class="form-select">
              <option value="">Choisir...</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $resource['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <label>Catégorie</label>
          </div>
          <div class="mb-3">
            <label class="form-label">Fichier actuel :</label>
            <?php if ($resource['file_url']): ?>
              <a href="<?= htmlspecialchars($resource['file_url']) ?>" target="_blank">Voir le fichier</a>
            <?php else: ?>
              <span class="text-muted">Aucun fichier</span>
            <?php endif; ?>
            <input type="file" name="file_url" class="form-control mt-2">
            <small class="text-muted">Laissez vide pour conserver le fichier actuel.</small>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-dashboard btn-dashboard-primary">Enregistrer</button>
            <a href="/expert/ressources" class="btn btn-dashboard btn-dashboard-secondary">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>