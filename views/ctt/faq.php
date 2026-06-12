<div class="row">
  <div class="col-12">
    <h1 class="font-heading mb-4"><i class="bi bi-question-circle me-2 text-pink"></i>Gestion FAQ</h1>
  </div>

  <div class="col-lg-5 mb-4" data-animate="fade-up">
    <div class="card-luma p-4">
      <h5 class="section-title">Nouvelle entrée FAQ</h5>
      <div class="divider-accent"></div>
      <form method="POST" action="/ctt/faq">
        <div class="mb-3">
          <label class="form-label text-white-50">Catégorie</label>
          <select name="category" class="form-select form-control-luma" required>
            <option value="">Choisir...</option>
            <option value="compte">Compte</option>
            <option value="grossesse">Grossesse</option>
            <option value="bebe">Bébé</option>
            <option value="vaccination">Vaccination</option>
            <option value="abonnement">Abonnement</option>
            <option value="technique">Problème technique</option>
            <option value="autre">Autre</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label text-white-50">Question</label>
          <input type="text" name="question" class="form-control form-control-luma" required>
        </div>
        <div class="mb-3">
          <label class="form-label text-white-50">Réponse</label>
          <textarea name="answer" class="form-control form-control-luma" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-luma">Ajouter</button>
      </form>
    </div>
  </div>

  <div class="col-lg-7" data-animate="fade-up">
    <div class="card-luma p-4">
      <h5 class="section-title">FAQ existante</h5>
      <div class="divider-accent"></div>
      <?php if (!empty($faqs)): ?>
        <?php foreach ($faqs as $category => $items): ?>
        <h6 class="text-light-pink mt-3 mb-2"><?= htmlspecialchars(ucfirst($category)) ?></h6>
        <?php foreach ($items as $f): ?>
        <div class="bg-luma-glass p-3 rounded-3 mb-2">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="fw-semibold mb-1"><?= htmlspecialchars($f['question']) ?></p>
              <p class="text-white-50 small mb-0"><?= htmlspecialchars($f['answer']) ?></p>
            </div>
            <form method="POST" action="/ctt/faq/<?= $f['id'] ?>/delete" onsubmit="return confirm('Supprimer cette entrée ?')">
              <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
        <?php endforeach; ?>
      <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-question-circle empty-state-icon"></i>
        <p class="text-white-50">Aucune entrée FAQ pour le moment.</p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
