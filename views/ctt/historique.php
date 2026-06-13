<div class="row">
  <div class="col-12">
    <div class="card-dashboard form-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Filtrer l'historique</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="GET" action="/ctt/historique" class="row g-3 align-items-end">
          <div class="col-md-5">
            <div class="form-floating">
              <input type="text" name="search" class="form-control" placeholder="Sujet ou utilisateur..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
              <label>Rechercher</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <select name="status" class="form-select">
                <option value="">Tous</option>
                <option value="résolu" <?= ($_GET['status'] ?? '') === 'résolu' ? 'selected' : '' ?>>Résolus</option>
                <option value="fermé" <?= ($_GET['status'] ?? '') === 'fermé' ? 'selected' : '' ?>>Fermés</option>
              </select>
              <label>Statut</label>
            </div>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-dashboard btn-dashboard-primary w-100"><i class="bi bi-search me-1"></i>Filtrer</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card-dashboard">
      <div class="table-wrapper">
        <table class="table-dashboard">
          <thead>
            <tr>
              <th>ID</th>
              <th>Sujet</th>
              <th>Utilisateur</th>
              <th>Priorité</th>
              <th>Statut</th>
              <th>Assigné à</th>
              <th>Créé le</th>
              <th>Résolu le</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($tickets)): ?>
              <?php foreach ($tickets as $t): ?>
              <tr>
                <td><?= $t['id'] ?></td>
                <td><?= htmlspecialchars($t['subject']) ?></td>
                <td><?= htmlspecialchars($t['user_name'] ?? '-') ?></td>
                <td>
                  <?php if ($t['priority'] === 'urgent'): ?>
                    <span class="badge-dashboard danger">Urgent</span>
                  <?php else: ?>
                    <span class="badge-dashboard info">Normal</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php $statusClasses = ['ouvert' => 'success', 'en_cours' => 'warning', 'résolu' => 'info', 'fermé' => 'danger']; ?>
                  <span class="badge-dashboard <?= $statusClasses[$t['status']] ?? 'info' ?>"><?= ucfirst($t['status']) ?></span>
                </td>
                <td><?= htmlspecialchars($t['assigned_name'] ?? '-') ?></td>
                <td><?= date('d/m/Y', strtotime($t['created_at'])) ?></td>
                <td><?= $t['resolved_at'] ? date('d/m/Y', strtotime($t['resolved_at'])) : '-' ?></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="8" class="text-center text-muted py-4">Aucun ticket trouvé.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
