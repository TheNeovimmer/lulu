<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="page-header-dashboard">
      <h1 class="page-title-dashboard"><i class="bi bi-ticket me-2"></i>Support</h1>
    </div>

    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Nouveau ticket</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/dashboard/tickets" class="form-dashboard">
          <div class="form-floating mb-3">
            <input type="text" name="subject" class="form-control" id="floatingSubject" placeholder="Sujet" required>
            <label for="floatingSubject">Sujet</label>
          </div>
          <div class="form-floating mb-3">
            <textarea name="message" class="form-control" id="floatingMessage" placeholder="Message" rows="4" style="min-height: 120px;" required></textarea>
            <label for="floatingMessage">Message</label>
          </div>
          <div class="form-floating mb-3">
            <select name="priority" class="form-select" id="floatingPriority">
              <option value="normal">Normal</option>
              <option value="urgent">Urgent</option>
            </select>
            <label for="floatingPriority">Priorité</label>
          </div>
          <button type="submit" class="btn-dashboard btn-dashboard-primary">Envoyer</button>
        </form>
      </div>
    </div>

    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title">Mes tickets</h5>
      </div>
      <div class="card-dashboard-body">
        <?php if (!empty($tickets)): ?>
        <div class="table-wrapper">
          <table class="table-dashboard">
            <thead>
              <tr>
                <th>#</th>
                <th>Sujet</th>
                <th>Date</th>
                <th>Priorité</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tickets as $t): ?>
              <tr>
                <td><?= $t['id'] ?></td>
                <td><?= htmlspecialchars($t['subject']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                <td>
                  <?php if ($t['priority'] === 'urgent'): ?>
                    <span class="badge-dashboard danger">Urgent</span>
                  <?php else: ?>
                    <span class="badge-dashboard">Normal</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php $statusClasses = ['ouvert' => 'success', 'en_cours' => 'warning', 'résolu' => 'info', 'fermé' => '']; ?>
                  <span class="badge-dashboard <?= $statusClasses[$t['status']] ?? '' ?>"><?= ucfirst($t['status']) ?></span>
                </td>
                <td>
                  <button class="btn-dashboard btn-dashboard-outline btn-dashboard-sm" data-bs-toggle="modal" data-bs-target="#ticketModal<?= $t['id'] ?>">Voir</button>
                </td>
              </tr>

              <div class="modal fade modal-dashboard" id="ticketModal<?= $t['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title"><?= htmlspecialchars($t['subject']) ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <p style="color: var(--dtext-muted); font-size: 0.85rem;">
                        <?= date('d/m/Y H:i', strtotime($t['created_at'])) ?> —
                        Priorité: <span class="badge-dashboard <?= $t['priority'] === 'urgent' ? 'danger' : '' ?>"><?= $t['priority'] ?></span>
                        Statut: <span class="badge-dashboard <?= $statusClasses[$t['status']] ?? '' ?>"><?= ucfirst($t['status']) ?></span>
                      </p>
                      <hr style="border-color: var(--dborder-light); margin: 16px 0;">
                      <p><?= nl2br(htmlspecialchars($t['message'])) ?></p>

                      <?php if (!empty($t['responses'])): ?>
                        <hr style="border-color: var(--dborder-light); margin: 16px 0;">
                        <h6 style="font-weight: 600; color: var(--dtext-dark);">Réponses</h6>
                        <?php foreach ($t['responses'] as $r): ?>
                          <div class="p-3 rounded-3 mb-2" style="background: var(--dprimary-subtle);">
                            <strong style="color: var(--dprimary);"><?= htmlspecialchars($r['user_name']) ?></strong>
                            <small style="color: var(--dtext-muted);"> - <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></small>
                            <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($r['message'])) ?></p>
                          </div>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div class="empty-state-dashboard">
          <i class="bi bi-ticket"></i>
          <p>Aucun ticket pour le moment.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
