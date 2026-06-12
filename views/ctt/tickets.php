<div class="row">
  <div class="col-12">
    <h1 class="font-heading mb-4"><i class="bi bi-ticket me-2 text-pink"></i>Gestion des Tickets</h1>

    <ul class="nav nav-pills mb-4 gap-2" role="tablist" data-animate="fade-up">
      <li class="nav-item"><a class="filter-pill active" href="?status=all">Tous</a></li>
      <li class="nav-item"><a class="filter-pill" href="?status=ouvert">Ouverts</a></li>
      <li class="nav-item"><a class="filter-pill" href="?status=en_cours">En cours</a></li>
      <li class="nav-item"><a class="filter-pill" href="?status=résolu">Résolus</a></li>
      <li class="nav-item"><a class="filter-pill" href="?status=fermé">Fermés</a></li>
    </ul>

    <div class="card-luma p-4" data-animate="fade-up">
      <div class="table-responsive">
        <table class="table table-luma">
          <thead>
            <tr>
              <th>ID</th>
              <th>Sujet</th>
              <th>De</th>
              <th>Priorité</th>
              <th>Statut</th>
              <th>Assigné à</th>
              <th>Actions</th>
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
                    <span class="badge bg-danger">Urgent</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Normal</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php $statusClasses = ['ouvert' => 'bg-success', 'en_cours' => 'bg-warning text-dark', 'résolu' => 'bg-info', 'fermé' => 'bg-secondary']; ?>
                  <span class="badge <?= $statusClasses[$t['status']] ?? 'bg-secondary' ?>"><?= ucfirst($t['status']) ?></span>
                </td>
                <td><?= htmlspecialchars($t['assigned_name'] ?? '-') ?></td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-luma dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                    <ul class="dropdown-menu dropdown-menu-dark bg-luma-glass">
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

              <div class="modal fade" id="assignModal<?= $t['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content bg-luma-glass">
                    <form method="POST" action="/ctt/tickets/<?= $t['id'] ?>/assign">
                      <div class="modal-header">
                        <h5 class="modal-title">Assigner ticket #<?= $t['id'] ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <select name="assigned_to" class="form-select form-control-luma" required>
                          <option value="">Choisir un agent...</option>
                          <?php foreach ($agents as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['name']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-outline-luma" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-luma">Assigner</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <div class="modal fade" id="viewModal<?= $t['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content bg-luma-glass">
                    <div class="modal-header">
                      <h5 class="modal-title">[#<?= $t['id'] ?>] <?= htmlspecialchars($t['subject']) ?></h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <p class="text-white-50 small">De <?= htmlspecialchars($t['user_name']) ?> — <?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></p>
                      <p><?= nl2br(htmlspecialchars($t['message'])) ?></p>
                      <?php if (!empty($t['responses'])): ?>
                        <div class="divider-accent"></div>
                        <h6 class="font-heading">Conversation</h6>
                        <?php foreach ($t['responses'] as $r): ?>
                          <div class="bg-dark p-3 rounded-3 mb-2">
                            <strong class="text-light-pink"><?= htmlspecialchars($r['user_name']) ?></strong>
                            <small class="text-white-50"> — <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></small>
                            <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($r['message'])) ?></p>
                          </div>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                      <form method="POST" action="/ctt/tickets/<?= $t['id'] ?>/respond" class="w-100">
                        <textarea name="message" class="form-control form-control-luma mb-2" rows="2" placeholder="Réponse..." required></textarea>
                        <button type="submit" class="btn btn-luma btn-sm">Envoyer</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center text-white-50">Aucun ticket trouvé.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
