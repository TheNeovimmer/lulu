<div class="row justify-content-center">
  <div class="col-lg-8">
    <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <form method="POST" action="/expert/profil" enctype="multipart/form-data" class="card-dashboard form-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title"><i class="bi bi-file-earmark-person me-2"></i>Profil Professionnel</h5>
      </div>
      <div class="card-dashboard-body">
        <div class="text-center mb-4">
          <?php if (!empty($expert['avatar'])): ?>
            <img src="/uploads/avatars/<?= htmlspecialchars($expert['avatar']) ?>" alt="Avatar" class="rounded-circle" width="120" height="120" style="object-fit:cover; border: 2px solid var(--dborder);">
          <?php else: ?>
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:120px;height:120px;background:var(--dprimary-subtle);">
              <i class="bi bi-person" style="color:var(--dprimary); font-size:2.5rem;"></i>
            </div>
          <?php endif; ?>
          <div class="mt-2">
            <label class="btn btn-dashboard btn-dashboard-outline btn-dashboard-sm">
              Changer la photo <input type="file" name="avatar" class="d-none" accept="image/*">
            </label>
          </div>
        </div>

        <div class="form-floating">
          <textarea name="bio" class="form-control" rows="4" placeholder="Biographie"><?= htmlspecialchars($expert['bio'] ?? '') ?></textarea>
          <label>Biographie</label>
        </div>
        <div class="form-floating">
          <input type="text" name="expertise_areas" class="form-control" value="<?= htmlspecialchars($expert['expertise_areas'] ?? '') ?>" placeholder="Ex: Pédiatrie, Allaitement, Nutrition">
          <label>Domaines d'expertise</label>
        </div>
        <div class="mb-3 small text-muted">Séparez les domaines par des virgules.</div>
        <button type="submit" class="btn btn-dashboard btn-dashboard-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</div>
