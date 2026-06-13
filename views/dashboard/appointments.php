<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="page-header-dashboard">
      <div>
        <h1 class="page-title-dashboard"><i class="bi bi-calendar-event me-2"></i>Consultation d'experts</h1>
        <p style="color: var(--dtext-muted); margin-bottom: 0;">Prenez rendez-vous et discutez avec nos professionnels de santé certifiés.</p>
      </div>
      <button class="btn-dashboard btn-dashboard-primary" data-bs-toggle="modal" data-bs-target="#bookModal"><i class="bi bi-plus-circle me-1"></i>Prendre rendez-vous</button>
    </div>

    <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
      <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Mes rendez-vous</h5>
      </div>
      <div class="card-dashboard-body">
        <?php if (!empty($appointments)): ?>
        <div class="table-wrapper">
          <table class="table-dashboard">
            <thead>
              <tr>
                <th>Expert</th>
                <th>Spécialité</th>
                <th>Date &amp; Heure</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($appointments as $a): ?>
              <tr>
                <td style="font-weight: 600;"><?= htmlspecialchars($a['expert_name']) ?></td>
                <td><?= htmlspecialchars($a['expert_specialty'] ?? 'Généraliste') ?></td>
                <td><?= date('d/m/Y H:i', strtotime($a['appointment_date'])) ?></td>
                <td>
                  <?php if ($a['type'] === 'online'): ?>
                    <span class="badge-dashboard info"><i class="bi bi-laptop me-1"></i>En ligne</span>
                  <?php else: ?>
                    <span class="badge-dashboard"><i class="bi bi-building me-1"></i>En cabinet</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($a['status'] === 'confirmed'): ?>
                    <span class="badge-dashboard success">Confirmé</span>
                  <?php elseif ($a['status'] === 'cancelled'): ?>
                    <span class="badge-dashboard danger">Annulé</span>
                  <?php else: ?>
                    <span class="badge-dashboard warning">En attente</span>
                  <?php endif; ?>
                </td>
                <td><small style="color: var(--dtext-muted);"><?= htmlspecialchars($a['notes'] ?? '-') ?></small></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div class="empty-state-dashboard">
          <i class="bi bi-calendar-x"></i>
          <p>Aucun rendez-vous planifié.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="modal fade modal-dashboard" id="bookModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" style="color: var(--dprimary);">Prendre rendez-vous</h5>
            <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#bookModal"></button>
          </div>
          <form method="POST" action="/dashboard/rendez-vous/book">
            <div class="modal-body">
              <div class="form-floating mb-3">
                <select name="expert_id" class="form-select" id="floatingExpert" required>
                  <option value="">Choisir...</option>
                  <?php foreach ($experts as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?> (<?= htmlspecialchars($e['specialty'] ?? 'Généraliste') ?>)</option>
                  <?php endforeach; ?>
                </select>
                <label for="floatingExpert">Choisir un spécialiste</label>
              </div>
              <div class="form-floating mb-3">
                <input type="datetime-local" name="appointment_date" class="form-control" id="floatingApptDate" placeholder="Date et Heure" required>
                <label for="floatingApptDate">Date et Heure</label>
              </div>
              <div class="form-floating mb-3">
                <select name="type" class="form-select" id="floatingType">
                  <option value="online">Téléconsultation en ligne</option>
                  <option value="in_person">En présentiel (Cabinet)</option>
                </select>
                <label for="floatingType">Type de consultation</label>
              </div>
              <div class="form-floating mb-3">
                <textarea name="notes" class="form-control" id="floatingApptNotes" placeholder="Notes" rows="3" style="min-height: 100px;"></textarea>
                <label for="floatingApptNotes">Notes / Symptômes / Motifs</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn-dashboard btn-dashboard-outline" data-bs-dismiss="modal">Annuler</button>
              <button type="submit" class="btn-dashboard btn-dashboard-primary">Confirmer la demande</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
