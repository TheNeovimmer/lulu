<div class="row justify-content-center">
  <div class="col-lg-8">
    <h1 class="font-heading mb-4"><i class="bi bi-emoji-smile me-2 text-pink"></i>Mon Bébé</h1>

    <?php if (!empty($baby)): ?>
    <div class="row g-4 mb-4 animate-stagger" data-animate="fade-up">
      <div class="col-md-3">
        <div class="stat-card">
          <i class="stat-icon bi bi-person"></i>
          <div class="stat-number"><?= htmlspecialchars($baby['name']) ?></div>
          <div class="stat-label">Prénom</div>
          <div class="stat-accent"></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <i class="stat-icon bi bi-calendar"></i>
          <div class="stat-number"><?= date('d/m/Y', strtotime($baby['birth_date'])) ?></div>
          <div class="stat-label">Naissance</div>
          <div class="stat-accent"></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <i class="stat-icon bi bi-gender-<?= $baby['gender'] === 'fille' ? 'female' : 'male' ?>"></i>
          <div class="stat-number"><?= ucfirst($baby['gender']) ?></div>
          <div class="stat-label">Sexe</div>
          <div class="stat-accent"></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <i class="stat-icon bi bi-speedometer2"></i>
          <div class="stat-number"><?= number_format($baby['weight'], 2) ?> kg</div>
          <div class="stat-label">Poids</div>
          <div class="stat-accent"></div>
        </div>
      </div>
    </div>

    <div class="card-luma p-4" data-animate="fade-up">
      <h5 class="font-heading mb-3">Modifier les informations</h5>
      <form method="POST" action="/dashboard/bebe">
        <input type="hidden" name="_method" value="PUT">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label text-white-50">Prénom</label>
            <input type="text" name="name" class="form-control form-control-luma" value="<?= htmlspecialchars($baby['name']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label text-white-50">Date de naissance</label>
            <input type="date" name="birth_date" class="form-control form-control-luma" value="<?= htmlspecialchars($baby['birth_date']) ?>" required>
          </div>
          <div class="col-md-4">
            <label class="form-label text-white-50">Sexe</label>
            <select name="gender" class="form-select form-control-luma" required>
              <option value="fille" <?= $baby['gender'] === 'fille' ? 'selected' : '' ?>>Fille</option>
              <option value="garçon" <?= $baby['gender'] === 'garçon' ? 'selected' : '' ?>>Garçon</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label text-white-50">Poids (kg)</label>
            <input type="number" step="0.01" name="weight" class="form-control form-control-luma" value="<?= htmlspecialchars($baby['weight']) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label text-white-50">Taille (cm)</label>
            <input type="number" step="0.1" name="height" class="form-control form-control-luma" value="<?= htmlspecialchars($baby['height']) ?>">
          </div>
        </div>
        <button type="submit" class="btn btn-luma mt-3">Enregistrer</button>
      </form>
    </div>
    <?php else: ?>
    <div class="card-luma p-4" data-animate="fade-up">
      <h5 class="font-heading mb-3">Ajouter mon bébé</h5>
      <form method="POST" action="/dashboard/bebe">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label text-white-50">Prénom</label>
            <input type="text" name="name" class="form-control form-control-luma" required>
          </div>
          <div class="col-md-6">
            <label class="form-label text-white-50">Date de naissance</label>
            <input type="date" name="birth_date" class="form-control form-control-luma" required>
          </div>
          <div class="col-md-4">
            <label class="form-label text-white-50">Sexe</label>
            <select name="gender" class="form-select form-control-luma" required>
              <option value="">Choisir...</option>
              <option value="fille">Fille</option>
              <option value="garçon">Garçon</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label text-white-50">Poids (kg)</label>
            <input type="number" step="0.01" name="weight" class="form-control form-control-luma">
          </div>
          <div class="col-md-4">
            <label class="form-label text-white-50">Taille (cm)</label>
            <input type="number" step="0.1" name="height" class="form-control form-control-luma">
          </div>
        </div>
        <button type="submit" class="btn btn-luma mt-3">Créer</button>
      </form>
    </div>
    <?php endif; ?>
  </div>
</div>
