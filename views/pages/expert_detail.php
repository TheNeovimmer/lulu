<section class="py-5" data-animate="fade-up">
  <div class="container">
    <div class="mb-4">
      <a href="/experts" class="btn btn-outline-luma btn-sm"><i class="bi bi-arrow-left me-1"></i>Retour à la liste</a>
    </div>

    <div class="row g-4">
      <!-- Profile details -->
      <div class="col-lg-4">
        <div class="card-testimonial text-center">
          <img src="<?= $expert['avatar'] ?: '/assets/images/home/avatar-placeholder.png' ?>" 
               alt="" class="rounded-circle border border-pink mb-3" style="width: 120px; height: 120px; object-fit: cover;">
          <h4 class="font-heading mb-1 text-white"><?= htmlspecialchars($expert['name']) ?></h4>
          <span class="badge bg-luma mb-4"><?= htmlspecialchars($expert['specialty'] ?? 'Médecin Généraliste') ?></span>
          
          <div class="text-start border-top border-secondary pt-3 mt-3">
            <p class="mb-2 text-white-50"><i class="bi bi-geo-alt me-2 text-pink"></i><?= htmlspecialchars($expert['address'] ?? 'Tunis, Tunisie') ?></p>
            <p class="mb-2 text-white-50"><i class="bi bi-telephone me-2 text-pink"></i><?= htmlspecialchars($expert['phone'] ?? 'Non renseigné') ?></p>
            <p class="mb-0 text-white-50"><i class="bi bi-envelope me-2 text-pink"></i><?= htmlspecialchars($expert['email']) ?></p>
          </div>

          <div class="d-grid gap-2 mt-4">
            <a href="/dashboard/messagerie?partner_id=<?= $expert['id'] ?>" class="btn btn-luma"><i class="bi bi-chat-dots me-2"></i>Contacter en ligne</a>
          </div>
        </div>
      </div>

      <!-- Bio and booking -->
      <div class="col-lg-8">
        <div class="card-luma p-4 mb-4">
          <h5 class="font-heading mb-3 text-pink">À propos de ce spécialiste</h5>
          <div class="divider-accent"></div>
          <p class="text-white-50" style="line-height: 1.6; font-size: 1.05rem;">
            <?= nl2br(htmlspecialchars($expert['bio'] ?? 'Ce professionnel n\'a pas encore rédigé sa biographie.')) ?>
          </p>
        </div>

        <div class="card-luma p-4">
          <h5 class="font-heading mb-3 text-pink">Prendre rendez-vous</h5>
          <div class="divider-accent"></div>
          <form method="POST" action="/dashboard/rendez-vous/book">
            <input type="hidden" name="expert_id" value="<?= $expert['id'] ?>">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label text-white-50">Date et Heure souhaitées</label>
                <input type="datetime-local" name="appointment_date" class="form-control form-control-luma" required>
              </div>
              <div class="col-md-6">
                <label class="form-label text-white-50">Mode de consultation</label>
                <select name="type" class="form-select form-control-luma">
                  <option value="online">Téléconsultation en ligne</option>
                  <option value="in_person">En présentiel (Cabinet)</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label text-white-50">Motif de consultation / Notes</label>
                <textarea name="notes" class="form-control form-control-luma" rows="3" placeholder="Décrivez brièvement vos symptômes ou vos questions..."></textarea>
              </div>
            </div>
            <button type="submit" class="btn btn-luma mt-3">Confirmer la demande de rendez-vous</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
