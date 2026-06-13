<div class="row">
  <div class="col-12">
    <div class="card-dashboard form-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Période du rapport</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="GET" action="/ctt/rapports" class="row g-3 align-items-end">
          <div class="col-md-4">
            <div class="form-floating">
              <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($_GET['date_from'] ?? date('Y-m-01')) ?>">
              <label>Date de début</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($_GET['date_to'] ?? date('Y-m-d')) ?>">
              <label>Date de fin</label>
            </div>
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-dashboard btn-dashboard-primary w-100"><i class="bi bi-funnel me-1"></i>Filtrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="stats-row-dashboard">
      <div class="stat-card-dashboard">
        <div class="stat-card-icon"><i class="bi bi-ticket"></i></div>
        <div class="stat-card-info">
          <span class="stat-card-number"><?= $stats['total_tickets'] ?? 0 ?></span>
          <span class="stat-card-label">Total tickets</span>
        </div>
      </div>
      <div class="stat-card-dashboard">
        <div class="stat-card-icon"><i class="bi bi-check-circle"></i></div>
        <div class="stat-card-info">
          <span class="stat-card-number"><?= $stats['resolved'] ?? 0 ?></span>
          <span class="stat-card-label">Résolus</span>
        </div>
      </div>
      <div class="stat-card-dashboard">
        <div class="stat-card-icon"><i class="bi bi-clock"></i></div>
        <div class="stat-card-info">
          <span class="stat-card-number"><?= $stats['avg_response_time'] ?? 'N/A' ?></span>
          <span class="stat-card-label">Temps moyen de réponse</span>
        </div>
      </div>
      <div class="stat-card-dashboard">
        <div class="stat-card-icon"><i class="bi bi-people"></i></div>
        <div class="stat-card-info">
          <span class="stat-card-number"><?= $stats['total_agents'] ?? 0 ?></span>
          <span class="stat-card-label">Agents</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card-dashboard d-flex justify-content-between align-items-center">
      <div>
        <h5 class="card-dashboard-title mb-1">Exporter les rapports</h5>
        <p class="text-muted small mb-0">Téléchargez un rapport détaillé au format CSV.</p>
      </div>
      <button class="btn btn-dashboard btn-dashboard-primary" onclick="alert('Fonctionnalité à venir.')"><i class="bi bi-download me-1"></i>Exporter</button>
    </div>
  </div>
</div>
