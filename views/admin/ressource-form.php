<div class="container-fluid py-4">
  <div data-animate="fade-up">
    <h1 class="section-title text-white mb-1">Nouvelle ressource</h1>
    <p class="section-subtitle text-white-50 mb-4">Ajoutez une ressource PDF téléchargeable pour les mamans</p>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="card card-luma animate-scale-in" data-animate="fade-up">
    <div class="card-body">
      <form action="/admin/ressources/create" method="post" enctype="multipart/form-data">
        <div class="row g-4">
          <div class="col-md-8">
            <div class="mb-3">
              <label class="form-label text-white-50">Titre</label>
              <input type="text" name="title" class="form-control form-control-luma" required>
            </div>
            <div class="mb-3">
              <label class="form-label text-white-50">Description</label>
              <textarea name="description" class="form-control form-control-luma" rows="8"></textarea>
            </div>
          </div>
          <div class="col-md-4">
            <div class="mb-3">
              <label class="form-label text-white-50">Catégorie</label>
              <select name="category_id" class="form-control form-control-luma">
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label text-white-50">Fichier PDF</label>
              <input type="file" name="pdf" class="form-control form-control-luma" accept=".pdf" required>
            </div>
            <button type="submit" class="btn btn-luma w-100">Créer la ressource</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
