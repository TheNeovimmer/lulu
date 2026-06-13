<div class="content-dashboard">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($tickets)): ?>
  <div class="empty-state-dashboard">
    <i class="bi bi-ticket-detailed"></i>
    <h5>Aucun ticket</h5>
    <p>Aucun ticket de support pour le moment.</p>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table class="table-dashboard">
      <thead>
        <tr>
          <th>Sujet</th>
          <th>Utilisateur</th>
          <th>Priorité</th>
          <th>Statut</th>
          <th>Assigné à</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tickets as $ticket): ?>
        <tr>
          <td><?= htmlspecialchars($ticket['subject']) ?></td>
          <td class="td-muted"><?= htmlspecialchars($ticket['user_name']) ?></td>
          <td>
            <?php if ($ticket['priority'] === 'high'): ?>
            <span class="badge-dashboard danger">Haute</span>
            <?php elseif ($ticket['priority'] === 'medium'): ?>
            <span class="badge-dashboard warning">Moyenne</span>
            <?php else: ?>
            <span class="badge-dashboard">Basse</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($ticket['status'] === 'open'): ?>
            <span class="badge-dashboard success">Ouvert</span>
            <?php elseif ($ticket['status'] === 'in_progress'): ?>
            <span class="badge-dashboard info">En cours</span>
            <?php else: ?>
            <span class="badge-dashboard">Fermé</span>
            <?php endif; ?>
          </td>
          <td class="td-muted"><?= htmlspecialchars($ticket['assigned_to'] ?? '-') ?></td>
          <td class="td-muted"><?= htmlspecialchars($ticket['created_at']) ?></td>
          <td class="actions-cell">
            <a href="/admin/tickets/view/<?= $ticket['id'] ?>" class="btn-icon"><i class="bi bi-eye"></i></a>
            <div class="dropdown d-inline">
              <button class="btn-icon" data-bs-toggle="dropdown"><i class="bi bi-shuffle"></i></button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/admin/tickets/status/<?= $ticket['id'] ?>?status=open">Ouvert</a></li>
                <li><a class="dropdown-item" href="/admin/tickets/status/<?= $ticket['id'] ?>?status=in_progress">En cours</a></li>
                <li><a class="dropdown-item" href="/admin/tickets/status/<?= $ticket['id'] ?>?status=closed">Fermé</a></li>
              </ul>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
