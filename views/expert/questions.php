<div class="row justify-content-center">
  <div class="col-lg-10">
    <?php if (!empty($questions)): ?>
      <?php foreach ($questions as $q): ?>
      <div class="card-dashboard mb-3" id="question-<?= $q['id'] ?>">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h5 class="card-dashboard-title"><?= htmlspecialchars($q['title']) ?></h5>
            <p class="text-muted small mb-2">
              Par <?= htmlspecialchars($q['author_name']) ?> — <?= date('d/m/Y H:i', strtotime($q['created_at'])) ?>
              <span class="ms-2 badge-dashboard info">0 réponse(s)</span>
            </p>
            <p><?= nl2br(htmlspecialchars($q['content'])) ?></p>
          </div>
        </div>

        <?php if (!empty($q['answers'])): ?>
          <?php foreach ($q['answers'] as $a): ?>
          <div class="bg-light rounded-3 p-3 mb-2 ms-4" style="background: var(--dprimary-subtle);">
            <strong style="color: var(--dprimary);"><?= htmlspecialchars($a['user_name']) ?></strong>
            <small class="text-muted"> — <?= date('d/m/Y H:i', strtotime($a['created_at'])) ?></small>
            <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($a['content'])) ?></p>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <button class="btn btn-dashboard btn-dashboard-outline btn-dashboard-sm mt-2" onclick="toggleReplyForm(<?= $q['id'] ?>)">
          <i class="bi bi-reply me-1"></i>Répondre
        </button>

        <div id="reply-form-<?= $q['id'] ?>" class="mt-3 d-none">
          <form method="POST" action="/expert/questions/<?= $q['id'] ?>" class="form-dashboard">
            <?= \App\Core\Session::csrf_field() ?>
            <div class="form-floating">
              <textarea name="content" class="form-control" rows="3" placeholder="Votre réponse..." required></textarea>
              <label>Votre réponse</label>
            </div>
            <button type="submit" class="btn btn-dashboard btn-dashboard-primary btn-dashboard-sm">Envoyer</button>
            <button type="button" class="btn btn-dashboard btn-dashboard-outline btn-dashboard-sm" onclick="toggleReplyForm(<?= $q['id'] ?>)">Annuler</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty-state-dashboard">
        <i class="bi bi-check-circle"></i>
        <h5>Aucune question</h5>
        <p>Aucune question en attente pour le moment.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
function toggleReplyForm(id) {
  const form = document.getElementById('reply-form-' + id);
  form.classList.toggle('d-none');
}
</script>
