<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($contacts)): ?>
  <div class="empty-state-dashboard">
    <i class="bi bi-envelope-open"></i>
    <h5>Aucun message</h5>
    <p>Aucun message reçu pour le moment.</p>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table class="table-dashboard">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Email</th>
          <th>Sujet</th>
          <th>Message</th>
          <th>Date</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($contacts as $contact): ?>
        <tr>
          <td><?= htmlspecialchars($contact['name']) ?></td>
          <td class="td-muted"><?= htmlspecialchars($contact['email']) ?></td>
          <td class="td-muted"><?= htmlspecialchars($contact['subject']) ?></td>
          <td class="td-muted"><?= htmlspecialchars(mb_substr($contact['message'], 0, 60)) ?><?= mb_strlen($contact['message']) > 60 ? '...' : '' ?></td>
          <td class="td-muted"><?= htmlspecialchars($contact['created_at']) ?></td>
          <td>
            <?php if ($contact['is_read']): ?>
            <span class="badge-dashboard">Lu</span>
            <?php else: ?>
            <span class="badge-dashboard info">Non lu</span>
            <?php endif; ?>
          </td>
          <td class="actions-cell">
            <form action="/admin/contacts/delete/<?= $contact['id'] ?>" method="post" class="inline-form">
              <?= \App\Core\Session::csrf_field() ?>
              <button type="submit" class="btn-icon danger" onclick="return confirm('Supprimer ce message ?')"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
