<div class="container-fluid py-4">
  <div data-animate="fade-up">
    <h1 class="section-title text-white mb-1">Témoignages</h1>
    <p class="section-subtitle text-white-50 mb-4">Modérez les témoignages des mamans</p>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($testimonials)): ?>
  <div class="empty-state card card-luma p-5 text-center" data-animate="fade-up">
    <div class="stat-icon text-light-pink mb-3">
      <i class="bi bi-star fs-1"></i>
    </div>
    <h4 class="text-white mb-2">Aucun témoignage</h4>
    <p class="text-white-50 mb-0">Aucun témoignage soumis pour le moment.</p>
  </div>
  <?php else: ?>
  <div class="card card-luma" data-animate="fade-up">
    <div class="card-body p-0">
      <table class="table table-luma mb-0">
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
            <td><?= htmlspecialchars($testimonial['author_name']) ?></td>
            <td>
              <?php for ($i = 1; $i <= 5; $i++): ?>
              <span class="text-pink <?= $i <= $testimonial['rating'] ? '' : 'text-white-50' ?>">&#9733;</span>
              <?php endfor; ?>
            </td>
            <td>
              <?php if ($testimonial['status'] === 'approved'): ?>
              <span class="badge bg-success">Approuvé</span>
              <?php elseif ($testimonial['status'] === 'pending'): ?>
              <span class="badge bg-warning text-dark">En attente</span>
              <?php else: ?>
              <span class="badge bg-secondary">Rejeté</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($testimonial['created_at']) ?></td>
            <td>
              <?php if ($testimonial['status'] !== 'approved'): ?>
              <a href="/admin/testimonials/approve/<?= $testimonial['id'] ?>" class="btn btn-luma btn-sm">Approuver</a>
              <?php endif; ?>
              <?php if ($testimonial['status'] !== 'rejected'): ?>
              <a href="/admin/testimonials/reject/<?= $testimonial['id'] ?>" class="btn btn-outline-danger-luma btn-sm">Rejeter</a>
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
