<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="page-header-dashboard">
      <h1 class="page-title-dashboard"><i class="bi bi-gear me-2"></i>Paramètres</h1>
    </div>

    <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
      <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Sécurité &amp; Mot de passe</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/dashboard/parametres" class="form-dashboard">
          <?= \App\Core\Session::csrf_field() ?>
          <div class="form-floating mb-3">
            <input type="password" name="old_password" class="form-control" id="floatingOldPassword" placeholder="Ancien mot de passe" required>
            <label for="floatingOldPassword">Ancien mot de passe</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" name="new_password" class="form-control" id="floatingNewPassword" placeholder="Nouveau mot de passe" required>
            <label for="floatingNewPassword">Nouveau mot de passe</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" name="new_password_confirm" class="form-control" id="floatingConfirmPassword" placeholder="Confirmer" required>
            <label for="floatingConfirmPassword">Confirmer le nouveau mot de passe</label>
          </div>
          <button type="submit" class="btn-dashboard btn-dashboard-primary">Mettre à jour le mot de passe</button>
        </form>
      </div>
    </div>
  </div>
</div>
