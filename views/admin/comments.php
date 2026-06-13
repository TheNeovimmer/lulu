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
          <td class="td-muted"><?= htmlspecialchars($comment['author_name']) ?></td>
          <td class="td-muted"><?= htmlspecialchars($comment['source_title']) ?></td>
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
            <a href="/admin/comments/approve/<?= $comment['id'] ?>" class="btn-icon success"><i class="bi bi-check-circle"></i></a>
            <?php endif; ?>
            <?php if ($comment['status'] !== 'rejected'): ?>
            <a href="/admin/comments/reject/<?= $comment['id'] ?>" class="btn-icon danger"><i class="bi bi-x-circle"></i></a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
