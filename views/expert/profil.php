<div class="row justify-content-center">
  <div class="col-lg-8">
    <h1 class="font-heading mb-4"><i class="bi bi-file-earmark-person me-2 text-pink"></i>Profil Professionnel</h1>

    <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <form method="POST" action="/expert/profil" enctype="multipart/form-data" class="card-luma p-4" data-animate="fade-up">
      <div class="text-center mb-4">
        <?php if (!empty($expert['avatar'])): ?>
          <img src="/uploads/avatars/<?= htmlspecialchars($expert['avatar']) ?>" alt="Avatar" class="rounded-circle" width="120" height="120" style="object-fit:cover;">
        <?php else: ?>
          <div class="rounded-circle bg-pink d-inline-flex align-items-center justify-content-center" style="width:120px;height:120px;">
            <i class="bi bi-person text-white fs-1"></i>
          </div>
        <?php endif; ?>
        <div class="mt-2">
          <label class="btn btn-outline-luma btn-sm">
            Changer la photo <input type="file" name="avatar" class="d-none" accept="image/*">
          </label>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label text-white-50">Biographie</label>
        <textarea name="bio" class="form-control form-control-luma" rows="4"><?= htmlspecialchars($expert['bio'] ?? '') ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label text-white-50">Domaines d'expertise</label>
        <input type="text" name="expertise_areas" class="form-control form-control-luma" value="<?= htmlspecialchars($expert['expertise_areas'] ?? '') ?>" placeholder="Ex: Pédiatrie, Allaitement, Nutrition">
        <small class="text-white-50">Séparez les domaines par des virgules.</small>
      </div>
      <button type="submit" class="btn btn-luma">Enregistrer</button>
    </form>
  </div>
</div>
