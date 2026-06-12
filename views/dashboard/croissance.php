<div class="row justify-content-center">
  <div class="col-lg-10">
    <h1 class="font-heading mb-4"><i class="bi bi-graph-up me-2 text-pink"></i>Suivi de Croissance</h1>

    <?php if (empty($baby)): ?>
    <div class="empty-state" data-animate="fade-up">
      <i class="bi bi-emoji-neutral empty-state-icon"></i>
      <p>Vous devez d'abord ajouter les informations de votre bébé.</p>
      <a href="/dashboard/bebe" class="btn btn-luma">Ajouter mon bébé</a>
    </div>
    <?php else: ?>
    <div class="card-luma p-4 mb-4" data-animate="fade-up">
      <h5 class="section-title">Nouvelle mesure</h5>
      <div class="divider-accent"></div>
      <form method="POST" action="/dashboard/croissance">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label text-white-50">Poids (kg)</label>
            <input type="number" step="0.01" name="weight" class="form-control form-control-luma" required>
          </div>
          <div class="col-md-3">
            <label class="form-label text-white-50">Taille (cm)</label>
            <input type="number" step="0.1" name="height" class="form-control form-control-luma" required>
          </div>
          <div class="col-md-3">
            <label class="form-label text-white-50">Périmètre crânien (cm)</label>
            <input type="number" step="0.1" name="head_circumference" class="form-control form-control-luma">
          </div>
          <div class="col-md-3">
            <label class="form-label text-white-50">Date</label>
            <input type="date" name="measured_at" class="form-control form-control-luma" value="<?= date('Y-m-d') ?>" required>
          </div>
        </div>
        <button type="submit" class="btn btn-luma mt-3">Ajouter</button>
      </form>
    </div>

    <div class="card-luma p-4" data-animate="fade-up">
      <h5 class="section-title">Historique des mesures</h5>
      <div class="divider-accent"></div>
      <?php if (!empty($records)): ?>
      <div class="table-responsive">
        <table class="table table-luma">
          <thead>
            <tr>
              <th>Date</th>
              <th>Poids (kg)</th>
              <th>Taille (cm)</th>
              <th>Périmètre crânien (cm)</th>
              <th>Âge</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($records as $r): ?>
            <tr>
              <td><?= date('d/m/Y', strtotime($r['measured_at'])) ?></td>
              <td><?= number_format($r['weight'], 2) ?></td>
              <td><?= number_format($r['height'], 1) ?></td>
              <td><?= $r['head_circumference'] ? number_format($r['head_circumference'], 1) : '-' ?></td>
              <td><?= $r['age_days'] ?> jours</td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-graph-down empty-state-icon"></i>
        <p class="text-white-50">Aucune mesure enregistrée.</p>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
