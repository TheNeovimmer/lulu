<div class="row justify-content-center">
  <div class="col-lg-10">

    <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
      <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title"><i class="bi bi-clock me-2"></i>Mes Disponibilités Hebdomadaires</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/expert/disponibilites" id="availabilityForm">
          <?= \App\Core\Session::csrf_field() ?>
          <div id="slotsContainer">
            <?php if (!empty($grouped)): ?>
              <?php foreach ($grouped as $dayNum => $group): ?>
                <?php foreach ($group['slots'] as $slot): ?>
                <div class="row g-2 align-items-center mb-2 slot-row">
                  <div class="col-md-3">
                    <select name="days[]" class="form-select form-dashboard">
                      <?php for ($d = 0; $d < 7; $d++): ?>
                      <option value="<?= $d ?>" <?= $dayNum == $d ? 'selected' : '' ?>><?= $dayNames[$d] ?? '' ?></option>
                      <?php endfor; ?>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <input type="time" name="start_times[]" class="form-control form-dashboard" value="<?= htmlspecialchars(substr($slot['start_time'], 0, 5)) ?>" required>
                  </div>
                  <div class="col-md-3">
                    <input type="time" name="end_times[]" class="form-control form-dashboard" value="<?= htmlspecialchars(substr($slot['end_time'], 0, 5)) ?>" required>
                  </div>
                  <div class="col-md-3">
                    <button type="button" class="btn btn-dashboard btn-dashboard-danger" onclick="this.closest('.slot-row').remove()">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
                <?php endforeach; ?>
              <?php endforeach; ?>
            <?php else: ?>
            <p class="text-muted mb-3" id="noSlotsMsg">Aucune disponibilité définie. Ajoutez vos créneaux ci-dessous.</p>
            <?php endif; ?>
          </div>

          <button type="button" class="btn btn-dashboard btn-dashboard-outline btn-dashboard-sm mb-3" onclick="addSlot()">
            <i class="bi bi-plus-circle"></i> Ajouter un créneau
          </button>

          <div class="mt-3">
            <button type="submit" class="btn btn-dashboard btn-dashboard-primary">
              <i class="bi bi-check-lg"></i> Enregistrer
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title"><i class="bi bi-calendar-x me-2"></i>Dates d'Indisponibilité</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/expert/disponibilites/unavailable" class="row g-2 align-items-end mb-3">
          <?= \App\Core\Session::csrf_field() ?>
          <div class="col-md-4">
            <label class="form-label-dashboard small">Date</label>
            <input type="date" name="unavailable_date" class="form-control form-dashboard" required>
          </div>
          <div class="col-md-5">
            <label class="form-label-dashboard small">Motif (optionnel)</label>
            <input type="text" name="reason" class="form-control form-dashboard" placeholder="Congé, formation...">
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-dashboard btn-dashboard-warning w-100">
              <i class="bi bi-plus-lg"></i> Ajouter
            </button>
          </div>
        </form>

        <?php if (!empty($unavailableDates)): ?>
        <table class="table-dashboard">
          <thead>
            <tr>
              <th>Date</th>
              <th>Motif</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($unavailableDates as $ud): ?>
            <tr>
              <td><?= htmlspecialchars(date('d/m/Y', strtotime($ud['unavailable_date']))) ?></td>
              <td><?= htmlspecialchars($ud['reason'] ?? '-') ?></td>
              <td class="text-end">
                <form method="POST" action="/expert/disponibilites/unavailable/remove/<?= htmlspecialchars($ud['unavailable_date']) ?>" style="display:inline">
                  <?= \App\Core\Session::csrf_field() ?>
                  <button type="submit" class="btn btn-dashboard btn-dashboard-danger btn-dashboard-sm" onclick="return confirm('Retirer cette date ?')">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
        <p class="text-muted mb-0">Aucune date d'indisponibilité. Vous êtes disponible tous les jours selon vos créneaux.</p>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<script>
function addSlot() {
  const container = document.getElementById('slotsContainer');
  const msg = document.getElementById('noSlotsMsg');
  if (msg) msg.remove();
  const row = document.createElement('div');
  row.className = 'row g-2 align-items-center mb-2 slot-row';
  row.innerHTML = `
    <div class="col-md-3">
      <select name="days[]" class="form-select form-dashboard">
        <?php for ($d = 0; $d < 7; $d++): ?>
        <option value="<?= $d ?>"><?= $dayNames[$d] ?? '' ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-3">
      <input type="time" name="start_times[]" class="form-control form-dashboard" required>
    </div>
    <div class="col-md-3">
      <input type="time" name="end_times[]" class="form-control form-dashboard" required>
    </div>
    <div class="col-md-3">
      <button type="button" class="btn btn-dashboard btn-dashboard-danger" onclick="this.closest('.slot-row').remove()">
        <i class="bi bi-trash"></i>
      </button>
    </div>
  `;
  container.appendChild(row);
}
</script>
