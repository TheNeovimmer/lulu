<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($subscribers)): ?>
  <div class="empty-state-dashboard">
    <i class="bi bi-envelope-paper"></i>
    <h5>Aucun abonné</h5>
    <p>Personne ne s'est encore inscrit à la newsletter.</p>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table class="table-dashboard">
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
          <td class="td-muted"><?= htmlspecialchars($sub['email']) ?></td>
          <td class="td-muted"><?= htmlspecialchars($sub['created_at']) ?></td>
          <td class="actions-cell">
            <form action="/admin/newsletters/delete/<?= $sub['id'] ?>" method="post" class="inline-form">
              <?= \App\Core\Session::csrf_field() ?>
              <button type="submit" class="btn-icon danger" onclick="return confirm('Supprimer cet abonné ?')"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
