<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="page-header-dashboard">
      <div>
        <h1 class="page-title-dashboard"><i class="bi bi-calendar-week me-2"></i>Mon Agenda</h1>
        <p style="color: var(--dtext-muted); margin-bottom: 0;">Votre calendrier de grossesse et de suivi pédiatrique réuni en un seul endroit.</p>
      </div>
    </div>

    <div class="d-flex gap-2 mb-4">
      <a href="?filter=all" class="btn-dashboard btn-dashboard-outline btn-dashboard-sm <?= $filter === 'all' ? 'active' : '' ?>">Tous</a>
      <a href="?filter=upcoming" class="btn-dashboard btn-dashboard-outline btn-dashboard-sm <?= $filter === 'upcoming' ? 'active' : '' ?>">À venir</a>
      <a href="?filter=past" class="btn-dashboard btn-dashboard-outline btn-dashboard-sm <?= $filter === 'past' ? 'active' : '' ?>">Passés</a>
    </div>

    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Calendrier personnalisé</h5>
      </div>
      <div class="card-dashboard-body">
        <?php if (!empty($events)): ?>
          <div class="d-flex flex-column gap-2">
            <?php foreach ($events as $event): ?>
              <?php
              $evtDate = new \DateTime($event['date']);
              $isPast = $evtDate < new \DateTime('today');
              ?>
              <div class="d-flex align-items-start gap-3 p-3 rounded-3 mb-2 <?= $isPast ? 'opacity-50' : '' ?>" style="border: 1px solid var(--dborder); background: var(--dbg-card); transition: transform 0.2s;">
                <div class="d-flex flex-column align-items-center p-2 rounded-3 text-center" style="min-width: 65px; background: var(--dprimary-subtle); border: 1px solid rgba(201, 75, 114, 0.2);">
                  <span style="font-weight: 700; color: var(--dprimary); font-size: 1.15rem;"><?= $evtDate->format('d') ?></span>
                  <span style="color: var(--dtext-muted); text-transform: uppercase; font-size: 0.72rem;"><?= $evtDate->format('M') ?></span>
                </div>
                <div style="flex: 1;">
                  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                    <h6 style="font-weight: 600; color: var(--dtext-dark); margin-bottom: 0;">
                      <i class="bi <?= $event['icon'] ?> me-1" style="color: var(--dprimary);"></i>
                      <?= htmlspecialchars($event['title']) ?>
                    </h6>
                    <span class="badge-dashboard <?= $event['badge_class'] ?>"><?= $event['badge'] ?></span>
                  </div>
                  <p style="color: var(--dtext-muted); font-size: 0.85rem; margin-bottom: 0;">
                    <i class="bi bi-clock me-1"></i><?= $event['time'] ? $event['time'] : 'Toute la journée' ?>
                    <?php if ($isPast): ?>
                      <span style="color: var(--dtext-muted); margin-left: 8px;">(Passé)</span>
                    <?php endif; ?>
                  </p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="empty-state-dashboard">
            <i class="bi bi-calendar-check"></i>
            <p>Aucun événement trouvé. Enregistrez vos rendez-vous, souvenirs et suivis pour remplir votre agenda.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>