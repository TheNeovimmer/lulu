<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="card-dashboard">
    <div class="card-dashboard-body">
      <form action="/admin/parametres" method="post" class="form-dashboard">
        <?= \App\Core\Session::csrf_field() ?>
        <?php foreach ($settings as $setting): ?>
        <div class="form-floating">
          <input type="text" name="settings[<?= htmlspecialchars($setting['key_name']) ?>]" class="form-control" id="setting_<?= htmlspecialchars($setting['key_name']) ?>" value="<?= htmlspecialchars($setting['value'] ?? '') ?>">
          <label for="setting_<?= htmlspecialchars($setting['key_name']) ?>"><?= htmlspecialchars($setting['key_name']) ?></label>
        </div>
        <?php endforeach; ?>
        <button type="submit" class="btn-dashboard btn-dashboard-primary">Enregistrer les paramètres</button>
      </form>
    </div>
  </div>
</div>
