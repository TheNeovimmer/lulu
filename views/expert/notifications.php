<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="font-heading mb-0"><i class="bi bi-bell me-2 text-pink"></i>Notifications</h1>
      <?php if (!empty($notifications)): ?>
        <form method="POST" action="/expert/notifications/read-all">
          <button class="btn btn-outline-luma btn-sm"><i class="bi bi-check-all me-1"></i>Marquer tout comme lu</button>
        </form>
      <?php endif; ?>
    </div>

    <?php if (!empty($notifications)): ?>
      <div class="animate-stagger">
      <?php foreach ($notifications as $n): ?>
      <div class="card-luma p-3 mb-3 d-flex align-items-start gap-3 <?= !$n['is_read'] ? 'border-pink' : '' ?>" data-animate="fade-up">
        <div>
          <?php $icons = ['info' => 'bi-info-circle', 'success' => 'bi-check-circle', 'warning' => 'bi-exclamation-triangle', 'danger' => 'bi-x-circle']; ?>
          <i class="bi <?= $icons[$n['type']] ?? 'bi-bell' ?> text-pink fs-4"></i>
        </div>
        <div class="flex-grow-1">
          <p class="mb-1"><?= htmlspecialchars($n['message']) ?></p>
          <small class="text-white-50"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></small>
        </div>
        <?php if (!$n['is_read']): ?>
        <form method="POST" action="/expert/notifications/read/<?= $n['id'] ?>">
          <button class="btn btn-sm btn-outline-luma" title="Marquer comme lu"><i class="bi bi-check"></i></button>
        </form>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state" data-animate="fade-up">
        <i class="bi bi-bell-slash empty-state-icon"></i>
        <p class="text-white-50">Aucune notification.</p>
      </div>
    <?php endif; ?>
  </div>
</div>
