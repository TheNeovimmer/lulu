<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($grouped)): ?>
  <div class="empty-state-dashboard">
    <i class="bi bi-question-circle"></i>
    <h5>Aucune FAQ</h5>
    <p>Créez votre première question fréquente.</p>
  </div>
  <?php endif; ?>

  <div class="row-dashboard">
    <?php if (isset($editFaq)): ?>
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Modifier la FAQ</h5>
      </div>
      <div class="card-dashboard-body">
        <form action="/admin/faqs/edit/<?= $editFaq['id'] ?>" method="post" class="form-dashboard">
          <?= \App\Core\Session::csrf_field() ?>
          <div class="form-floating">
            <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($editFaq['category']) ?>" required>
            <label>Catégorie</label>
          </div>
          <div class="form-floating">
            <input type="text" name="question" class="form-control" value="<?= htmlspecialchars($editFaq['question']) ?>" required>
            <label>Question</label>
          </div>
          <div class="form-floating">
            <textarea name="answer" class="form-control" rows="4" required><?= htmlspecialchars($editFaq['answer']) ?></textarea>
            <label>Réponse</label>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn-dashboard btn-dashboard-primary">Enregistrer</button>
            <a href="/admin/faqs" class="btn-dashboard btn-dashboard-outline">Annuler</a>
          </div>
        </form>
      </div>
    </div>
    <?php endif; ?>
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Ajouter une FAQ</h5>
      </div>
      <div class="card-dashboard-body">
        <form action="/admin/faqs/create" method="post" class="form-dashboard">
          <?= \App\Core\Session::csrf_field() ?>
          <div class="form-floating">
            <input type="text" name="category" class="form-control" id="category_id" required>
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
      <?php foreach ($grouped as $categoryName => $faqs): ?>
      <div class="card-dashboard">
        <div class="card-dashboard-header">
          <h5 class="card-dashboard-title"><?= htmlspecialchars($categoryName) ?></h5>
        </div>
        <div class="card-dashboard-body">
          <?php $faqIndex = 0; $faqTotal = count($faqs); ?>
          <?php foreach ($faqs as $faq): ?>
          <?php $faqIndex++; $isLast = ($faqIndex === $faqTotal); ?>
          <div class="mb-3 pb-3 <?= !$isLast ? 'border-bottom' : '' ?>" style="<?= !$isLast ? 'border-color: var(--dborder-light);' : '' ?>">
            <div class="d-flex justify-content-between align-items-start">
              <h6 style="color: var(--dprimary); font-weight: 600; margin-bottom: 4px;"><?= htmlspecialchars($faq['question']) ?></h6>
              <div class="d-flex gap-1">
                <a href="/admin/faqs/edit/<?= $faq['id'] ?>" class="btn-icon"><i class="bi bi-pencil"></i></a>
                <form action="/admin/faqs/delete/<?= $faq['id'] ?>" method="post" class="inline-form">
                  <?= \App\Core\Session::csrf_field() ?>
                  <button type="submit" class="btn-icon danger" onclick="return confirm('Supprimer cette FAQ ?')"><i class="bi bi-trash"></i></button>
                </form>
              </div>
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
