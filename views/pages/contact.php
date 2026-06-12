<section class="py-5" data-animate="fade-up">
  <div class="container">
    <div class="row g-5">
      <div class="col-md-5">
        <h1 class="font-heading" style="font-size:64px; line-height:1.1;">
          Nous sommes là pour <span class="text-pink">vous</span>
        </h1>
        <p class="fs-5 mt-3 mb-4">Une question, un besoin d'accompagnement ou une suggestion ? L'équipe Luma est à votre écoute avec bienveillance.</p>

        <div class="animate-stagger d-flex flex-column gap-4">
          <div class="d-flex align-items-center gap-3" data-animate="fade-up">
            <div class="icon-circle bg-pink">
              <i class="bi bi-chat-dots fs-3 text-white"></i>
            </div>
            <div>
              <h5 class="fw-bold">Chat en direct</h5>
              <p class="text-white-50 mb-0">Lun-Ven 9h-18h</p>
            </div>
          </div>
          <div class="d-flex align-items-center gap-3" data-animate="fade-up">
            <div class="icon-circle bg-pink">
              <i class="bi bi-envelope fs-3 text-white"></i>
            </div>
            <div>
              <h5 class="fw-bold">Par e-mail</h5>
              <p class="text-white-50 mb-0">hello@luma.tn</p>
            </div>
          </div>
          <div class="d-flex align-items-center gap-3" data-animate="fade-up">
            <div class="icon-circle bg-pink">
              <i class="bi bi-telephone fs-3 text-white"></i>
            </div>
            <div>
              <h5 class="fw-bold">Par téléphone</h5>
              <p class="text-white-50 mb-0">+216 97 203 908</p>
            </div>
          </div>
          <div class="d-flex align-items-center gap-3" data-animate="fade-up">
            <div class="icon-circle bg-pink">
              <i class="bi bi-geo-alt fs-3 text-white"></i>
            </div>
            <div>
              <h5 class="fw-bold">Notre siège</h5>
              <p class="text-white-50 mb-0">Tunis, Tunisie</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-7" data-animate="fade-up">
        <div class="card card-luma p-4">
          <h2 class="font-heading mb-2" style="font-size:36px;">Envoyez-nous un message</h2>
          <p class="text-white-50 mb-4">Remplissez le formulaire ci-dessous, nous vous répondrons avec soin et bienveillance.</p>

          <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
            <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
          <?php endif; ?>

          <form method="POST" action="/contact">
            <div class="row g-3">
              <div class="col-md-6">
                <input type="text" name="name" class="form-control form-control-luma" placeholder="Nom" required>
              </div>
              <div class="col-md-6">
                <input type="text" name="subject" class="form-control form-control-luma" placeholder="Prénom" required>
              </div>
              <div class="col-md-6">
                <input type="email" name="email" class="form-control form-control-luma" placeholder="Email" required>
              </div>
              <div class="col-md-6">
                <input type="tel" name="phone" class="form-control form-control-luma" placeholder="Numéro de Téléphone">
              </div>
              <div class="col-12">
                <textarea name="message" class="form-control form-control-luma" rows="5" placeholder="Votre Message" required style="border-radius:10px;"></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-luma w-100">Envoyer un message</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
