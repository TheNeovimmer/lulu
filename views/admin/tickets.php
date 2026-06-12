<div class="container-fluid py-4">
  <div data-animate="fade-up">
    <h1 class="section-title text-white mb-1">Tickets</h1>
    <p class="section-subtitle text-white-50 mb-4">Gérez les tickets de support</p>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($tickets)): ?>
  <div class="empty-state card card-luma p-5 text-center" data-animate="fade-up">
    <div class="stat-icon text-light-pink mb-3">
      <i class="bi bi-ticket-detailed fs-1"></i>
    </div>
    <h4 class="text-white mb-2">Aucun ticket</h4>
    <p class="text-white-50 mb-0">Aucun ticket de support pour le moment.</p>
  </div>
  <?php else: ?>
  <div class="card card-luma" data-animate="fade-up">
    <div class="card-body p-0">
      <table class="table table-luma mb-0">
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
            <td><?= htmlspecialchars($ticket['user_name']) ?></td>
            <td>
              <?php if ($ticket['priority'] === 'high'): ?>
              <span class="badge bg-danger">Haute</span>
              <?php elseif ($ticket['priority'] === 'medium'): ?>
              <span class="badge bg-warning text-dark">Moyenne</span>
              <?php else: ?>
              <span class="badge bg-secondary">Basse</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($ticket['status'] === 'open'): ?>
              <span class="badge bg-success">Ouvert</span>
              <?php elseif ($ticket['status'] === 'in_progress'): ?>
              <span class="badge bg-info">En cours</span>
              <?php else: ?>
              <span class="badge bg-secondary">Fermé</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($ticket['assigned_to'] ?? '-') ?></td>
            <td><?= htmlspecialchars($ticket['created_at']) ?></td>
            <td>
              <a href="/admin/tickets/view/<?= $ticket['id'] ?>" class="btn btn-outline-luma btn-sm">Voir</a>
              <div class="dropdown d-inline">
                <button class="btn btn-outline-luma btn-sm dropdown-toggle" data-bs-toggle="dropdown">Statut</button>
                <ul class="dropdown-menu dropdown-menu-dark">
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
  </div>
  <?php endif; ?>
</div>
