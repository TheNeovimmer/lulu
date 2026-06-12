<div class="row">
  <div class="col-12">
    <h1 class="font-heading mb-4"><i class="bi bi-clock-history me-2 text-pink"></i>Historique des Tickets</h1>

    <div class="card-luma p-4 mb-4" data-animate="fade-up">
      <form method="GET" action="/ctt/historique" class="row g-3 align-items-end">
        <div class="col-md-6">
          <label class="form-label text-white-50">Rechercher</label>
          <input type="text" name="search" class="form-control form-control-luma" placeholder="Sujet ou utilisateur..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label text-white-50">Statut</label>
          <select name="status" class="form-select form-control-luma">
            <option value="">Tous</option>
            <option value="résolu" <?= ($_GET['status'] ?? '') === 'résolu' ? 'selected' : '' ?>>Résolus</option>
            <option value="fermé" <?= ($_GET['status'] ?? '') === 'fermé' ? 'selected' : '' ?>>Fermés</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-luma w-100"><i class="bi bi-search me-1"></i>Filtrer</button>
        </div>
      </form>
    </div>

    <div class="card-luma p-4" data-animate="fade-up">
      <div class="table-responsive">
        <table class="table table-luma">
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
                    <span class="badge bg-danger">Urgent</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Normal</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php $statusClasses = ['ouvert' => 'bg-success', 'en_cours' => 'bg-warning text-dark', 'résolu' => 'bg-info', 'fermé' => 'bg-secondary']; ?>
                  <span class="badge <?= $statusClasses[$t['status']] ?? 'bg-secondary' ?>"><?= ucfirst($t['status']) ?></span>
                </td>
                <td><?= htmlspecialchars($t['assigned_name'] ?? '-') ?></td>
                <td><?= date('d/m/Y', strtotime($t['created_at'])) ?></td>
                <td><?= $t['resolved_at'] ? date('d/m/Y', strtotime($t['resolved_at'])) : '-' ?></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="8" class="text-center text-white-50">Aucun ticket trouvé.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
