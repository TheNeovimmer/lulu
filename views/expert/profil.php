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
          <?php if (!empty($user['avatar'])): ?>
            <img src="/uploads/avatars/<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="rounded-circle" width="120" height="120" style="object-fit:cover; border: 3px solid var(--dprimary-subtle);">
          <?php else: ?>
            <img src="/assets/images/default-avatar.svg" alt="Avatar" class="rounded-circle" width="120" height="120" style="object-fit:cover; border: 3px solid var(--dprimary-subtle);">
          <?php endif; ?>
          <div class="mt-2">
            <label class="btn btn-dashboard btn-dashboard-outline btn-dashboard-sm">
              Changer la photo <input type="file" name="avatar" class="d-none" accept="image/*">
            </label>
          </div>
        </div>

        <div class="form-floating mb-3">
          <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
          <label>Nom complet</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" name="specialty" class="form-control" value="<?= htmlspecialchars($user['specialty'] ?? '') ?>">
          <label>Spécialité</label>
        </div>
        <div class="form-floating mb-3">
          <textarea name="bio" class="form-control" rows="4" placeholder="Biographie"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
          <label>Biographie</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
          <label>Téléphone</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
          <label>Adresse</label>
        </div>
        <button type="submit" class="btn btn-dashboard btn-dashboard-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</div>
