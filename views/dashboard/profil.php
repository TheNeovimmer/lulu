<div class="row justify-content-center">
  <div class="col-lg-6">
    <h1 class="font-heading mb-4"><i class="bi bi-person me-2 text-pink"></i>Mon Profil</h1>

    <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <form method="POST" action="/dashboard/profil" class="card-luma p-4" data-animate="fade-up">
      <div class="mb-3">
        <label class="form-label text-white-50">Prénom</label>
        <input type="text" name="first_name" class="form-control form-control-luma" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label text-white-50">Nom</label>
        <input type="text" name="last_name" class="form-control form-control-luma" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label text-white-50">Email</label>
        <input type="email" name="email" class="form-control form-control-luma" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label text-white-50">Téléphone</label>
        <input type="tel" name="phone" class="form-control form-control-luma" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label text-white-50">Date de naissance</label>
        <input type="date" name="date_of_birth" class="form-control form-control-luma" value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>">
      </div>
      <button type="submit" class="btn btn-luma">Enregistrer</button>
    </form>
  </div>
</div>
