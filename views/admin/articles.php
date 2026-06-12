<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4" data-animate="fade-up">
    <div>
      <h1 class="section-title text-white mb-1">Articles</h1>
      <p class="section-subtitle text-white-50 mb-0">Gérez les articles de votre blog</p>
    </div>
    <a href="/admin/articles/create" class="btn btn-luma">Nouvel article</a>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($articles)): ?>
  <div class="empty-state card card-luma p-5 text-center" data-animate="fade-up">
    <div class="stat-icon text-light-pink mb-3">
      <i class="bi bi-file-earmark-text fs-1"></i>
    </div>
    <h4 class="text-white mb-2">Aucun article</h4>
    <p class="text-white-50 mb-4">Commencez par créer votre premier article.</p>
    <a href="/admin/articles/create" class="btn btn-luma">Créer un article</a>
  </div>
  <?php else: ?>
  <div class="card card-luma" data-animate="fade-up">
    <div class="card-body p-0">
      <table class="table table-luma mb-0">
        <thead>
          <tr>
            <th>Titre</th>
            <th>Catégorie</th>
            <th>Auteur</th>
            <th>Statut</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($articles as $article): ?>
          <tr>
            <td><?= htmlspecialchars($article['title']) ?></td>
            <td><?= htmlspecialchars($article['category_name']) ?></td>
            <td><?= htmlspecialchars($article['author_name']) ?></td>
            <td>
              <?php if ($article['status'] === 'published'): ?>
              <span class="badge bg-success">Publié</span>
              <?php else: ?>
              <span class="badge bg-secondary">Brouillon</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($article['created_at']) ?></td>
            <td>
              <a href="/admin/articles/edit/<?= $article['id'] ?>" class="btn btn-outline-luma btn-sm">Modifier</a>
              <form action="/admin/articles/delete/<?= $article['id'] ?>" method="post" class="d-inline">
                <button type="submit" class="btn btn-outline-danger-luma btn-sm" onclick="return confirm('Supprimer cet article ?')">Supprimer</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if (!empty($pagination)): ?>
  <nav class="mt-4" data-animate="fade-up">
    <ul class="pagination pagination-dark justify-content-center">
      <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
      <li class="page-item <?= $i === $pagination['current'] ? 'active' : '' ?>">
        <a class="page-link" href="/admin/articles?page=<?= $i ?>"><?= $i ?></a>
      </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <?php endif; ?>
  <?php endif; ?>
</div>
