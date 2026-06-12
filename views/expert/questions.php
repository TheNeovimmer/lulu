<div class="row justify-content-center">
  <div class="col-lg-10">
    <h1 class="font-heading mb-4"><i class="bi bi-question-circle me-2 text-pink"></i>Questions des Mamans</h1>

    <?php if (!empty($questions)): ?>
      <div class="animate-stagger">
      <?php foreach ($questions as $q): ?>
      <div class="card-luma p-4 mb-3" id="question-<?= $q['id'] ?>" data-animate="fade-up">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h5 class="font-heading"><?= htmlspecialchars($q['title']) ?></h5>
            <p class="text-white-50 small mb-2">
              Par <?= htmlspecialchars($q['author_name']) ?> — <?= date('d/m/Y H:i', strtotime($q['created_at'])) ?>
              <span class="ms-2 badge bg-pink"><?= $q['reply_count'] ?> réponse(s)</span>
            </p>
            <p><?= nl2br(htmlspecialchars($q['content'])) ?></p>
          </div>
        </div>

        <?php if (!empty($q['answers'])): ?>
          <?php foreach ($q['answers'] as $a): ?>
          <div class="bg-luma-glass p-3 rounded-3 mb-2 ms-4">
            <strong class="text-light-pink"><?= htmlspecialchars($a['user_name']) ?></strong>
            <small class="text-white-50"> — <?= date('d/m/Y H:i', strtotime($a['created_at'])) ?></small>
            <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($a['content'])) ?></p>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <button class="btn btn-luma btn-sm mt-2" onclick="toggleReplyForm(<?= $q['id'] ?>)">
          <i class="bi bi-reply me-1"></i>Répondre
        </button>

        <div id="reply-form-<?= $q['id'] ?>" class="mt-3 d-none">
          <form method="POST" action="/expert/questions/<?= $q['id'] ?>/answer">
            <textarea name="content" class="form-control form-control-luma mb-2" rows="3" placeholder="Votre réponse..." required></textarea>
            <button type="submit" class="btn btn-luma btn-sm">Envoyer</button>
            <button type="button" class="btn btn-outline-luma btn-sm" onclick="toggleReplyForm(<?= $q['id'] ?>)">Annuler</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state" data-animate="fade-up">
        <i class="bi bi-check-circle empty-state-icon"></i>
        <p class="text-white-50">Aucune question en attente pour le moment.</p>
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
