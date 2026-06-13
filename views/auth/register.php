<div class="container py-5" data-animate="fade-up">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card card-luma p-4">
        <h1 class="font-heading text-center mb-1" style="font-size:48px;">Créer un compte</h1>
        <p class="text-center text-white-50 mb-4">Rejoignez la communauté LUMA</p>

        <?php if ($errors = \App\Core\Session::getFlash('errors')): ?>
          <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
          <?php endforeach; ?>
        <?php endif; ?>

        <form method="POST" action="/auth/register">
          <?= \App\Core\Session::csrf_field() ?>
          <div class="mb-3">
            <input type="text" name="name" class="form-control form-control-luma" placeholder="Nom complet" required>
          </div>
          <div class="mb-3">
            <input type="email" name="email" class="form-control form-control-luma" placeholder="Email" required>
          </div>
          <div class="mb-3">
            <label class="form-label text-white-50">Je suis...</label>
            <div class="animate-stagger d-flex gap-3">
              <label class="role-card flex-fill text-center">
                <input type="radio" name="role" value="maman" class="d-none" required>
                <div class="role-icon">
                  <i class="bi bi-heart-pulse fs-2"></i>
                </div>
                <span class="d-block mt-2 fw-bold">Future maman / Maman</span>
              </label>
              <label class="role-card flex-fill text-center">
                <input type="radio" name="role" value="expert" class="d-none" required>
                <div class="role-icon">
                  <i class="bi bi-star fs-2"></i>
                </div>
                <span class="d-block mt-2 fw-bold">Expert (sage-femme, pédiatre...)</span>
              </label>
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <input type="password" name="password" class="form-control form-control-luma" placeholder="Mot de passe (6 min)" required>
            </div>
            <div class="col-md-6">
              <input type="password" name="password_confirm" class="form-control form-control-luma" placeholder="Confirmer" required>
            </div>
          </div>
          <button type="submit" class="btn btn-luma w-100">S'inscrire</button>
        </form>
        <p class="text-center text-white-50 mt-4">
          Déjà inscrite ? <a href="/auth/login" class="text-light-pink">Se connecter</a>
        </p>
      </div>
    </div>
  </div>
</div>
