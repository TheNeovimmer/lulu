<div class="row">
  <div class="col-12">
    <h1 class="font-heading mb-4"><i class="bi bi-file-text me-2 text-pink"></i>Mes Articles</h1>
  </div>

  <div class="col-lg-5 mb-4" data-animate="fade-up">
    <div class="card-luma p-4">
      <h5 class="section-title">Nouvel article</h5>
      <div class="divider-accent"></div>
      <form method="POST" action="/expert/articles">
        <div class="mb-3">
          <label class="form-label text-white-50">Titre</label>
          <input type="text" name="title" class="form-control form-control-luma" required>
        </div>
        <div class="mb-3">
          <label class="form-label text-white-50">Contenu</label>
          <textarea name="content" class="form-control form-control-luma" rows="8" required></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label text-white-50">Catégorie</label>
          <select name="category_id" class="form-select form-control-luma" required>
            <option value="">Choisir...</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label text-white-50">Statut</label>
          <select name="status" class="form-select form-control-luma">
            <option value="draft">Brouillon</option>
            <option value="published">Publié</option>
          </select>
        </div>
        <button type="submit" class="btn btn-luma">Créer</button>
      </form>
    </div>
  </div>

  <div class="col-lg-7" data-animate="fade-up">
    <div class="card-luma p-4">
      <h5 class="section-title">Articles publiés</h5>
      <div class="divider-accent"></div>
      <?php if (!empty($articles)): ?>
      <div class="table-responsive">
        <table class="table table-luma">
          <thead>
            <tr>
              <th>Titre</th>
              <th>Catégorie</th>
              <th>Date</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($articles as $a): ?>
            <tr>
              <td><?= htmlspecialchars($a['title']) ?></td>
              <td><?= htmlspecialchars($a['category_name'] ?? '-') ?></td>
              <td><?= date('d/m/Y', strtotime($a['created_at'])) ?></td>
              <td>
                <?php if ($a['status'] === 'published'): ?>
                  <span class="badge bg-success">Publié</span>
                <?php else: ?>
                  <span class="badge bg-secondary">Brouillon</span>
                <?php endif; ?>
              </td>
              <td>
                <a href="/expert/articles/<?= $a['id'] ?>/edit" class="btn btn-sm btn-outline-luma"><i class="bi bi-pencil"></i></a>
                <a href="/blog/<?= $a['id'] ?>" class="btn btn-sm btn-outline-luma"><i class="bi bi-eye"></i></a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-file-text empty-state-icon"></i>
        <p class="text-white-50">Aucun article pour le moment.</p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
