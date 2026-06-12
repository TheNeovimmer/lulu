<div class="container-fluid py-4">
  <div data-animate="fade-up">
    <h1 class="section-title text-white mb-1">Abonnés newsletter</h1>
    <p class="section-subtitle text-white-50 mb-4">Liste des abonnés à la newsletter</p>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($subscribers)): ?>
  <div class="empty-state card card-luma p-5 text-center" data-animate="fade-up">
    <div class="stat-icon text-light-pink mb-3">
      <i class="bi bi-envelope-paper fs-1"></i>
    </div>
    <h4 class="text-white mb-2">Aucun abonné</h4>
    <p class="text-white-50 mb-0">Personne ne s'est encore inscrit à la newsletter.</p>
  </div>
  <?php else: ?>
  <div class="card card-luma" data-animate="fade-up">
    <div class="card-body p-0">
      <table class="table table-luma mb-0">
        <thead>
          <tr>
            <th>Email</th>
            <th>Inscrit le</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($subscribers as $sub): ?>
          <tr>
            <td><?= htmlspecialchars($sub['email']) ?></td>
            <td><?= htmlspecialchars($sub['created_at']) ?></td>
            <td>
              <form action="/admin/newsletters/delete/<?= $sub['id'] ?>" method="post" class="d-inline">
                <button type="submit" class="btn btn-outline-danger-luma btn-sm" onclick="return confirm('Supprimer cet abonné ?')">Supprimer</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
</div>
