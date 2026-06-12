<div class="container py-5" data-animate="fade-up">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
      <h1 class="font-heading mb-1">Communauté</h1>
      <p class="text-white-50 mb-0">Échangez, partagez et trouvez du soutien.</p>
    </div>
    <?php if (\App\Core\Session::has('user_id')): ?>
      <button class="btn btn-luma" data-bs-toggle="modal" data-bs-target="#newPostModal"><i class="bi bi-plus-circle me-1"></i>Nouveau sujet</button>
    <?php else: ?>
      <a href="/auth/login" class="btn btn-luma"><i class="bi bi-plus-circle me-1"></i>Connectez-vous pour poster</a>
    <?php endif; ?>
  </div>

  <?php if (!empty($posts)): ?>
    <div class="animate-stagger">
    <?php foreach ($posts as $p): ?>
    <div class="card-luma p-4 mb-3" data-animate="fade-up">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <a href="/sujet/<?= $p['id'] ?>" class="text-white text-decoration-none">
            <h5 class="font-heading"><?= htmlspecialchars($p['title']) ?></h5>
          </a>
          <p class="text-white-50 small mb-2">
            Par <?= htmlspecialchars($p['author_name']) ?> — <?= date('d/m/Y H:i', strtotime($p['created_at'])) ?>
            <?php if (!empty($p['is_expert'])): ?>
              <span class="badge bg-pink ms-2">Experte</span>
            <?php endif; ?>
          </p>
          <p><?= nl2br(htmlspecialchars(mb_substr($p['content'], 0, 300))) ?><?= mb_strlen($p['content']) > 300 ? '...' : '' ?></p>
        </div>
        <div class="text-end text-white-50 small" style="min-width:80px;">
          <div><i class="bi bi-heart me-1"></i><?= $p['likes_count'] ?? 0 ?></div>
          <div><i class="bi bi-chat me-1"></i><?= $p['comments_count'] ?? 0 ?></div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav>
      <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= ($_GET['page'] ?? 1) == $i ? 'active' : '' ?>">
          <a class="page-link bg-luma-glass border-secondary text-white" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
      </ul>
    </nav>
    <?php endif; ?>
  <?php else: ?>
  <div class="empty-state">
    <i class="bi bi-chat-dots text-pink fs-1 mb-3"></i>
    <p class="text-white-50">Aucun sujet pour le moment. Soyez la première !</p>
  </div>
  <?php endif; ?>
</div>

<?php if (\App\Core\Session::has('user_id')): ?>
<div class="modal fade" id="newPostModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-luma-glass">
      <form method="POST" action="/communaute">
        <div class="modal-header">
          <h5 class="modal-title">Nouveau sujet</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label text-white-50">Titre</label>
            <input type="text" name="title" class="form-control form-control-luma" required>
          </div>
          <div class="mb-3">
            <label class="form-label text-white-50">Contenu</label>
            <textarea name="content" class="form-control form-control-luma" rows="5" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-luma" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-luma">Publier</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>
