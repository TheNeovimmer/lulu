<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="page-header-dashboard">
      <h5 class="page-title-dashboard"><i class="bi bi-bell me-2"></i>Notifications</h5>
      <?php if (!empty($notifications)): ?>
        <div class="page-actions-dashboard">
          <form method="POST" action="/ctt/notifications/read-all">
            <button class="btn btn-dashboard btn-dashboard-outline btn-dashboard-sm"><i class="bi bi-check-all me-1"></i>Marquer tout comme lu</button>
          </form>
        </div>
      <?php endif; ?>
    </div>

    <?php if (!empty($notifications)): ?>
      <?php foreach ($notifications as $n): ?>
      <div class="card-dashboard mb-3 d-flex align-items-start gap-3" style="<?= !$n['is_read'] ? 'border-left: 3px solid var(--dprimary);' : '' ?>">
        <div style="margin-top:4px;">
          <?php $icons = ['info' => 'bi-info-circle', 'success' => 'bi-check-circle', 'warning' => 'bi-exclamation-triangle', 'danger' => 'bi-x-circle']; ?>
          <i class="bi <?= $icons[$n['type']] ?? 'bi-bell' ?>" style="color:var(--dprimary); font-size:1.5rem;"></i>
        </div>
        <div class="flex-grow-1">
          <p class="mb-1"><?= htmlspecialchars($n['message']) ?></p>
          <small class="text-muted"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></small>
        </div>
        <?php if (!$n['is_read']): ?>
        <form method="POST" action="/ctt/notifications/read/<?= $n['id'] ?>">
          <button class="btn-icon" title="Marquer comme lu"><i class="bi bi-check"></i></button>
        </form>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty-state-dashboard">
        <i class="bi bi-bell-slash"></i>
        <h5>Aucune notification</h5>
        <p>Vous n'avez aucune notification pour le moment.</p>
      </div>
    <?php endif; ?>
  </div>
</div>
