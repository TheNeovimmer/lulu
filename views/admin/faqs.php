<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($groupedFaqs)): ?>
  <div class="empty-state-dashboard">
    <i class="bi bi-question-circle"></i>
    <h5>Aucune FAQ</h5>
    <p>Créez votre première question fréquente.</p>
  </div>
  <?php endif; ?>

  <div class="row-dashboard">
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Ajouter une FAQ</h5>
      </div>
      <div class="card-dashboard-body">
        <form action="/admin/faqs/create" method="post" class="form-dashboard">
          <div class="form-floating">
            <select name="category_id" class="form-select" id="category_id">
              <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <label for="category_id">Catégorie</label>
          </div>
          <div class="form-floating">
            <input type="text" name="question" class="form-control" id="question" required>
            <label for="question">Question</label>
          </div>
          <div class="form-floating">
            <textarea name="answer" class="form-control" id="answer" rows="4" required></textarea>
            <label for="answer">Réponse</label>
          </div>
          <button type="submit" class="btn-dashboard btn-dashboard-primary">Ajouter</button>
        </form>
      </div>
    </div>
    <div>
      <?php foreach ($groupedFaqs as $categoryName => $faqs): ?>
      <div class="card-dashboard">
        <div class="card-dashboard-header">
          <h5 class="card-dashboard-title"><?= htmlspecialchars($categoryName) ?></h5>
        </div>
        <div class="card-dashboard-body">
          <?php foreach ($faqs as $faq): ?>
          <div class="mb-3 pb-3 <?= !$loop->last ? 'border-bottom' : '' ?>" style="<?= !$loop->last ? 'border-color: var(--dborder-light);' : '' ?>">
            <div class="d-flex justify-content-between align-items-start">
              <h6 style="color: var(--dprimary); font-weight: 600; margin-bottom: 4px;"><?= htmlspecialchars($faq['question']) ?></h6>
              <form action="/admin/faqs/delete/<?= $faq['id'] ?>" method="post" class="inline-form">
                <button type="submit" class="btn-icon danger" onclick="return confirm('Supprimer cette FAQ ?')"><i class="bi bi-trash"></i></button>
              </form>
            </div>
            <p style="color: var(--dtext-muted); font-size: 0.85rem; margin: 0;"><?= htmlspecialchars($faq['answer']) ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
