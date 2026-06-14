<!-- Hero -->
<section class="contact-hero" id="contact-hero" data-animate="fade-up">
  <div class="contact-hero-bg" aria-hidden="true"></div>
  <div class="contact-hero-overlay" aria-hidden="true"></div>
  <div class="contact-hero-ring" aria-hidden="true"></div>
  <div class="container position-relative" style="z-index:2;">
    <div class="row">
      <div class="col-lg-7">
        <div class="contact-hero-label" data-animate="fade-up">Contact</div>
        <div class="contact-hero-title-group">
          <span class="contact-hero-title">Nous sommes la pour <span class="text-pink">vous</span></span>
        </div>
        <p class="contact-hero-sub">
          Une question, un besoin d'accompagnement ou une suggestion ? L'equipe Luma est a votre ecoute avec bienveillance.
        </p>
      </div>
    </div>
  </div>
  <div class="contact-hero-scroll" aria-hidden="true">
    <i class="bi bi-chevron-down"></i>
  </div>
</section>

<!-- Contact Content -->
<section class="contact-section" data-animate="fade-up">
  <div class="container">
    <div class="row g-5">
      <!-- Contact Methods -->
      <div class="col-lg-5">
        <h2 class="contact-section-title">Autres moyens de nous contacter</h2>
        <div class="contact-methods-list">
          <div class="contact-method-card" data-animate="fade-up">
            <div class="contact-method-icon">
              <i class="bi bi-chat-dots"></i>
            </div>
            <div class="contact-method-body">
              <h4 class="contact-method-title">Chat en direct</h4>
              <p class="contact-method-text">Discutez avec notre equipe du lundi au vendredi<br>de 9h a 18h.</p>
            </div>
          </div>
          <div class="contact-method-card" data-animate="fade-up">
            <div class="contact-method-icon">
              <i class="bi bi-envelope"></i>
            </div>
            <div class="contact-method-body">
              <h4 class="contact-method-title">Par e-mail</h4>
              <p class="contact-method-text">Envoyez-nous un message, nous vous repondrons dans les plus brefs delais.</p>
              <span class="contact-method-value">hello@luma.tn</span>
            </div>
          </div>
          <div class="contact-method-card" data-animate="fade-up">
            <div class="contact-method-icon">
              <i class="bi bi-telephone"></i>
            </div>
            <div class="contact-method-body">
              <h4 class="contact-method-title">Par telephone</h4>
              <p class="contact-method-text">Appelez-nous du lundi au vendredi<br>de 9h a 17h.</p>
              <span class="contact-method-value">+216 97 203 908</span>
            </div>
          </div>
          <div class="contact-method-card" data-animate="fade-up">
            <div class="contact-method-icon">
              <i class="bi bi-geo-alt"></i>
            </div>
            <div class="contact-method-body">
              <h4 class="contact-method-title">Notre siege</h4>
              <p class="contact-method-text">Tunis, Tunisie</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Contact Form -->
      <div class="col-lg-7" data-animate="fade-up">
        <div class="contact-form-card">
          <div class="contact-form-card-bg" aria-hidden="true"></div>
          <div class="contact-form-content">
            <h2 class="contact-form-title">Envoyez-nous un message</h2>
            <p class="contact-form-sub">Remplissez le formulaire ci-dessous, nous vous repondrons avec soin et bienveillance.</p>

            <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
              <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
            <?php endif; ?>

            <form method="POST" action="/contact">
              <?= \App\Core\Session::csrf_field() ?>
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="contact-input-wrap">
                    <i class="bi bi-person contact-input-icon"></i>
                    <input type="text" name="name" class="contact-input" placeholder="Nom" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="contact-input-wrap">
                    <i class="bi bi-person contact-input-icon"></i>
                    <input type="text" name="subject" class="contact-input" placeholder="Prenom" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="contact-input-wrap">
                    <i class="bi bi-envelope contact-input-icon"></i>
                    <input type="email" name="email" class="contact-input" placeholder="Email" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="contact-input-wrap">
                    <i class="bi bi-telephone contact-input-icon"></i>
                    <input type="tel" name="phone" class="contact-input" placeholder="Numero de Telephone">
                  </div>
                </div>
                <div class="col-12">
                  <div class="contact-input-wrap">
                    <i class="bi bi-chat-dots contact-input-icon contact-input-icon-textarea"></i>
                    <textarea name="message" class="contact-input contact-textarea" rows="4" placeholder="Votre Message" required></textarea>
                  </div>
                </div>
                <div class="col-12">
                  <button type="submit" class="contact-submit-btn">
                    <span>Envoyer un message</span>
                    <i class="bi bi-arrow-right"></i>
                  </button>
                </div>
              </div>
            </form>

            <div class="contact-secure-badge">
              <i class="bi bi-shield-check"></i>
              <span>Vos donnees sont securisees et confidentielles.</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Newsletter -->
<section class="contact-newsletter" data-animate="fade-up">
  <div class="container">
    <div class="contact-newsletter-card">
      <div class="contact-newsletter-bg" aria-hidden="true"></div>
      <div class="contact-newsletter-overlay" aria-hidden="true"></div>
      <div class="contact-newsletter-content">
        <div class="row align-items-center g-4">
          <div class="col-lg-5">
            <div class="contact-newsletter-text-group">
              <div class="contact-newsletter-icon">
                <i class="bi bi-envelope-paper"></i>
              </div>
              <h3 class="contact-newsletter-title">Restez connectee avec Luma</h3>
              <p class="contact-newsletter-sub">Recevez nos conseils, nouveautes et ressources chaque semaine.</p>
            </div>
          </div>
          <div class="col-lg-6 offset-lg-1">
            <div class="contact-newsletter-form">
              <input type="email" class="contact-newsletter-input" placeholder="Votre e-mail">
              <button class="contact-newsletter-btn">
                <span>S'abonner</span>
                <i class="bi bi-send"></i>
              </button>
            </div>
            <p class="contact-newsletter-disclaimer">Pas de spam, desinscription en 1 clic.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
