<div class="row-dashboard">
  <div>
    <div class="card-dashboard form-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Soumettre un témoignage</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/dashboard/temoignages">
          <?= \App\Core\Session::csrf_field() ?>
          <div class="form-floating">
            <textarea name="content" class="form-control" rows="5" placeholder="Votre témoignage" required></textarea>
            <label>Votre témoignage</label>
          </div>
          <div class="form-floating">
            <select name="rating" class="form-select" required>
              <option value="5">5 - Excellent</option>
              <option value="4">4 - Très bien</option>
              <option value="3">3 - Bien</option>
              <option value="2">2 - Moyen</option>
              <option value="1">1 - Médiocre</option>
            </select>
            <label>Note</label>
          </div>
          <button type="submit" class="btn btn-dashboard btn-dashboard-primary">Soumettre</button>
        </form>
      </div>
    </div>
  </div>

  <div>
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Mes témoignages</h5>
      </div>
      <div class="card-dashboard-body">
        <?php if (!empty($testimonials)): ?>
          <?php foreach ($testimonials as $t): ?>
          <div class="testimonial-item">
            <div class="testimonial-rating">
              <?php for ($i = 1; $i <= 5; $i++): ?>
              <span style="color: <?= $i <= $t['rating'] ? 'var(--dprimary)' : 'var(--dborder)' ?>;">&#9733;</span>
              <?php endfor; ?>
            </div>
            <p><?= htmlspecialchars($t['content']) ?></p>
            <small class="td-muted">
              <?= date('d/m/Y', strtotime($t['created_at'])) ?> &middot;
              <?php if ($t['status'] === 'approved'): ?>
              <span class="badge-dashboard success">Approuvé</span>
              <?php elseif ($t['status'] === 'pending'): ?>
              <span class="badge-dashboard warning">En attente</span>
              <?php else: ?>
              <span class="badge-dashboard">Rejeté</span>
              <?php endif; ?>
            </small>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state-dashboard">
          <i class="bi bi-star"></i>
          <h5>Aucun témoignage</h5>
          <p>Vous n'avez pas encore soumis de témoignage.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
