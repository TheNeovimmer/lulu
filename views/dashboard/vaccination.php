<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="page-header-dashboard">
      <h1 class="page-title-dashboard"><i class="bi bi-shield-check me-2"></i>Vaccination</h1>
    </div>

    <?php if (empty($baby)): ?>
    <div class="empty-state-dashboard">
      <i class="bi bi-emoji-neutral"></i>
      <p>Vous devez d'abord ajouter les informations de votre bébé.</p>
      <a href="/dashboard/bebe" class="btn-dashboard btn-dashboard-primary mt-3">Ajouter mon bébé</a>
    </div>
    <?php else: ?>
    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Ajouter un vaccin</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/dashboard/vaccination" class="form-dashboard">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" name="vaccine_name" class="form-control" id="floatingVaccineName" placeholder="Nom du vaccin" required>
                <label for="floatingVaccineName">Nom du vaccin</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-floating">
                <input type="date" name="scheduled_date" class="form-control" id="floatingScheduledDate" placeholder="Date prévue">
                <label for="floatingScheduledDate">Date prévue</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-floating">
                <input type="date" name="administered_date" class="form-control" id="floatingAdminDate" placeholder="Date d'administration">
                <label for="floatingAdminDate">Date d'administration</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <textarea name="notes" class="form-control" id="floatingVaccineNotes" placeholder="Notes" rows="2" style="min-height: 80px;"></textarea>
                <label for="floatingVaccineNotes">Notes</label>
              </div>
            </div>
          </div>
          <button type="submit" class="btn-dashboard btn-dashboard-primary mt-3">Ajouter</button>
        </form>
      </div>
    </div>

    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Calendrier vaccinal</h5>
      </div>
      <div class="card-dashboard-body">
        <?php if (!empty($vaccinations)): ?>
        <div class="table-wrapper">
          <table class="table-dashboard">
            <thead>
              <tr>
                <th>Vaccin</th>
                <th>Date prévue</th>
                <th>Date administrée</th>
                <th>Statut</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($vaccinations as $v): ?>
              <tr>
                <td style="font-weight: 600;"><?= htmlspecialchars($v['vaccine_name']) ?></td>
                <td><?= $v['scheduled_date'] ? date('d/m/Y', strtotime($v['scheduled_date'])) : '-' ?></td>
                <td><?= $v['administered_date'] ? date('d/m/Y', strtotime($v['administered_date'])) : '-' ?></td>
                <td>
                  <?php if ($v['administered_date']): ?>
                    <span class="badge-dashboard success">Effectué</span>
                  <?php else: ?>
                    <span class="badge-dashboard warning">À venir</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($v['notes'] ?? '-') ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div class="empty-state-dashboard">
          <i class="bi bi-shield-x"></i>
          <p>Aucun vaccin enregistré.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
