<div class="row justify-content-center">
  <div class="col-lg-6">
    <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
      <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="card-dashboard form-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title"><i class="bi bi-gear me-2"></i>Sécurité &amp; Mot de passe</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/expert/parametres">
          <div class="form-floating">
            <input type="password" name="old_password" class="form-control" placeholder="Ancien mot de passe" required>
            <label>Ancien mot de passe</label>
          </div>
          <div class="form-floating">
            <input type="password" name="new_password" class="form-control" placeholder="Nouveau mot de passe" required>
            <label>Nouveau mot de passe</label>
          </div>
          <div class="form-floating">
            <input type="password" name="new_password_confirm" class="form-control" placeholder="Confirmer le nouveau mot de passe" required>
            <label>Confirmer le nouveau mot de passe</label>
          </div>
          <button type="submit" class="btn btn-dashboard btn-dashboard-primary">Mettre à jour le mot de passe</button>
        </form>
      </div>
    </div>
  </div>
</div>
