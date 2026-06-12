<div class="row justify-content-center">
  <div class="col-lg-10">
    <h1 class="font-heading mb-4"><i class="bi bi-ticket me-2 text-pink"></i>Support</h1>

    <div class="card-luma p-4 mb-4" data-animate="fade-up">
      <h5 class="section-title">Nouveau ticket</h5>
      <div class="divider-accent"></div>
      <form method="POST" action="/dashboard/tickets">
        <div class="mb-3">
          <label class="form-label text-white-50">Sujet</label>
          <input type="text" name="subject" class="form-control form-control-luma" required>
        </div>
        <div class="mb-3">
          <label class="form-label text-white-50">Message</label>
          <textarea name="message" class="form-control form-control-luma" rows="4" required></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label text-white-50">Priorité</label>
          <select name="priority" class="form-select form-control-luma">
            <option value="normal">Normal</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
        <button type="submit" class="btn btn-luma">Envoyer</button>
      </form>
    </div>

    <div class="card-luma p-4" data-animate="fade-up">
      <h5 class="section-title">Mes tickets</h5>
      <div class="divider-accent"></div>
      <?php if (!empty($tickets)): ?>
      <div class="table-responsive">
        <table class="table table-luma">
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
                  <span class="badge bg-danger">Urgent</span>
                <?php else: ?>
                  <span class="badge bg-secondary">Normal</span>
                <?php endif; ?>
              </td>
              <td>
                <?php $statusClasses = ['ouvert' => 'bg-success', 'en_cours' => 'bg-warning text-dark', 'résolu' => 'bg-info', 'fermé' => 'bg-secondary']; ?>
                <span class="badge <?= $statusClasses[$t['status']] ?? 'bg-secondary' ?>"><?= ucfirst($t['status']) ?></span>
              </td>
              <td>
                <button class="btn btn-sm btn-outline-luma" data-bs-toggle="modal" data-bs-target="#ticketModal<?= $t['id'] ?>">Voir</button>
              </td>
            </tr>

            <div class="modal fade" id="ticketModal<?= $t['id'] ?>" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content bg-luma-glass">
                  <div class="modal-header">
                    <h5 class="modal-title"><?= htmlspecialchars($t['subject']) ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <p class="text-white-50 small">
                      <?= date('d/m/Y H:i', strtotime($t['created_at'])) ?> —
                      Priorité: <span class="badge <?= $t['priority'] === 'urgent' ? 'bg-danger' : 'bg-secondary' ?>"><?= $t['priority'] ?></span>
                      Statut: <span class="badge <?= $statusClasses[$t['status']] ?? 'bg-secondary' ?>"><?= ucfirst($t['status']) ?></span>
                    </p>
                    <div class="divider-accent"></div>
                    <p><?= nl2br(htmlspecialchars($t['message'])) ?></p>

                    <?php if (!empty($t['responses'])): ?>
                      <div class="divider-accent"></div>
                      <h6 class="font-heading">Réponses</h6>
                      <?php foreach ($t['responses'] as $r): ?>
                        <div class="bg-dark p-3 rounded-3 mb-2">
                          <strong class="text-light-pink"><?= htmlspecialchars($r['user_name']) ?></strong>
                          <small class="text-white-50"> - <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></small>
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
      <div class="empty-state">
        <i class="bi bi-ticket empty-state-icon"></i>
        <p class="text-white-50">Aucun ticket pour le moment.</p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
