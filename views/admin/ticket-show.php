<div class="content-dashboard">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Ticket #<?= $ticket['id'] ?>: <?= htmlspecialchars($ticket['subject']) ?></h5>
    <a href="/admin/tickets" class="btn-dashboard btn-dashboard-outline btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
  </div>

  <div class="row g-4">
    <div class="col-md-8">
      <div class="card-dashboard mb-4">
        <div class="card-dashboard-header">
          <h5 class="card-dashboard-title">Messages</h5>
        </div>
        <div class="card-dashboard-body">
          <?php if (empty($messages)): ?>
          <p class="td-muted mb-0">Aucun message.</p>
          <?php else: ?>
          <div class="d-flex flex-column gap-3">
            <?php foreach ($messages as $msg): ?>
            <div class="p-3 rounded" style="background: #f8f9fc; border-left: 3px solid var(--dprimary);">
              <div class="d-flex justify-content-between mb-2">
                <strong><?= htmlspecialchars($msg['user_name'] ?? 'Inconnu') ?></strong>
                <small class="td-muted"><?= htmlspecialchars($msg['created_at']) ?></small>
              </div>
              <p class="mb-0"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($ticket['status'] !== 'closed'): ?>
      <div class="card-dashboard">
        <div class="card-dashboard-header">
          <h5 class="card-dashboard-title">Répondre</h5>
        </div>
        <div class="card-dashboard-body">
          <form action="/admin/tickets/reply/<?= $ticket['id'] ?>" method="post" class="form-dashboard">
            <?= \App\Core\Session::csrf_field() ?>
            <div class="form-floating">
              <textarea name="message" class="form-control" id="replyMsg" rows="4" required></textarea>
              <label for="replyMsg">Votre message</label>
            </div>
            <button type="submit" class="btn-dashboard btn-dashboard-primary">Envoyer</button>
          </form>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <div class="col-md-4">
      <div class="card-dashboard">
        <div class="card-dashboard-body">
          <ul class="list-unstyled mb-0">
            <li class="mb-3">
              <small class="td-muted d-block">Statut</small>
              <?php if ($ticket['status'] === 'open'): ?>
              <span class="badge-dashboard success">Ouvert</span>
              <?php elseif ($ticket['status'] === 'in_progress'): ?>
              <span class="badge-dashboard info">En cours</span>
              <?php else: ?>
              <span class="badge-dashboard">Fermé</span>
              <?php endif; ?>
            </li>
            <li class="mb-3">
              <small class="td-muted d-block">Priorité</small>
              <?php if ($ticket['priority'] === 'high'): ?>
              <span class="badge-dashboard danger">Haute</span>
              <?php elseif ($ticket['priority'] === 'medium'): ?>
              <span class="badge-dashboard warning">Moyenne</span>
              <?php else: ?>
              <span class="badge-dashboard">Basse</span>
              <?php endif; ?>
            </li>
            <li class="mb-3">
              <small class="td-muted d-block">Utilisateur</small>
              <span><?= htmlspecialchars($ticket['user_name'] ?? 'Inconnu') ?></span><br>
              <small class="td-muted"><?= htmlspecialchars($ticket['user_email'] ?? '') ?></small>
            </li>
            <li class="mb-3">
              <small class="td-muted d-block">Assigné à</small>
              <span><?= htmlspecialchars($ticket['expert_name'] ?? '-') ?></span>
            </li>
            <li class="mb-3">
              <small class="td-muted d-block">Créé le</small>
              <span><?= htmlspecialchars($ticket['created_at']) ?></span>
            </li>
          </ul>
          <hr>
          <div class="d-flex flex-column gap-2">
            <?php if ($ticket['status'] === 'open'): ?>
            <form action="/admin/tickets/assign/<?= $ticket['id'] ?>" method="post" class="d-inline">
              <?= \App\Core\Session::csrf_field() ?>
              <button type="submit" class="btn-dashboard btn-dashboard-primary btn-sm">Assigner</button>
            </form>
            <form action="/admin/tickets/close/<?= $ticket['id'] ?>" method="post" class="d-inline">
              <?= \App\Core\Session::csrf_field() ?>
              <button type="submit" class="btn-dashboard btn-dashboard-primary btn-sm">Fermer</button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>