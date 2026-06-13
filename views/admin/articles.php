<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="page-header-dashboard">
    <h1 class="page-title-dashboard">Articles</h1>
    <div class="page-actions-dashboard">
      <a href="/admin/articles/create" class="btn-dashboard btn-dashboard-primary">Nouvel article</a>
    </div>
  </div>

  <?php if (empty($articles)): ?>
  <div class="empty-state-dashboard">
    <i class="bi bi-file-earmark-text"></i>
    <h5>Aucun article</h5>
    <p>Commencez par créer votre premier article.</p>
    <a href="/admin/articles/create" class="btn-dashboard btn-dashboard-primary">Créer un article</a>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table class="table-dashboard">
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
          <td class="td-muted"><?= htmlspecialchars($article['category_name']) ?></td>
          <td class="td-muted"><?= htmlspecialchars($article['author_name'] ?? $article['user_name'] ?? '') ?></td>
          <td>
            <?php if ($article['status'] === 'published'): ?>
            <span class="badge-dashboard success">Publié</span>
            <?php else: ?>
            <span class="badge-dashboard warning">Brouillon</span>
            <?php endif; ?>
          </td>
          <td class="td-muted"><?= htmlspecialchars($article['created_at']) ?></td>
          <td class="actions-cell">
            <a href="/admin/articles/edit/<?= $article['id'] ?>" class="btn-icon"><i class="bi bi-pencil"></i></a>
            <form action="/admin/articles/delete/<?= $article['id'] ?>" method="post" class="inline-form">
              <?= \App\Core\Session::csrf_field() ?>
              <button type="submit" class="btn-icon danger" onclick="return confirm('Supprimer cet article ?')"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if (!empty($pagination)): ?>
  <nav class="pagination-dashboard">
    <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
    <span class="page-item <?= $i === $pagination['current'] ? 'active' : '' ?>">
      <a class="page-link" href="/admin/articles?page=<?= $i ?>"><?= $i ?></a>
    </span>
    <?php endfor; ?>
  </nav>
  <?php endif; ?>
  <?php endif; ?>
</div>
