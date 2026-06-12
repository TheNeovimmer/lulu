<div class="container-fluid py-4">
  <div data-animate="fade-up">
    <h1 class="section-title text-white mb-1">Commentaires</h1>
    <p class="section-subtitle text-white-50 mb-4">Modérez les commentaires des articles et publications</p>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($comments)): ?>
  <div class="empty-state card card-luma p-5 text-center" data-animate="fade-up">
    <div class="stat-icon text-light-pink mb-3">
      <i class="bi bi-chat-square-text fs-1"></i>
    </div>
    <h4 class="text-white mb-2">Aucun commentaire</h4>
    <p class="text-white-50 mb-0">Aucun commentaire en attente de modération.</p>
  </div>
  <?php else: ?>
  <div class="card card-luma" data-animate="fade-up">
    <div class="card-body p-0">
      <table class="table table-luma mb-0">
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
            <td><?= htmlspecialchars($comment['author_name']) ?></td>
            <td><?= htmlspecialchars($comment['source_title']) ?></td>
            <td>
              <?php if ($comment['status'] === 'approved'): ?>
              <span class="badge bg-success">Approuvé</span>
              <?php elseif ($comment['status'] === 'pending'): ?>
              <span class="badge bg-warning text-dark">En attente</span>
              <?php else: ?>
              <span class="badge bg-secondary">Rejeté</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($comment['created_at']) ?></td>
            <td>
              <?php if ($comment['status'] !== 'approved'): ?>
              <a href="/admin/comments/approve/<?= $comment['id'] ?>" class="btn btn-luma btn-sm">Approuver</a>
              <?php endif; ?>
              <?php if ($comment['status'] !== 'rejected'): ?>
              <a href="/admin/comments/reject/<?= $comment['id'] ?>" class="btn btn-outline-danger-luma btn-sm">Rejeter</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
</div>
