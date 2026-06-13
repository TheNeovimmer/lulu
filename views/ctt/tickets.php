<div class="row">
  <div class="col-12">
    <ul class="nav nav-pills mb-4 gap-2" role="tablist">
      <li class="nav-item"><a class="filter-pill active" href="?status=all">Tous</a></li>
      <li class="nav-item"><a class="filter-pill" href="?status=ouvert">Ouverts</a></li>
      <li class="nav-item"><a class="filter-pill" href="?status=en_cours">En cours</a></li>
      <li class="nav-item"><a class="filter-pill" href="?status=résolu">Résolus</a></li>
      <li class="nav-item"><a class="filter-pill" href="?status=fermé">Fermés</a></li>
    </ul>

    <div class="card-dashboard">
      <div class="table-wrapper">
        <table class="table-dashboard">
          <thead>
            <tr>
              <th>ID</th>
              <th>Sujet</th>
              <th>De</th>
              <th>Priorité</th>
              <th>Statut</th>
              <th>Assigné à</th>
              <th class="actions-cell">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($tickets)): ?>
              <?php foreach ($tickets as $t): ?>
              <tr>
                <td><?= $t['id'] ?></td>
                <td><?= htmlspecialchars($t['subject']) ?></td>
                <td><?= htmlspecialchars($t['user_name'] ?? '-') ?></td>
                <td>
                  <?php if ($t['priority'] === 'urgent'): ?>
                    <span class="badge-dashboard danger">Urgent</span>
                  <?php else: ?>
                    <span class="badge-dashboard info">Normal</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php $statusClasses = ['ouvert' => 'success', 'en_cours' => 'warning', 'résolu' => 'info', 'fermé' => 'danger']; ?>
                  <span class="badge-dashboard <?= $statusClasses[$t['status']] ?? 'info' ?>"><?= ucfirst($t['status']) ?></span>
                </td>
                <td><?= htmlspecialchars($t['assigned_name'] ?? '-') ?></td>
                <td class="actions-cell">
                  <div class="dropdown">
                    <button class="btn-dashboard btn-dashboard-outline btn-dashboard-sm dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                    <ul class="dropdown-menu">
                      <li>
                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#assignModal<?= $t['id'] ?>"><i class="bi bi-person me-2"></i>Assigner</button>
                      </li>
                      <?php $nextStatuses = ['ouvert' => 'en_cours', 'en_cours' => 'résolu', 'résolu' => 'fermé']; ?>
                      <?php if (isset($nextStatuses[$t['status']])): ?>
                      <li>
                        <form method="POST" action="/ctt/tickets/<?= $t['id'] ?>/status" class="d-inline">
                          <input type="hidden" name="status" value="<?= $nextStatuses[$t['status']] ?>">
                          <button type="submit" class="dropdown-item"><i class="bi bi-arrow-right me-2"></i>Passer en « <?= ucfirst($nextStatuses[$t['status']]) ?> »</button>
                        </form>
                      </li>
                      <?php endif; ?>
                      <li><hr class="dropdown-divider"></li>
                      <li>
                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#viewModal<?= $t['id'] ?>"><i class="bi bi-eye me-2"></i>Voir</button>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>

              <div class="modal fade modal-dashboard" id="assignModal<?= $t['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form method="POST" action="/ctt/tickets/<?= $t['id'] ?>/assign">
                      <div class="modal-header">
                        <h5 class="modal-title">Assigner ticket #<?= $t['id'] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <select name="assigned_to" class="form-select" required>
                          <option value="">Choisir un agent...</option>
                          <?php foreach ($agents as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['name']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-dashboard btn-dashboard-outline" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-dashboard btn-dashboard-primary">Assigner</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <div class="modal fade modal-dashboard" id="viewModal<?= $t['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">[#<?= $t['id'] ?>] <?= htmlspecialchars($t['subject']) ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <p class="text-muted small">De <?= htmlspecialchars($t['user_name']) ?> — <?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></p>
                      <p><?= nl2br(htmlspecialchars($t['message'])) ?></p>
                      <?php if (!empty($t['responses'])): ?>
                        <h6 class="fw-semibold mt-3 mb-2">Conversation</h6>
                        <?php foreach ($t['responses'] as $r): ?>
                          <div class="bg-light p-3 rounded-3 mb-2" style="background: var(--dprimary-subtle);">
                            <strong style="color: var(--dprimary);"><?= htmlspecialchars($r['user_name']) ?></strong>
                            <small class="text-muted"> — <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></small>
                            <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($r['message'])) ?></p>
                          </div>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                      <form method="POST" action="/ctt/tickets/<?= $t['id'] ?>/respond" class="w-100 form-dashboard">
                        <div class="form-floating">
                          <textarea name="message" class="form-control" rows="2" placeholder="Réponse..." required></textarea>
                          <label>Réponse</label>
                        </div>
                        <button type="submit" class="btn btn-dashboard btn-dashboard-primary btn-dashboard-sm mt-2">Envoyer</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center text-muted py-4">Aucun ticket trouvé.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
