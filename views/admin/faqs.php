<div class="container-fluid py-4">
  <div data-animate="fade-up">
    <h1 class="section-title text-white mb-1">FAQ</h1>
    <p class="section-subtitle text-white-50 mb-4">Gérez les questions fréquentes</p>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($groupedFaqs)): ?>
  <div class="empty-state card card-luma p-5 text-center" data-animate="fade-up">
    <div class="stat-icon text-light-pink mb-3">
      <i class="bi bi-question-circle fs-1"></i>
    </div>
    <h4 class="text-white mb-2">Aucune FAQ</h4>
    <p class="text-white-50 mb-4">Créez votre première question fréquente.</p>
  </div>
  <?php endif; ?>

  <div class="row g-4" data-animate="fade-up">
    <div class="col-md-4">
      <div class="card card-luma">
        <div class="card-header bg-transparent border-pink">
          <h5 class="text-white font-heading mb-0">Ajouter une FAQ</h5>
        </div>
        <div class="card-body">
          <form action="/admin/faqs/create" method="post">
            <div class="mb-3">
              <label class="form-label text-white-50">Catégorie</label>
              <select name="category_id" class="form-control form-control-luma">
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
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
            <button type="submit" class="btn btn-luma w-100">Ajouter</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <?php foreach ($groupedFaqs as $categoryName => $faqs): ?>
      <div class="card card-luma mb-4" data-animate="fade-up">
        <div class="card-header bg-transparent border-pink">
          <h5 class="text-white font-heading mb-0"><?= htmlspecialchars($categoryName) ?></h5>
        </div>
        <div class="card-body">
          <?php foreach ($faqs as $faq): ?>
          <div class="mb-3 pb-3 <?= !$loop->last ? 'border-bottom border-pink' : '' ?>">
            <div class="d-flex justify-content-between align-items-start">
              <h6 class="text-pink mb-1"><?= htmlspecialchars($faq['question']) ?></h6>
              <form action="/admin/faqs/delete/<?= $faq['id'] ?>" method="post" class="d-inline">
                <button type="submit" class="btn btn-outline-danger-luma btn-sm" onclick="return confirm('Supprimer cette FAQ ?')">Supprimer</button>
              </form>
            </div>
            <p class="text-white-50 mb-0"><?= htmlspecialchars($faq['answer']) ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
