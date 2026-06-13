<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="page-header-dashboard">
      <div>
        <h1 class="page-title-dashboard"><i class="bi bi-calendar-week me-2"></i>Mon Agenda</h1>
        <p style="color: var(--dtext-muted); margin-bottom: 0;">Consultations et rendez-vous avec les mamans.</p>
      </div>
    </div>

    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">À venir</h5>
      </div>
      <div class="card-dashboard-body">
        <?php if (!empty($upcoming)): ?>
          <div class="d-flex flex-column gap-2">
            <?php foreach ($upcoming as $e): ?>
              <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="border: 1px solid var(--dborder); background: var(--dbg-card);">
                <div class="d-flex flex-column align-items-center p-2 rounded-3 text-center" style="min-width: 60px; background: var(--dprimary-subtle); border: 1px solid rgba(201, 75, 114, 0.2);">
                  <span style="font-weight: 700; color: var(--dprimary); font-size: 1.1rem;"><?= (new \DateTime($e['date']))->format('d') ?></span>
                  <span style="color: var(--dtext-muted); text-transform: uppercase; font-size: 0.7rem;"><?= (new \DateTime($e['date']))->format('M') ?></span>
                </div>
                <div style="flex: 1;">
                  <h6 style="font-weight: 600; color: var(--dtext-dark); margin-bottom: 2px;"><?= htmlspecialchars($e['title']) ?></h6>
                  <p style="color: var(--dtext-muted); font-size: 0.85rem; margin-bottom: 0;">
                    <i class="bi bi-clock me-1"></i><?= $e['time'] ?>
                    <span class="badge-dashboard <?= $e['status'] === 'confirmed' ? 'success' : ($e['status'] === 'cancelled' ? 'danger' : 'warning') ?> ms-2"><?= $e['status'] === 'confirmed' ? 'Confirmé' : ($e['status'] === 'cancelled' ? 'Annulé' : 'En attente') ?></span>
                    <span class="badge-dashboard info ms-1"><?= $e['type'] === 'online' ? 'En ligne' : 'Cabinet' ?></span>
                  </p>
                  <?php if ($e['notes']): ?>
                  <small style="color: var(--dtext-muted);"><?= htmlspecialchars($e['notes']) ?></small>
                  <?php endif; ?>
                </div>
                <?php if ($e['status'] === 'pending'): ?>
                <div class="d-flex gap-1">
                  <form method="POST" action="/expert/appointments/update/<?= $e['id'] ?>">
                    <?= \App\Core\Session::csrf_field() ?>
                    <input type="hidden" name="action" value="confirmed">
                    <button type="submit" class="btn btn-sm btn-dashboard btn-dashboard-primary" title="Confirmer"><i class="bi bi-check-lg"></i></button>
                  </form>
                  <form method="POST" action="/expert/appointments/update/<?= $e['id'] ?>" onsubmit="return confirm('Annuler ce rendez-vous ?')">
                    <?= \App\Core\Session::csrf_field() ?>
                    <input type="hidden" name="action" value="cancelled">
                    <button type="submit" class="btn btn-sm btn-dashboard btn-dashboard-danger" title="Annuler"><i class="bi bi-x-lg"></i></button>
                  </form>
                </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="empty-state-dashboard">
            <i class="bi bi-calendar-check"></i>
            <p>Aucun rendez-vous à venir.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($past)): ?>
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Passés</h5>
      </div>
      <div class="card-dashboard-body">
        <div class="d-flex flex-column gap-2">
          <?php foreach ($past as $e): ?>
            <div class="d-flex align-items-center gap-3 p-3 rounded-3 opacity-50" style="border: 1px solid var(--dborder); background: var(--dbg-card);">
              <div class="d-flex flex-column align-items-center p-2 rounded-3 text-center" style="min-width: 60px; background: var(--dprimary-subtle); border: 1px solid rgba(201, 75, 114, 0.2);">
                <span style="font-weight: 700; color: var(--dprimary); font-size: 1.1rem;"><?= (new \DateTime($e['date']))->format('d') ?></span>
                <span style="color: var(--dtext-muted); text-transform: uppercase; font-size: 0.7rem;"><?= (new \DateTime($e['date']))->format('M') ?></span>
              </div>
              <div style="flex: 1;">
                <h6 style="font-weight: 600; color: var(--dtext-dark); margin-bottom: 2px;"><?= htmlspecialchars($e['title']) ?></h6>
                <p style="color: var(--dtext-muted); font-size: 0.85rem; margin-bottom: 0;">
                  <i class="bi bi-clock me-1"></i><?= $e['time'] ?>
                  <span style="color: var(--dtext-muted); margin-left: 8px;">(Passé)</span>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>