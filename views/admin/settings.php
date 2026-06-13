<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="card-dashboard">
    <div class="card-dashboard-body">
      <form action="/admin/settings" method="post" class="form-dashboard">
        <?php foreach ($settings as $setting): ?>
        <div class="form-floating">
          <?php if ($setting['type'] === 'textarea'): ?>
          <textarea name="settings[<?= htmlspecialchars($setting['key']) ?>]" class="form-control" id="setting_<?= htmlspecialchars($setting['key']) ?>" rows="4"><?= htmlspecialchars($setting['value']) ?></textarea>
          <?php else: ?>
          <input type="<?= htmlspecialchars($setting['type'] ?? 'text') ?>" name="settings[<?= htmlspecialchars($setting['key']) ?>]" class="form-control" id="setting_<?= htmlspecialchars($setting['key']) ?>" value="<?= htmlspecialchars($setting['value']) ?>">
          <?php endif; ?>
          <label for="setting_<?= htmlspecialchars($setting['key']) ?>"><?= htmlspecialchars($setting['label']) ?></label>
        </div>
        <?php endforeach; ?>
        <button type="submit" class="btn-dashboard btn-dashboard-primary">Enregistrer les paramètres</button>
      </form>
    </div>
  </div>
</div>
