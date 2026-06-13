<section class="py-5" data-animate="fade-up">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
      <div>
        <h1 class="section-title text-start">Nos Experts <span class="text-light-pink">LUMA</span></h1>
        <p class="section-subtitle text-start mb-0">Consultez et échangez avec des professionnels de santé certifiés.</p>
      </div>
    </div>

    <div class="row g-4 mt-2">
      <?php if (!empty($experts)): ?>
        <?php foreach ($experts as $e): ?>
          <div class="col-md-4 col-sm-6">
            <div class="card-testimonial h-100 d-flex flex-column text-center">
              <div class="position-relative mx-auto mb-3" style="width: 100px; height: 100px;">
                <img src="<?= $e['avatar'] ?: '/assets/images/home/avatar-placeholder.png' ?>" 
                     alt="" class="rounded-circle border border-pink" style="width: 100px; height: 100px; object-fit: cover;">
              </div>
              <h5 class="font-heading mb-1 text-white"><?= htmlspecialchars($e['name']) ?></h5>
              <span class="badge bg-luma mb-3 align-self-center"><?= htmlspecialchars($e['specialty'] ?? 'Médecin Généraliste') ?></span>
              <p class="text-white-50 small flex-grow-1"><?= htmlspecialchars($e['bio'] ? (strlen($e['bio']) > 120 ? substr($e['bio'], 0, 117) . '...' : $e['bio']) : 'Aucune description disponible.') ?></p>
              
              <div class="mt-auto pt-3 border-top border-secondary d-flex justify-content-center gap-2">
                <a href="/experts/<?= $e['id'] ?>" class="btn btn-outline-luma btn-sm">Voir profil</a>
                <a href="/dashboard/messagerie?partner_id=<?= $e['id'] ?>" class="btn btn-luma btn-sm"><i class="bi bi-chat-dots me-1"></i>Contacter</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="card-luma p-5 text-center">
            <i class="bi bi-person-x text-pink fs-1 mb-3"></i>
            <p class="text-white-50">Aucun expert n'est enregistré pour le moment.</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
