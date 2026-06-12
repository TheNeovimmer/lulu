<section class="py-5" data-animate="fade-up">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8">
        <?php if ($article['image']): ?>
          <img src="<?= htmlspecialchars($article['image']) ?>" class="img-fluid rounded-3 mb-4" alt="">
        <?php endif; ?>
        <h1 class="font-heading" style="font-size:48px;"><?= htmlspecialchars($article['title']) ?></h1>
        <div class="text-light-pink mb-4"><?= date('d M Y', strtotime($article['created_at'])) ?> . Par <?= htmlspecialchars($article['category_name'] ?? 'LUMA') ?></div>
        <div class="fs-5 lh-lg"><?= nl2br(htmlspecialchars($article['content'])) ?></div>

        <div class="divider-accent my-5"></div>

        <h2 class="font-heading mb-4" style="font-size:32px;">Commentaires</h2>
        <?php if (empty($comments)): ?>
          <div class="empty-state mb-4">
            <p class="text-white-50">Soyez la première à commenter !</p>
          </div>
        <?php else: ?>
          <div class="animate-stagger">
          <?php foreach ($comments as $comment): ?>
          <div class="card-testimonial mb-3" data-animate="fade-up">
            <strong class="text-light-pink"><?= htmlspecialchars($comment['user_name'] ?? 'Anonyme') ?></strong>
            <span class="text-white-50 small"> - <?= date('d M Y', strtotime($comment['created_at'])) ?></span>
            <p class="mt-2"><?= htmlspecialchars($comment['content']) ?></p>
          </div>
          <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if (\App\Core\Session::has('user_id')): ?>
        <form method="POST" action="/blog/<?= htmlspecialchars($article['slug']) ?>/comment" class="mt-4" data-animate="fade-up">
          <div class="mb-3">
            <textarea name="content" class="form-control form-control-luma" rows="4" placeholder="Votre commentaire..." required></textarea>
          </div>
          <button type="submit" class="btn btn-luma">Publier</button>
        </form>
        <?php else: ?>
          <p class="mt-4 text-white-50"><a href="/auth/login" class="text-light-pink">Connectez-vous</a> pour laisser un commentaire.</p>
        <?php endif; ?>
      </div>

      <div class="col-lg-4" data-animate="fade-up">
        <h3 class="mb-3">Autres populaires</h3>
        <div class="divider-accent mb-4"></div>
        <?php foreach ($popular as $p): ?>
        <a href="/blog/<?= htmlspecialchars($p['slug']) ?>" class="text-decoration-none d-block mb-3">
          <div class="d-flex gap-3 align-items-center">
            <div style="width:80px; height:80px; background:var(--luma-glass); border-radius:20px; flex-shrink:0;"></div>
            <div>
              <p class="text-white mb-1"><?= htmlspecialchars($p['title']) ?></p>
              <small class="text-light-pink"><?= date('d M Y', strtotime($p['created_at'])) ?></small>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
