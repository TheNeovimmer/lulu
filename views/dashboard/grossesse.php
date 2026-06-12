<div class="row justify-content-center">
  <div class="col-lg-8">
    <h1 class="font-heading mb-4"><i class="bi bi-flower1 me-2 text-pink"></i>Ma Grossesse</h1>

    <?php if (!empty($pregnancy)): ?>
    <div class="row g-4 mb-4" data-animate="fade-up">
      <div class="col-md-6">
        <div class="stat-card">
          <i class="stat-icon bi bi-calendar-heart"></i>
          <div class="stat-number"><?= date('d/m/Y', strtotime($pregnancy['due_date'])) ?></div>
          <div class="stat-label">Date prévue d'accouchement</div>
          <div class="stat-accent"></div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="stat-card">
          <i class="stat-icon bi bi-clock"></i>
          <div class="stat-number"><?= $pregnancy['weeks'] ?> sem.</div>
          <div class="stat-label">Semaine de grossesse</div>
          <div class="stat-accent"></div>
        </div>
      </div>
    </div>

    <div class="card-luma p-4 mb-4" data-animate="fade-up">
      <h5 class="font-heading mb-3">Modifier ma grossesse</h5>
      <form method="POST" action="/dashboard/grossesse">
        <input type="hidden" name="_method" value="PUT">
        <div class="mb-3">
          <label class="form-label text-white-50">Date prévue d'accouchement</label>
          <input type="date" name="due_date" class="form-control form-control-luma" value="<?= htmlspecialchars($pregnancy['due_date']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label text-white-50">Notes</label>
          <textarea name="notes" class="form-control form-control-luma" rows="3"><?= htmlspecialchars($pregnancy['notes'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-luma">Mettre à jour</button>
      </form>
    </div>
    <?php else: ?>
    <div class="card-luma p-4" data-animate="fade-up">
      <h5 class="font-heading mb-3">Ajouter ma grossesse</h5>
      <form method="POST" action="/dashboard/grossesse">
        <div class="mb-3">
          <label class="form-label text-white-50">Date prévue d'accouchement</label>
          <input type="date" name="due_date" class="form-control form-control-luma" required>
        </div>
        <div class="mb-3">
          <label class="form-label text-white-50">Notes</label>
          <textarea name="notes" class="form-control form-control-luma" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-luma">Créer</button>
      </form>
    </div>
    <?php endif; ?>
  </div>
</div>
