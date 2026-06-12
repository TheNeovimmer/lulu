<div class="container-fluid py-4">
  <div data-animate="fade-up">
    <h1 class="section-title text-white mb-1">Messages reçus</h1>
    <p class="section-subtitle text-white-50 mb-4">Consultez les messages envoyés depuis le formulaire de contact</p>
  </div>

  <div class="divider-accent mb-4"></div>

  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
  <div class="alert alert-success animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
  <div class="alert alert-danger animate-fade-up"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($contacts)): ?>
  <div class="empty-state card card-luma p-5 text-center" data-animate="fade-up">
    <div class="stat-icon text-light-pink mb-3">
      <i class="bi bi-envelope-open fs-1"></i>
    </div>
    <h4 class="text-white mb-2">Aucun message</h4>
    <p class="text-white-50 mb-0">Aucun message reçu pour le moment.</p>
  </div>
  <?php else: ?>
  <div class="card card-luma" data-animate="fade-up">
    <div class="card-body p-0">
      <table class="table table-luma mb-0">
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
            <td><?= htmlspecialchars($contact['email']) ?></td>
            <td><?= htmlspecialchars($contact['subject']) ?></td>
            <td><?= htmlspecialchars(mb_substr($contact['message'], 0, 60)) ?><?= mb_strlen($contact['message']) > 60 ? '...' : '' ?></td>
            <td><?= htmlspecialchars($contact['created_at']) ?></td>
            <td>
              <?php if ($contact['read']): ?>
              <span class="badge bg-secondary">Lu</span>
              <?php else: ?>
              <span class="badge bg-luma">Non lu</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="/admin/contacts/view/<?= $contact['id'] ?>" class="btn btn-outline-luma btn-sm">Voir</a>
              <form action="/admin/contacts/delete/<?= $contact['id'] ?>" method="post" class="d-inline">
                <button type="submit" class="btn btn-outline-danger-luma btn-sm" onclick="return confirm('Supprimer ce message ?')">Supprimer</button>
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
