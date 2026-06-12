<div class="row">
  <div class="col-12">
    <h1 class="font-heading mb-4"><i class="bi bi-book me-2 text-pink"></i>Ressources</h1>
  </div>

  <div class="col-lg-5 mb-4" data-animate="fade-up">
    <div class="card-luma p-4">
      <h5 class="section-title">Nouvelle ressource</h5>
      <div class="divider-accent"></div>
      <form method="POST" action="/expert/ressources" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label text-white-50">Titre</label>
          <input type="text" name="title" class="form-control form-control-luma" required>
        </div>
        <div class="mb-3">
          <label class="form-label text-white-50">Description</label>
          <textarea name="description" class="form-control form-control-luma" rows="3" required></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label text-white-50">Catégorie</label>
          <select name="category_id" class="form-select form-control-luma">
            <option value="">Choisir...</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label text-white-50">Fichier</label>
          <input type="file" name="file" class="form-control form-control-luma">
        </div>
        <div class="mb-3">
          <label class="form-label text-white-50">Lien externe</label>
          <input type="url" name="link" class="form-control form-control-luma" placeholder="https://...">
        </div>
        <button type="submit" class="btn btn-luma">Créer</button>
      </form>
    </div>
  </div>

  <div class="col-lg-7" data-animate="fade-up">
    <div class="card-luma p-4">
      <h5 class="section-title">Mes ressources</h5>
      <div class="divider-accent"></div>
      <?php if (!empty($ressources)): ?>
      <div class="table-responsive">
        <table class="table table-luma">
          <thead>
            <tr>
              <th>Titre</th>
              <th>Catégorie</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ressources as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['title']) ?></td>
              <td><?= htmlspecialchars($r['category_name'] ?? '-') ?></td>
              <td><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
              <td>
                <a href="<?= $r['file'] ? '/uploads/ressources/'.htmlspecialchars($r['file']) : htmlspecialchars($r['link']) ?>" class="btn btn-sm btn-outline-luma" target="_blank"><i class="bi bi-download"></i></a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-book empty-state-icon"></i>
        <p class="text-white-50">Aucune ressource créée.</p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
