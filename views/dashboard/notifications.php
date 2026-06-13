<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="page-header-dashboard">
      <h1 class="page-title-dashboard"><i class="bi bi-bell me-2"></i>Notifications</h1>
      <?php if (!empty($notifications)): ?>
        <form method="POST" action="/dashboard/notifications/read-all">
          <button class="btn-dashboard btn-dashboard-outline btn-dashboard-sm"><i class="bi bi-check-all me-1"></i>Marquer tout comme lu</button>
        </form>
      <?php endif; ?>
    </div>

    <?php if (!empty($notifications)): ?>
      <?php foreach ($notifications as $n): ?>
      <div class="d-flex align-items-start gap-3 p-3 mb-3" style="border: 1px solid var(--dborder); border-radius: var(--dradius); background: var(--dbg-card); <?= !$n['is_read'] ? 'border-color: var(--dprimary) !important;' : '' ?>">
        <div>
          <?php $icons = ['info' => 'bi-info-circle', 'success' => 'bi-check-circle', 'warning' => 'bi-exclamation-triangle', 'danger' => 'bi-x-circle']; ?>
          <i class="bi <?= $icons[$n['type']] ?? 'bi-bell' ?>" style="color: var(--dprimary); font-size: 1.25rem;"></i>
        </div>
        <div style="flex: 1;">
          <p class="mb-1" style="color: var(--dtext-dark);"><?= htmlspecialchars($n['message']) ?></p>
          <small style="color: var(--dtext-muted);"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></small>
        </div>
        <?php if (!$n['is_read']): ?>
        <form method="POST" action="/dashboard/notifications/read/<?= $n['id'] ?>">
          <button class="btn-icon" title="Marquer comme lu" data-action="mark-read" data-url="/dashboard/notifications/read/<?= $n['id'] ?>"><i class="bi bi-check"></i></button>
        </form>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty-state-dashboard">
        <i class="bi bi-bell-slash"></i>
        <p>Aucune notification.</p>
      </div>
    <?php endif; ?>
  </div>
</div>
