<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="page-header-dashboard">
      <h1 class="page-title-dashboard"><i class="bi bi-person me-2"></i>Mon Profil</h1>
    </div>

    <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <form method="POST" action="/dashboard/profil" enctype="multipart/form-data" class="card-dashboard form-dashboard">
      <?= \App\Core\Session::csrf_field() ?>
      <div class="text-center mb-4">
        <?php $avatarUrl = $_SESSION['user_avatar'] ?: ($user['avatar'] ? '/uploads/avatars/' . $user['avatar'] : ''); ?>
        <?php if ($avatarUrl): ?>
          <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="rounded-circle" width="100" height="100" style="object-fit:cover; border: 3px solid var(--dprimary-subtle);">
        <?php else: ?>
          <img src="/assets/images/default-avatar.svg" alt="Avatar" class="rounded-circle" width="100" height="100" style="object-fit:cover; border: 3px solid var(--dprimary-subtle);">
        <?php endif; ?>
        <div class="mt-2">
          <label class="btn btn-dashboard btn-dashboard-outline btn-dashboard-sm">
            Changer la photo <input type="file" name="avatar" class="d-none" accept="image/*">
          </label>
        </div>
      </div>

      <div class="form-floating mb-3">
        <input type="text" name="first_name" class="form-control" id="floatingFirstName" placeholder="Prénom" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
        <label for="floatingFirstName">Prénom</label>
      </div>
      <div class="form-floating mb-3">
        <input type="text" name="last_name" class="form-control" id="floatingLastName" placeholder="Nom" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
        <label for="floatingLastName">Nom</label>
      </div>
      <div class="form-floating mb-3">
        <input type="email" name="email" class="form-control" id="floatingEmail" placeholder="Email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
        <label for="floatingEmail">Email</label>
      </div>
      <div class="form-floating mb-3">
        <input type="tel" name="phone" class="form-control" id="floatingPhone" placeholder="Téléphone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
        <label for="floatingPhone">Téléphone</label>
      </div>
      <div class="form-floating mb-3">
        <input type="date" name="date_of_birth" class="form-control" id="floatingDob" placeholder="Date de naissance" value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>">
        <label for="floatingDob">Date de naissance</label>
      </div>
      <button type="submit" class="btn-dashboard btn-dashboard-primary">Enregistrer</button>
    </form>
  </div>
</div>
