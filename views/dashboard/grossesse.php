<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="page-header-dashboard">
      <h1 class="page-title-dashboard"><i class="bi bi-flower1 me-2"></i>Ma Grossesse</h1>
    </div>

    <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
      <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <?php if (!empty($pregnancy)): ?>
    <div class="stats-row-dashboard mb-4">
      <div class="stat-card-dashboard">
        <div class="stat-card-icon"><i class="bi bi-calendar-heart"></i></div>
        <div class="stat-card-info">
          <span class="stat-card-number"><?= date('d/m/Y', strtotime($pregnancy['due_date'])) ?></span>
          <span class="stat-card-label">Date prévue d'accouchement</span>
        </div>
      </div>
      <div class="stat-card-dashboard">
        <div class="stat-card-icon"><i class="bi bi-clock"></i></div>
        <div class="stat-card-info">
          <span class="stat-card-number" style="color: var(--dprimary);"><?= $pregnancy['weeks'] ?> semaines</span>
          <span class="stat-card-label">Semaine de grossesse (Calculé)</span>
        </div>
      </div>
    </div>

    <?php
    $selectedWeek = isset($_GET['week']) ? max(1, min(40, (int)$_GET['week'])) : ($pregnancy['weeks'] ?: 1);
    
    $pregnancyData = [
        1 => ['fruit' => 'Graine de pavot', 'size' => '0.1 mm', 'weight' => 'Moins de 1g', 'desc' => 'Le début du voyage ! Fécondation et nidation. La cellule commence à se diviser.'],
        4 => ['fruit' => 'Graine de sésame', 'size' => '2 mm', 'weight' => 'Moins de 1g', 'desc' => 'L\'embryon s\'implante dans l\'utérus. Le tube neural commence à se former.'],
        8 => ['fruit' => 'Framboise', 'size' => '1.6 cm', 'weight' => '1 g', 'desc' => 'Les doigts, les orteils et les paupières se forment. Le cœur bat à un rythme rapide.'],
        12 => ['fruit' => 'Prune', 'size' => '5.4 cm', 'weight' => '14 g', 'desc' => 'Fin du premier trimestre. Les organes vitaux sont en place. Le bébé commence à bouger.'],
        16 => ['fruit' => 'Avocat', 'size' => '11.6 cm', 'weight' => '100 g', 'desc' => 'Le bébé peut entendre les bruits extérieurs. Ses réflexes se développent.'],
        20 => ['fruit' => 'Banane', 'size' => '25.6 cm', 'weight' => '300 g', 'desc' => 'Mi-parcours ! Les mouvements sont bien perçus par la maman. Les cheveux et ongles poussent.'],
        24 => ['fruit' => 'Épi de maïs', 'size' => '30 cm', 'weight' => '600 g', 'desc' => 'Les poumons continuent de se développer. Le bébé commence à ouvrir les yeux.'],
        28 => ['fruit' => 'Aubergine', 'size' => '37.6 cm', 'weight' => '1 kg', 'desc' => 'Début du troisième trimestre. Le cerveau se développe rapidement. La rétine est active.'],
        32 => ['fruit' => 'Courge', 'size' => '42.4 cm', 'weight' => '1.7 kg', 'desc' => 'Le bébé prend du poids rapidement et se positionne généralement la tête en bas.'],
        36 => ['fruit' => 'Melon', 'size' => '47.4 cm', 'weight' => '2.6 kg', 'desc' => 'Les organes sont matures. Le bébé descend dans le bassin en vue de l\'accouchement.'],
        40 => ['fruit' => 'Pastèque', 'size' => '51.2 cm', 'weight' => '3.4 kg', 'desc' => 'Le terme est arrivé ! Le bébé est pleinement prêt pour le grand jour de sa naissance.']
    ];

    $dataKeys = array_keys($pregnancyData);
    $closestKey = 1;
    foreach ($dataKeys as $k) {
        if ($selectedWeek >= $k) {
            $closestKey = $k;
        }
    }
    $currentWeekData = $pregnancyData[$closestKey];
    ?>

    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title"><i class="bi bi-compass me-2"></i>Évolution du bébé (Semaine par Semaine)</h5>
        <div class="d-flex align-items-center gap-2">
          <label style="font-size: 0.85rem; color: var(--dtext-muted);">Semaine :</label>
          <select class="form-select" style="width: 80px; padding: 4px 8px; font-size: 0.85rem; border: 1px solid var(--dborder); border-radius: var(--dradius-sm);" onchange="window.location.href='/dashboard/grossesse?week=' + this.value;">
            <?php for ($i = 1; $i <= 40; $i++): ?>
              <option value="<?= $i ?>" <?= $i == $selectedWeek ? 'selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
          </select>
        </div>
      </div>
      <div class="card-dashboard-body">
        <div class="row g-4 align-items-center mt-2">
          <div class="col-md-4 text-center">
            <div class="p-3 rounded-4" style="background: var(--dprimary-subtle); border: 1px solid var(--dborder);">
              <h6 style="color: var(--dtext-muted); font-size: 0.75rem; text-transform: uppercase;">Taille équivalente à</h6>
              <div style="font-size: 1.5rem; font-weight: 700; color: var(--dprimary); margin-top: 4px; margin-bottom: 8px;"><?= $currentWeekData['fruit'] ?></div>
              <div class="d-flex justify-content-around text-start pt-3 mt-3" style="border-top: 1px solid var(--dborder-light);">
                <div>
                  <small style="color: var(--dtext-muted); display: block;">Taille</small>
                  <strong style="color: var(--dtext-dark);"><?= $currentWeekData['size'] ?></strong>
                </div>
                <div style="border-left: 1px solid var(--dborder-light); padding-left: 12px;">
                  <small style="color: var(--dtext-muted); display: block;">Poids</small>
                  <strong style="color: var(--dtext-dark);"><?= $currentWeekData['weight'] ?></strong>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <h6 style="font-weight: 600; color: var(--dtext-dark);">Que se passe-t-il à la semaine <?= $selectedWeek ?> ?</h6>
            <p style="color: var(--dtext-muted); margin-top: 8px; font-size: 1.05rem; line-height: 1.6;">
              <?= $currentWeekData['desc'] ?>
            </p>
            <div class="mt-3 p-3 rounded-3" style="background: var(--dprimary-subtle); border-left: 4px solid var(--dprimary);">
              <small style="color: var(--dprimary); font-weight: 600; display: block;"><i class="bi bi-info-circle me-1"></i>Conseil de la semaine</small>
              <span style="color: var(--dtext-muted); font-size: 0.875rem;">
                <?php if ($selectedWeek <= 12): ?>
                  Pensez à prendre vos suppléments d'acide folique et planifiez votre première échographie obligatoire.
                <?php elseif ($selectedWeek <= 24): ?>
                  Hydratez bien votre peau pour prévenir les vergetures et commencez à réfléchir aux cours de préparation à la naissance.
                <?php else: ?>
                  Préparez votre valise de maternité et privilégiez le repos. Suivez régulièrement votre tension artérielle.
                <?php endif; ?>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title" style="color: var(--dprimary);">Mettre à jour les informations</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/dashboard/grossesse" class="form-dashboard">
          <div class="form-floating mb-3">
            <input type="date" name="due_date" class="form-control" id="floatingDueDate" placeholder="Date prévue" value="<?= htmlspecialchars($pregnancy['due_date']) ?>" required>
            <label for="floatingDueDate">Date prévue d'accouchement</label>
          </div>
          <div class="form-floating mb-3">
            <textarea name="notes" class="form-control" id="floatingNotes" placeholder="Notes" rows="3" style="min-height: 100px;"><?= htmlspecialchars($pregnancy['notes'] ?? '') ?></textarea>
            <label for="floatingNotes">Notes personnelles</label>
          </div>
          <button type="submit" class="btn-dashboard btn-dashboard-primary">Mettre à jour</button>
        </form>
      </div>
    </div>
    <?php else: ?>
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title" style="color: var(--dprimary);">Initialiser mon suivi de grossesse</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/dashboard/grossesse" class="form-dashboard">
          <div class="form-floating mb-3">
            <input type="date" name="due_date" class="form-control" id="floatingDueDateInit" placeholder="Date prévue" required>
            <label for="floatingDueDateInit">Date prévue d'accouchement</label>
          </div>
          <div class="form-floating mb-3">
            <textarea name="notes" class="form-control" id="floatingNotesInit" placeholder="Notes" rows="3" style="min-height: 100px;"></textarea>
            <label for="floatingNotesInit">Notes / Objectifs</label>
          </div>
          <button type="submit" class="btn-dashboard btn-dashboard-primary">Lancer le suivi de grossesse</button>
        </form>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
