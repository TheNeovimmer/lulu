<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($comments)): ?>
  <div class="empty-state-dashboard">
    <i class="bi bi-chat-square-text"></i>
    <h5>Aucun commentaire</h5>
    <p>Aucun commentaire en attente de modération.</p>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table class="table-dashboard">
      <thead>
        <tr>
          <th>Contenu</th>
          <th>Auteur</th>
          <th>Article / Publication</th>
          <th>Statut</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($comments as $comment): ?>
        <tr>
          <td><?= htmlspecialchars(mb_substr($comment['content'], 0, 60)) ?><?= mb_strlen($comment['content']) > 60 ? '...' : '' ?></td>
          <td class="td-muted"><?= htmlspecialchars($comment['user_name']) ?></td>
          <td class="td-muted"><?= htmlspecialchars($comment['article_title']) ?></td>
          <td>
            <?php if ($comment['status'] === 'approved'): ?>
            <span class="badge-dashboard success">Approuvé</span>
            <?php elseif ($comment['status'] === 'pending'): ?>
            <span class="badge-dashboard warning">En attente</span>
            <?php else: ?>
            <span class="badge-dashboard">Rejeté</span>
            <?php endif; ?>
          </td>
          <td class="td-muted"><?= htmlspecialchars($comment['created_at']) ?></td>
          <td class="actions-cell">
            <?php if ($comment['status'] !== 'approved'): ?>
            <form action="/admin/comments/approve/<?= $comment['id'] ?>" method="post" class="inline-form">
              <?= \App\Core\Session::csrf_field() ?>
              <button type="submit" class="btn-icon success"><i class="bi bi-check-circle"></i></button>
            </form>
            <?php endif; ?>
            <?php if ($comment['status'] !== 'rejected'): ?>
            <form action="/admin/comments/reject/<?= $comment['id'] ?>" method="post" class="inline-form">
              <?= \App\Core\Session::csrf_field() ?>
              <button type="submit" class="btn-icon danger"><i class="bi bi-x-circle"></i></button>
            </form>
            <?php endif; ?>
            <form action="/admin/comments/delete/<?= $comment['id'] ?>" method="post" class="inline-form">
              <?= \App\Core\Session::csrf_field() ?>
              <button type="submit" class="btn-icon danger" onclick="return confirm('Supprimer ce commentaire ?')"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
