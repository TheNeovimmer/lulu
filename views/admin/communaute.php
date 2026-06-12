<div class="container-fluid py-4">
  <div data-animate="fade-up">
    <h1 class="section-title text-white mb-1">Communauté</h1>
    <p class="section-subtitle text-white-50 mb-4">Modérez les publications de la communauté</p>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($posts)): ?>
  <div class="empty-state card card-luma p-5 text-center" data-animate="fade-up">
    <div class="stat-icon text-light-pink mb-3">
      <i class="bi bi-chat-dots fs-1"></i>
    </div>
    <h4 class="text-white mb-2">Aucune publication</h4>
    <p class="text-white-50 mb-0">La communauté n'a encore rien publié.</p>
  </div>
  <?php else: ?>
  <div class="card card-luma" data-animate="fade-up">
    <div class="card-body p-0">
      <table class="table table-luma mb-0">
        <thead>
          <tr>
            <th>Contenu</th>
            <th>Auteur</th>
            <th>Commentaires</th>
            <th>Date</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $post): ?>
          <tr>
            <td><?= htmlspecialchars(mb_substr($post['content'], 0, 80)) ?><?= mb_strlen($post['content']) > 80 ? '...' : '' ?></td>
            <td><?= htmlspecialchars($post['author_name']) ?></td>
            <td><?= htmlspecialchars($post['comments_count']) ?></td>
            <td><?= htmlspecialchars($post['created_at']) ?></td>
            <td>
              <?php if ($post['visible']): ?>
              <span class="badge bg-success">Visible</span>
              <?php else: ?>
              <span class="badge bg-secondary">Masqué</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($post['visible']): ?>
              <a href="/admin/communaute/hide/<?= $post['id'] ?>" class="btn btn-outline-luma btn-sm">Masquer</a>
              <?php else: ?>
              <a href="/admin/communaute/show/<?= $post['id'] ?>" class="btn btn-luma btn-sm">Afficher</a>
              <?php endif; ?>
              <button type="button" class="btn btn-outline-danger-luma btn-sm" data-bs-toggle="modal" data-bs-target="#deletePostModal<?= $post['id'] ?>">Supprimer</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <?php foreach ($posts as $post): ?>
  <div class="modal fade" id="deletePostModal<?= $post['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content bg-luma-glass">
        <div class="modal-header border-pink">
          <h5 class="modal-title text-white font-heading">Confirmer la suppression</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-white">
          Supprimer cette publication ?
        </div>
        <div class="modal-footer border-pink">
          <button type="button" class="btn btn-outline-luma" data-bs-dismiss="modal">Annuler</button>
          <form action="/admin/communaute/delete/<?= $post['id'] ?>" method="post" class="d-inline">
            <button type="submit" class="btn btn-danger">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
