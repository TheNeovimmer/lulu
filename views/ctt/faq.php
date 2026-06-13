<div class="row-dashboard">
  <div>
    <div class="card-dashboard form-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Nouvelle entrée FAQ</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/ctt/faq">
          <div class="form-floating">
            <select name="category" class="form-select" required>
              <option value="">Choisir...</option>
              <option value="compte">Compte</option>
              <option value="grossesse">Grossesse</option>
              <option value="bebe">Bébé</option>
              <option value="vaccination">Vaccination</option>
              <option value="abonnement">Abonnement</option>
              <option value="technique">Problème technique</option>
              <option value="autre">Autre</option>
            </select>
            <label>Catégorie</label>
          </div>
          <div class="form-floating">
            <input type="text" name="question" class="form-control" placeholder="Question" required>
            <label>Question</label>
          </div>
          <div class="form-floating">
            <textarea name="answer" class="form-control" rows="4" placeholder="Réponse" required></textarea>
            <label>Réponse</label>
          </div>
          <button type="submit" class="btn btn-dashboard btn-dashboard-primary">Ajouter</button>
        </form>
      </div>
    </div>
  </div>

  <div>
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">FAQ existante</h5>
      </div>
      <div class="card-dashboard-body">
        <?php if (!empty($faqs)): ?>
          <?php foreach ($faqs as $category => $items): ?>
          <h6 class="fw-semibold mt-3 mb-2" style="color:var(--dprimary);"><?= htmlspecialchars(ucfirst($category)) ?></h6>
          <?php foreach ($items as $f): ?>
          <div class="bg-light p-3 rounded-3 mb-2" style="background: var(--dbg-body);">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="fw-semibold mb-1"><?= htmlspecialchars($f['question']) ?></p>
                <p class="text-muted small mb-0"><?= htmlspecialchars($f['answer']) ?></p>
              </div>
              <form method="POST" action="/ctt/faq/<?= $f['id'] ?>/delete" onsubmit="return confirm('Supprimer cette entrée ?')">
                <button class="btn-icon danger" title="Supprimer"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </div>
          <?php endforeach; ?>
          <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state-dashboard">
          <i class="bi bi-question-circle"></i>
          <h5>Aucune entrée FAQ</h5>
          <p>Aucune entrée FAQ pour le moment.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
