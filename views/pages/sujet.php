<div class="container py-5" style="max-width:900px;" data-animate="fade-up">
  <a href="/communaute" class="text-light-pink text-decoration-none mb-3 d-inline-block"><i class="bi bi-arrow-left me-1"></i>Retour à la communauté</a>

  <div class="card-luma p-4 mb-4" data-animate="fade-up">
    <h1 class="font-heading"><?= htmlspecialchars($post['title']) ?></h1>
    <p class="text-white-50 small mb-3">
      Par <?= htmlspecialchars($post['author_name']) ?> — <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?>
      <?php if (!empty($post['is_expert'])): ?>
        <span class="badge bg-pink ms-2">Experte</span>
      <?php endif; ?>
    </p>
    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

    <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top border-secondary">
      <button class="btn btn-sm <?= $post['user_liked'] ? 'btn-luma' : 'btn-outline-luma' ?>" onclick="toggleLike(<?= $post['id'] ?>)">
        <i class="bi <?= $post['user_liked'] ? 'bi-heart-fill' : 'bi-heart' ?> me-1"></i>
        <span id="likes-count"><?= $post['likes_count'] ?? 0 ?></span>
      </button>
    </div>
  </div>

  <div class="divider-accent"></div>

  <h5 class="font-heading mb-3">Commentaires (<?= count($comments) ?>)</h5>

  <?php if (!empty($comments)): ?>
    <div class="animate-stagger">
    <?php foreach ($comments as $c): ?>
    <div class="card-luma p-3 mb-3 <?= !empty($c['is_expert']) ? 'border-pink' : '' ?>" data-animate="fade-up">
      <div class="d-flex align-items-start gap-2">
        <div class="flex-grow-1">
          <strong class="<?= !empty($c['is_expert']) ? 'text-light-pink' : 'text-white' ?>">
            <?= htmlspecialchars($c['user_name']) ?>
            <?php if (!empty($c['is_expert'])): ?>
              <span class="badge bg-pink ms-1">Experte</span>
            <?php endif; ?>
          </strong>
          <small class="text-white-50"> — <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></small>
          <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($c['content'])) ?></p>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="empty-state mb-4">
      <p class="text-white-50">Aucun commentaire pour le moment. Soyez la première à commenter !</p>
    </div>
  <?php endif; ?>

  <?php if (\App\Core\Session::has('user_id')): ?>
  <div class="card-luma p-4" data-animate="fade-up">
    <h6 class="font-heading mb-3">Ajouter un commentaire</h6>
    <form method="POST" action="/sujet/<?= $post['id'] ?>/comment">
      <div class="mb-3">
        <textarea name="content" class="form-control form-control-luma" rows="3" placeholder="Votre message..." required></textarea>
      </div>
      <button type="submit" class="btn btn-luma">Publier</button>
    </form>
  </div>
  <?php else: ?>
  <div class="card-luma p-4 text-center" data-animate="fade-up">
    <p class="text-white-50 mb-0"><a href="/auth/login" class="text-light-pink">Connectez-vous</a> pour ajouter un commentaire.</p>
  </div>
  <?php endif; ?>
</div>

<script>
function toggleLike(postId) {
  fetch('/sujet/' + postId + '/like', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(data => {
      const btn = event.target.closest('button');
      const icon = btn.querySelector('i');
      const count = document.getElementById('likes-count');
      if (data.liked) {
        btn.classList.remove('btn-outline-luma');
        btn.classList.add('btn-luma');
        icon.classList.remove('bi-heart');
        icon.classList.add('bi-heart-fill');
      } else {
        btn.classList.remove('btn-luma');
        btn.classList.add('btn-outline-luma');
        icon.classList.remove('bi-heart-fill');
        icon.classList.add('bi-heart');
      }
      count.textContent = data.count;
    });
}
</script>
