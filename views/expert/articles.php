<div class="row-dashboard">
  <div>
    <div class="card-dashboard form-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Nouvel article</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/expert/articles/create">
          <?= \App\Core\Session::csrf_field() ?>
          <div class="form-floating">
            <input type="text" name="title" class="form-control" placeholder="Titre" required>
            <label>Titre</label>
          </div>
          <div class="form-floating">
            <textarea name="content" class="form-control" rows="8" placeholder="Contenu" required></textarea>
            <label>Contenu</label>
          </div>
          <div class="form-floating">
            <select name="category_id" class="form-select" required>
              <option value="">Choisir...</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <label>Catégorie</label>
          </div>
          <div class="form-floating">
            <select name="status" class="form-select">
              <option value="draft">Brouillon</option>
              <option value="published">Publié</option>
            </select>
            <label>Statut</label>
          </div>
          <button type="submit" class="btn btn-dashboard btn-dashboard-primary">Créer</button>
        </form>
      </div>
    </div>
  </div>

  <div>
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Articles publiés</h5>
      </div>
      <div class="card-dashboard-body">
        <?php if (!empty($articles)): ?>
        <div class="table-wrapper">
          <table class="table-dashboard">
            <thead>
              <tr>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Date</th>
                <th>Statut</th>
                <th class="actions-cell">Actions</th>
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
                    <span class="badge-dashboard success">Publié</span>
                  <?php else: ?>
                    <span class="badge-dashboard warning">Brouillon</span>
                  <?php endif; ?>
                </td>
                <td class="actions-cell">
                  <a href="/expert/articles/<?= $a['id'] ?>/edit" class="btn-icon"><i class="bi bi-pencil"></i></a>
                  <a href="/blog/<?= $a['id'] ?>" class="btn-icon"><i class="bi bi-eye"></i></a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div class="empty-state-dashboard">
          <i class="bi bi-file-text"></i>
          <h5>Aucun article</h5>
          <p>Aucun article pour le moment.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
