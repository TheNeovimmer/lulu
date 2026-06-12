<div class="container-fluid py-4">
  <div data-animate="fade-up">
    <h1 class="section-title text-white mb-1">Paramètres</h1>
    <p class="section-subtitle text-white-50 mb-4">Configurez les paramètres de la plateforme</p>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="card card-luma animate-scale-in" data-animate="fade-up">
    <div class="card-body">
      <form action="/admin/settings" method="post">
        <?php foreach ($settings as $setting): ?>
        <div class="mb-3">
          <label class="form-label text-white-50"><?= htmlspecialchars($setting['label']) ?></label>
          <?php if ($setting['type'] === 'textarea'): ?>
          <textarea name="settings[<?= htmlspecialchars($setting['key']) ?>]" class="form-control form-control-luma" rows="4"><?= htmlspecialchars($setting['value']) ?></textarea>
          <?php else: ?>
          <input type="<?= htmlspecialchars($setting['type'] ?? 'text') ?>" name="settings[<?= htmlspecialchars($setting['key']) ?>]" class="form-control form-control-luma" value="<?= htmlspecialchars($setting['value']) ?>">
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-luma">Enregistrer les paramètres</button>
      </form>
    </div>
  </div>
</div>
