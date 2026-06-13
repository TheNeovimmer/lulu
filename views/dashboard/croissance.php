<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="page-header-dashboard">
      <h1 class="page-title-dashboard"><i class="bi bi-graph-up me-2"></i>Suivi de Croissance</h1>
    </div>

    <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
      <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <?php if (empty($baby)): ?>
    <div class="empty-state-dashboard">
      <i class="bi bi-emoji-neutral"></i>
      <p>Vous devez d'abord ajouter les informations de votre bébé.</p>
      <a href="/dashboard/bebe" class="btn-dashboard btn-dashboard-primary mt-3">Ajouter mon bébé</a>
    </div>
    <?php else: ?>
    
    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title" style="color: var(--dprimary);">Nouvelle mesure</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/dashboard/croissance" class="form-dashboard">
          <div class="row g-3">
            <div class="col-md-3">
              <div class="form-floating">
                <input type="number" step="0.01" name="weight" class="form-control" id="floatingWeight" placeholder="Poids" required>
                <label for="floatingWeight">Poids (kg)</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-floating">
                <input type="number" step="0.1" name="height" class="form-control" id="floatingHeight" placeholder="Taille" required>
                <label for="floatingHeight">Taille (cm)</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-floating">
                <input type="number" step="0.1" name="head_circumference" class="form-control" id="floatingHead" placeholder="Périmètre crânien">
                <label for="floatingHead">Périmètre crânien (cm)</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-floating">
                <input type="date" name="measured_at" class="form-control" id="floatingDate" placeholder="Date" value="<?= date('Y-m-d') ?>" required>
                <label for="floatingDate">Date</label>
              </div>
            </div>
          </div>
          <button type="submit" class="btn-dashboard btn-dashboard-primary mt-3">Ajouter la mesure</button>
        </form>
      </div>
    </div>

    <?php if (!empty($records)): ?>
    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title" style="color: var(--dprimary);">Courbes de croissance</h5>
      </div>
      <div class="card-dashboard-body">
        <div style="position: relative; height: 320px; width: 100%;">
          <canvas id="growthChart"></canvas>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title" style="color: var(--dprimary);">Historique des mesures</h5>
      </div>
      <div class="card-dashboard-body">
        <?php if (!empty($records)): ?>
        <div class="table-wrapper">
          <table class="table-dashboard">
            <thead>
              <tr>
                <th>Date</th>
                <th>Poids (kg)</th>
                <th>Taille (cm)</th>
                <th>Périmètre crânien (cm)</th>
                <th>Âge du bébé</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($records as $r): ?>
              <tr>
                <td><?= date('d/m/Y', strtotime($r['record_date'])) ?></td>
                <td style="font-weight: 600;"><?= number_format($r['weight'], 2) ?> kg</td>
                <td style="font-weight: 600;"><?= number_format($r['height'], 1) ?> cm</td>
                <td><?= $r['head_circumference'] ? number_format($r['head_circumference'], 1) . ' cm' : '-' ?></td>
                <td><?= $r['age_days'] ?> jours</td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div class="empty-state-dashboard">
          <i class="bi bi-graph-down"></i>
          <p class="mb-0">Aucune mesure enregistrée pour le moment.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php if (!empty($records)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('growthChart').getContext('2d');
    
    const labels = [
      <?php foreach ($records as $r) {
          echo "'" . date('d/m/Y', strtotime($r['record_date'])) . "',";
      } ?>
    ];
    const weightData = [
      <?php foreach ($records as $r) {
          echo $r['weight'] . ",";
      } ?>
    ];
    const heightData = [
      <?php foreach ($records as $r) {
          echo $r['height'] . ",";
      } ?>
    ];

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Poids (kg)',
            data: weightData,
            borderColor: '#C94B72',
            backgroundColor: 'rgba(201, 75, 114, 0.1)',
            borderWidth: 3,
            tension: 0.3,
            yAxisID: 'yWeight',
            pointBackgroundColor: '#C94B72',
            fill: true
          },
          {
            label: 'Taille (cm)',
            data: heightData,
            borderColor: '#F0A0BB',
            backgroundColor: 'transparent',
            borderWidth: 3,
            tension: 0.3,
            yAxisID: 'yHeight',
            pointBackgroundColor: '#F0A0BB'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            grid: { color: 'rgba(0, 0, 0, 0.05)' },
            ticks: { color: 'rgba(0, 0, 0, 0.5)' }
          },
          yWeight: {
            type: 'linear',
            position: 'left',
            grid: { color: 'rgba(0, 0, 0, 0.05)' },
            ticks: { color: '#C94B72' },
            title: { display: true, text: 'Poids (kg)', color: '#C94B72' }
          },
          yHeight: {
            type: 'linear',
            position: 'right',
            grid: { drawOnChartArea: false },
            ticks: { color: '#F0A0BB' },
            title: { display: true, text: 'Taille (cm)', color: '#F0A0BB' }
          }
        },
        plugins: {
          legend: {
            labels: { color: '#333' }
          }
        }
      }
    });
  });
</script>
<?php endif; ?>
