<div class="row">
  <div class="col-12">
    <h1 class="font-heading mb-4"><i class="bi bi-bar-chart me-2 text-pink"></i>Rapports</h1>
  </div>

  <div class="col-12 mb-4" data-animate="fade-up">
    <div class="card-luma p-4">
      <form method="GET" action="/ctt/rapports" class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label text-white-50">Date de début</label>
          <input type="date" name="date_from" class="form-control form-control-luma" value="<?= htmlspecialchars($_GET['date_from'] ?? date('Y-m-01')) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label text-white-50">Date de fin</label>
          <input type="date" name="date_to" class="form-control form-control-luma" value="<?= htmlspecialchars($_GET['date_to'] ?? date('Y-m-d')) ?>">
        </div>
        <div class="col-md-4">
          <button type="submit" class="btn btn-luma w-100"><i class="bi bi-funnel me-1"></i>Filtrer</button>
        </div>
      </form>
    </div>
  </div>

  <div class="col-md-3" data-animate="fade-up">
    <div class="stat-card">
      <i class="stat-icon bi bi-ticket"></i>
      <div class="stat-number"><?= $stats['total_tickets'] ?? 0 ?></div>
      <div class="stat-label">Total tickets</div>
      <div class="stat-accent"></div>
    </div>
  </div>
  <div class="col-md-3" data-animate="fade-up">
    <div class="stat-card">
      <i class="stat-icon bi bi-check-circle"></i>
      <div class="stat-number"><?= $stats['resolved'] ?? 0 ?></div>
      <div class="stat-label">Résolus</div>
      <div class="stat-accent"></div>
    </div>
  </div>
  <div class="col-md-3" data-animate="fade-up">
    <div class="stat-card">
      <i class="stat-icon bi bi-clock"></i>
      <div class="stat-number"><?= $stats['avg_response_time'] ?? 'N/A' ?></div>
      <div class="stat-label">Temps moyen de réponse</div>
      <div class="stat-accent"></div>
    </div>
  </div>
  <div class="col-md-3" data-animate="fade-up">
    <div class="stat-card">
      <i class="stat-icon bi bi-people"></i>
      <div class="stat-number"><?= $stats['total_agents'] ?? 0 ?></div>
      <div class="stat-label">Agents</div>
      <div class="stat-accent"></div>
    </div>
  </div>

  <div class="col-12" data-animate="fade-up">
    <div class="card-luma p-4 d-flex justify-content-between align-items-center">
      <div>
        <h5 class="section-title">Exporter les rapports</h5>
        <p class="text-white-50 small mb-0">Téléchargez un rapport détaillé au format CSV.</p>
      </div>
      <button class="btn btn-luma" onclick="alert('Fonctionnalité à venir.')"><i class="bi bi-download me-1"></i>Exporter</button>
    </div>
  </div>
</div>
