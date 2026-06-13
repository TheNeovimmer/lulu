<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($testimonials)): ?>
  <div class="empty-state-dashboard">
    <i class="bi bi-star"></i>
    <h5>Aucun témoignage</h5>
    <p>Aucun témoignage soumis pour le moment.</p>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table class="table-dashboard">
      <thead>
        <tr>
          <th>Contenu</th>
          <th>Auteur</th>
          <th>Note</th>
          <th>Statut</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($testimonials as $testimonial): ?>
        <tr>
          <td><?= htmlspecialchars(mb_substr($testimonial['content'], 0, 80)) ?><?= mb_strlen($testimonial['content']) > 80 ? '...' : '' ?></td>
          <td class="td-muted"><?= htmlspecialchars($testimonial['author_name']) ?></td>
          <td style="color: var(--dprimary);">
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <span style="color: <?= $i <= $testimonial['rating'] ? 'var(--dprimary)' : 'var(--dborder)' ?>;">&#9733;</span>
            <?php endfor; ?>
          </td>
          <td>
            <?php if ($testimonial['status'] === 'approved'): ?>
            <span class="badge-dashboard success">Approuvé</span>
            <?php elseif ($testimonial['status'] === 'pending'): ?>
            <span class="badge-dashboard warning">En attente</span>
            <?php else: ?>
            <span class="badge-dashboard">Rejeté</span>
            <?php endif; ?>
          </td>
          <td class="td-muted"><?= htmlspecialchars($testimonial['created_at']) ?></td>
          <td class="actions-cell">
            <?php if ($testimonial['status'] !== 'approved'): ?>
            <a href="/admin/testimonials/approve/<?= $testimonial['id'] ?>" class="btn-icon success"><i class="bi bi-check-circle"></i></a>
            <?php endif; ?>
            <?php if ($testimonial['status'] !== 'rejected'): ?>
            <a href="/admin/testimonials/reject/<?= $testimonial['id'] ?>" class="btn-icon danger"><i class="bi bi-x-circle"></i></a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
