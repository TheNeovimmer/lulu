<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($posts)): ?>
  <div class="empty-state-dashboard">
    <i class="bi bi-chat-dots"></i>
    <h5>Aucune publication</h5>
    <p>La communauté n'a encore rien publié.</p>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table class="table-dashboard">
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
          <td class="td-muted"><?= htmlspecialchars($post['author_name']) ?></td>
          <td class="td-muted"><?= htmlspecialchars($post['comments_count'] ?? 0) ?></td>
          <td class="td-muted"><?= htmlspecialchars($post['created_at']) ?></td>
          <td>
            <?php if ($post['status'] === 'published'): ?>
            <span class="badge-dashboard success">Visible</span>
            <?php else: ?>
            <span class="badge-dashboard">Masqué</span>
            <?php endif; ?>
          </td>
          <td class="actions-cell">
            <?php if ($post['status'] === 'published'): ?>
            <a href="/admin/communaute/hide/<?= $post['id'] ?>" class="btn-icon warning"><i class="bi bi-eye-slash"></i></a>
            <?php endif; ?>
            <button type="button" class="btn-icon danger" data-bs-toggle="modal" data-bs-target="#deletePostModal<?= $post['id'] ?>"><i class="bi bi-trash"></i></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <?php foreach ($posts as $post): ?>
  <div class="modal fade modal-dashboard" id="deletePostModal<?= $post['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmer la suppression</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Supprimer cette publication ?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-dashboard btn-dashboard-outline" data-bs-dismiss="modal">Annuler</button>
          <form action="/admin/communaute/delete/<?= $post['id'] ?>" method="post" class="inline-form">
            <?= \App\Core\Session::csrf_field() ?>
            <button type="submit" class="btn-dashboard btn-dashboard-primary">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
