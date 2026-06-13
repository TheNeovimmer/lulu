<div class="container py-5" data-animate="fade-up">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card card-luma p-4">
        <h1 class="font-heading text-center mb-1" style="font-size:48px;">Connexion</h1>
        <p class="text-center text-white-50 mb-4">Retrouvez votre espace LUMA</p>

        <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
          <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <form method="POST" action="/auth/login">
          <?= \App\Core\Session::csrf_field() ?>
          <div class="mb-3">
            <input type="email" name="email" class="form-control form-control-luma" placeholder="Votre email" required>
          </div>
          <div class="mb-3">
            <input type="password" name="password" class="form-control form-control-luma" placeholder="Mot de passe" required>
          </div>
          <button type="submit" class="btn btn-luma w-100">Se connecter</button>
        </form>
        <p class="text-center text-white-50 mt-4">
          Pas encore de compte ? <a href="/auth/register" class="text-light-pink">S'inscrire</a>
        </p>
      </div>
    </div>
  </div>
</div>
