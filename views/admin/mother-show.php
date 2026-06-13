<div class="content-dashboard">
  <div class="page-header-dashboard">
    <div>
      <h1 class="page-title-dashboard"><i class="bi bi-person me-2"></i><?= htmlspecialchars($mother['name']) ?></h1>
      <a href="/admin/mamans" class="btn-dashboard btn-dashboard-outline btn-dashboard-sm">&larr; Retour</a>
    </div>
  </div>

  <div class="row-dashboard">
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Profil</h5>
      </div>
      <div class="card-dashboard-body">
        <div class="row g-3">
          <div class="col-md-6"><strong>Email :</strong> <?= htmlspecialchars($mother['email']) ?></div>
          <div class="col-md-6"><strong>Téléphone :</strong> <?= htmlspecialchars($mother['phone'] ?? '-') ?></div>
          <div class="col-md-6"><strong>Date de naissance :</strong> <?= htmlspecialchars($mother['date_of_birth'] ?? '-') ?></div>
          <div class="col-md-6"><strong>Ville :</strong> <?= htmlspecialchars($mother['city'] ?? '-') ?></div>
        </div>
      </div>
    </div>
  </div>

  <?php if ($pregnancy): ?>
  <div class="row-dashboard">
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Grossesse</h5>
      </div>
      <div class="card-dashboard-body">
        <div class="row g-3">
          <div class="col-md-4"><strong>Date d'accouchement :</strong> <?= htmlspecialchars($pregnancy['due_date'] ?? '-') ?></div>
          <div class="col-md-4"><strong>Statut :</strong> <span class="badge-dashboard <?= $pregnancy['status'] === 'active' ? 'success' : 'info' ?>"><?= $pregnancy['status'] === 'active' ? 'Active' : 'Terminée' ?></span></div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if (!empty($babies)): foreach ($babies as $baby): ?>
  <div class="row-dashboard">
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Bébé : <?= htmlspecialchars($baby['name'] ?? 'Non renseigné') ?></h5>
      </div>
      <div class="card-dashboard-body">
        <div class="row g-3 mb-3">
          <div class="col-md-4"><strong>Date de naissance :</strong> <?= htmlspecialchars($baby['date_of_birth'] ?? '-') ?></div>
          <div class="col-md-4"><strong>Genre :</strong> <?= $baby['gender'] === 'girl' ? 'Fille' : ($baby['gender'] === 'boy' ? 'Garçon' : 'Autre') ?></div>
        </div>

        <?php if (!empty($baby['vaccinations'])): ?>
        <h6 class="mb-2">Vaccinations</h6>
        <div class="table-wrapper">
          <table class="table-dashboard">
            <thead><tr><th>Vaccin</th><th>Date prévue</th><th>Date d'administration</th><th>Statut</th></tr></thead>
            <tbody>
              <?php foreach ($baby['vaccinations'] as $v): ?>
              <tr>
                <td><?= htmlspecialchars($v['vaccine_name']) ?></td>
                <td><?= htmlspecialchars($v['due_date'] ?? '-') ?></td>
                <td><?= htmlspecialchars($v['administered_date'] ?? '-') ?></td>
                <td><span class="badge-dashboard <?= $v['status'] === 'done' ? 'success' : ($v['status'] === 'pending' ? 'warning' : 'danger') ?>"><?= $v['status'] === 'done' ? 'Fait' : ($v['status'] === 'pending' ? 'En attente' : 'Manqué') ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>

        <?php if (!empty($baby['growth_records'])): ?>
        <h6 class="mb-2 mt-3">Courbes de croissance</h6>
        <div class="table-wrapper">
          <table class="table-dashboard">
            <thead><tr><th>Date</th><th>Poids (kg)</th><th>Taille (cm)</th><th>Périmètre crânien (cm)</th></tr></thead>
            <tbody>
              <?php foreach ($baby['growth_records'] as $g): ?>
              <tr>
                <td><?= htmlspecialchars($g['record_date']) ?></td>
                <td><?= htmlspecialchars($g['weight'] ?? '-') ?></td>
                <td><?= htmlspecialchars($g['height'] ?? '-') ?></td>
                <td><?= htmlspecialchars($g['head_circumference'] ?? '-') ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; endif; ?>
</div>