<div class="row-dashboard">
  <div>
    <div class="card-dashboard form-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Nouvelle ressource</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/expert/ressources" enctype="multipart/form-data">
          <div class="form-floating">
            <input type="text" name="title" class="form-control" placeholder="Titre" required>
            <label>Titre</label>
          </div>
          <div class="form-floating">
            <textarea name="description" class="form-control" rows="3" placeholder="Description" required></textarea>
            <label>Description</label>
          </div>
          <div class="form-floating">
            <select name="category_id" class="form-select">
              <option value="">Choisir...</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <label>Catégorie</label>
          </div>
          <div class="mb-3">
            <label class="form-label small text-muted">Fichier</label>
            <input type="file" name="file" class="form-control">
          </div>
          <div class="form-floating">
            <input type="url" name="link" class="form-control" placeholder="https://...">
            <label>Lien externe</label>
          </div>
          <button type="submit" class="btn btn-dashboard btn-dashboard-primary">Créer</button>
        </form>
      </div>
    </div>
  </div>

  <div>
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Mes ressources</h5>
      </div>
      <div class="card-dashboard-body">
        <?php if (!empty($ressources)): ?>
        <div class="table-wrapper">
          <table class="table-dashboard">
            <thead>
              <tr>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Date</th>
                <th class="actions-cell">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($ressources as $r): ?>
              <tr>
                <td><?= htmlspecialchars($r['title']) ?></td>
                <td><?= htmlspecialchars($r['category_name'] ?? '-') ?></td>
                <td><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
                <td class="actions-cell">
                  <a href="<?= $r['file'] ? '/uploads/ressources/'.htmlspecialchars($r['file']) : htmlspecialchars($r['link']) ?>" class="btn-icon" target="_blank"><i class="bi bi-download"></i></a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div class="empty-state-dashboard">
          <i class="bi bi-book"></i>
          <h5>Aucune ressource</h5>
          <p>Aucune ressource créée.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
