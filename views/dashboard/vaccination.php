<div class="row justify-content-center">
  <div class="col-lg-10">
    <h1 class="font-heading mb-4"><i class="bi bi-shield-check me-2 text-pink"></i>Vaccination</h1>

    <?php if (empty($baby)): ?>
    <div class="empty-state" data-animate="fade-up">
      <i class="bi bi-emoji-neutral empty-state-icon"></i>
      <p>Vous devez d'abord ajouter les informations de votre bébé.</p>
      <a href="/dashboard/bebe" class="btn btn-luma">Ajouter mon bébé</a>
    </div>
    <?php else: ?>
    <div class="card-luma p-4 mb-4" data-animate="fade-up">
      <h5 class="section-title">Ajouter un vaccin</h5>
      <div class="divider-accent"></div>
      <form method="POST" action="/dashboard/vaccination">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label text-white-50">Nom du vaccin</label>
            <input type="text" name="vaccine_name" class="form-control form-control-luma" required>
          </div>
          <div class="col-md-3">
            <label class="form-label text-white-50">Date prévue</label>
            <input type="date" name="scheduled_date" class="form-control form-control-luma">
          </div>
          <div class="col-md-3">
            <label class="form-label text-white-50">Date d'administration</label>
            <input type="date" name="administered_date" class="form-control form-control-luma">
          </div>
          <div class="col-12">
            <label class="form-label text-white-50">Notes</label>
            <textarea name="notes" class="form-control form-control-luma" rows="2"></textarea>
          </div>
        </div>
        <button type="submit" class="btn btn-luma mt-3">Ajouter</button>
      </form>
    </div>

    <div class="card-luma p-4" data-animate="fade-up">
      <h5 class="section-title">Calendrier vaccinal</h5>
      <div class="divider-accent"></div>
      <?php if (!empty($vaccinations)): ?>
      <div class="table-responsive">
        <table class="table table-luma">
          <thead>
            <tr>
              <th>Vaccin</th>
              <th>Date prévue</th>
              <th>Date administrée</th>
              <th>Statut</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($vaccinations as $v): ?>
            <tr>
              <td class="fw-semibold"><?= htmlspecialchars($v['vaccine_name']) ?></td>
              <td><?= $v['scheduled_date'] ? date('d/m/Y', strtotime($v['scheduled_date'])) : '-' ?></td>
              <td><?= $v['administered_date'] ? date('d/m/Y', strtotime($v['administered_date'])) : '-' ?></td>
              <td>
                <?php if ($v['administered_date']): ?>
                  <span class="badge bg-success">Effectué</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">À venir</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($v['notes'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-shield-x empty-state-icon"></i>
        <p class="text-white-50">Aucun vaccin enregistré.</p>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
