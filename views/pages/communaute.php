<!-- Sommaire / Anchored Summary -->
<nav class="communaute-sommaire" data-animate="fade-up">
  <div class="container">
    <div class="sommaire-inner">
      <span class="sommaire-label">Sommaire</span>
      <div class="sommaire-links">
        <a href="#communaute-hero" class="sommaire-link active">Présentation</a>
        <a href="#communaute-temoignages" class="sommaire-link">Témoignages</a>
        <a href="#communaute-themes" class="sommaire-link">Explorer</a>
        <a href="#communaute-posts" class="sommaire-link">Discussions</a>
        <a href="#communaute-cta" class="sommaire-link">Rejoindre</a>
      </div>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="communaute-hero" id="communaute-hero" data-animate="fade-up">
  <div class="communaute-hero-bg-img" aria-hidden="true"></div>
  <div class="communaute-hero-overlay" aria-hidden="true"></div>
  <div class="container position-relative" style="z-index:2;">
    <div class="row">
      <div class="col-lg-8">
        <div class="communaute-hero-title-group">
          <span class="communaute-hero-title-top">Une communaute</span>
          <span class="communaute-hero-title-bottom">de mamans bienveillantes</span>
        </div>
        <p class="communaute-hero-sub">
          Échangez, partagez et trouvez du soutien aupres d'autres mamans qui vivent les memes experiences que vous.
        </p>
        <div class="communaute-hero-actions">
          <a href="<?= \App\Core\Session::has('user_id') ? '#communaute-posts' : '/auth/register' ?>" class="communaute-btn-primary">
            <span>Rejoinder la communaute</span>
          </a>
          <div class="communaute-hero-stat-block">
            <span class="hero-stat-number"><?= number_format($mamansCount) ?>+</span>
            <span class="hero-stat-label">mamans deja connectees</span>
          </div>
        </div>
        <div class="communaute-hero-quote">
          <p>« Ici, chaque histoire compte et chaque maman trouve ecoute, soutien et inspiration. »</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Search & Ask -->
<section class="communaute-search-section" data-animate="fade-up">
  <div class="container">
    <div class="communaute-search-bar">
      <div class="communaute-search-inner">
        <i class="bi bi-search communaute-search-icon"></i>
        <input type="text" class="communaute-search-input" placeholder="Rechercher un sujet, une question, un conseil..." id="communaute-search">
      </div>
      <?php if (\App\Core\Session::has('user_id')): ?>
        <button class="communaute-btn-search-ask" data-bs-toggle="modal" data-bs-target="#newPostModal">Poser une question +</button>
      <?php else: ?>
        <a href="/auth/login" class="communaute-btn-search-ask">Poser une question +</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Sujets populaires -->
<section class="communaute-sujets" data-animate="fade-up">
  <div class="container">
    <div class="sujets-bar">
      <span class="sujets-label">Sujets populaires :</span>
      <?php foreach ($sujets as $s): ?>
        <a href="/communaute?q=<?= urlencode($s) ?>" class="sujet-pill-ref"><?= htmlspecialchars($s) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Temoignages -->
<?php if (!empty($testimonials)): ?>
<section class="section-luma communaute-temoignages" id="communaute-temoignages" data-animate="fade-up">
  <div class="container">
    <div class="temoignages-header">
      <h2 class="section-heading-luma" style="font-size:clamp(1.5rem,3.5vw,2.25rem);">Elles partagent leur experience</h2>
      <a href="#" class="temoignages-header-link">Voir tous les temoignages →</a>
    </div>
    <div class="animate-stagger row g-4 justify-content-center">
      <?php
        $tNames = ['Melanie', 'Imane', 'Clara'];
        $tSubtitles = ['Maman de Hugo', 'Maman de Sofia', 'Maman de Noa'];
        $tCategories = ['Grossesse', 'Accouchement', 'Allaitement'];
        $tTimes = ['il y a 2 jours', 'Il y a 5 jours', 'Il y a 1 semaine'];
        $tContents = [
          "Grace a la communaute Luma, j'ai pu poser toutes mes questions et me sentir rassuree a chaque etape de ma grossesse. Un vrai cocon de bienveillance !",
          "Vos temoignages m'ont beaucoup aidee avant mon accouchement. Merci pour vos conseils et votre soutien au quotidien.",
          "L'allaitement n'a pas toujours ete facile, mais grace a vos partages et encouragements, j'ai persevre. Merci a toutes !"
        ];
        $tColors = ['#f0a0bb', '#c94b72', '#a63b5a', '#e88aa8', '#d47a9a'];
        $tReactionColors = [
          ['#f0a0bb','#c94b72','#e88aa8','#d47a9a','#b85a7a'],
          ['#c94b72','#a63b5a','#e88aa8','#f0a0bb','#d47a9a'],
          ['#e88aa8','#f0a0bb','#c94b72','#b85a7a','#a63b5a']
        ];
        $ti = 0;
      ?>
      <?php foreach ($testimonials as $t): ?>
      <div class="col-md-4">
        <div class="temoignage-card-ref">
          <div class="temoignage-card-ref-header">
            <div class="temoignage-card-ref-user">
              <div class="temoignage-avatar-initials" style="background:<?= $tColors[$ti % 5] ?>;"><?= mb_substr($tNames[$ti % 3], 0, 1) ?></div>
              <div>
                <div class="temoignage-name-ref"><?= $tNames[$ti % 3] ?></div>
                <span class="temoignage-subtitle-ref"><?= $tSubtitles[$ti % 3] ?></span>
              </div>
            </div>
            <span class="temoignage-badge-ref"><?= $tCategories[$ti % 3] ?></span>
          </div>
          <p class="temoignage-text-ref"><?= htmlspecialchars($tContents[$ti % 3]) ?></p>
          <div class="temoignage-card-ref-footer">
            <div class="temoignage-reactions-ref">
              <?php for ($ri = 0; $ri < 5; $ri++): ?>
              <span class="reaction-dot-ref" style="background:<?= $tReactionColors[$ti % 3][$ri] ?>;"></span>
              <?php endfor; ?>
              <span class="reaction-count-ref">+12</span>
            </div>
            <span class="temoignage-time-ref"><?= $tTimes[$ti % 3] ?></span>
          </div>
        </div>
      </div>
      <?php $ti++; endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Explorer par theme -->
<section class="section-luma communaute-themes" id="communaute-themes" data-animate="fade-up">
  <div class="container">
    <div class="temoignages-header">
      <h2 class="section-heading-luma" style="font-size:clamp(1.5rem,3.5vw,2.25rem);">Explorez par theme</h2>
      <a href="#" class="temoignages-header-link">Voir tous les groupes →</a>
    </div>
    <div class="animate-stagger row g-4">
      <?php
        $themeBiIcons = ['bi-flower1', 'bi-moon-stars', 'bi-people', 'bi-heart-pulse'];
        $themeBiColors = ['#f0a0bb', '#c94b72', '#a63b5a', '#e88aa8'];
        $themeDescs = [
          'Vivez sereinement chaque etape de votre grossesse.',
          'De la naissance aux premiers mois de bebe.',
          'Conseils et partage d\'experiences entre parents.',
          'Prenez soin de vous, corps et esprit.'
        ];
        $themeMembres = ['12 842 membres', '15 673 membres', '18 309 membres', '9 246 membres'];
        $tii = 0;
      ?>
      <?php foreach ($themes as $theme): ?>
      <div class="col-md-6 col-lg-3">
        <a href="/communaute?theme=<?= urlencode($theme['name']) ?>" class="theme-card-ref-link">
          <div class="theme-card-ref">
            <div class="theme-card-ref-row">
              <div class="theme-card-ref-icon-wrap" style="background:<?= $themeBiColors[$tii % 4] ?>1a;">
                <i class="bi <?= $themeBiIcons[$tii % 4] ?> theme-card-bi-icon" style="color:<?= $themeBiColors[$tii % 4] ?>;"></i>
              </div>
              <h3 class="theme-card-ref-title"><?= htmlspecialchars($theme['name']) ?></h3>
            </div>
            <p class="theme-card-ref-desc"><?= $themeDescs[$tii % 4] ?></p>
            <span class="theme-card-ref-membres"><?= $themeMembres[$tii % 4] ?></span>
          </div>
        </a>
      </div>
      <?php $tii++; endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA Banner -->
<section class="section-luma" id="communaute-cta" data-animate="fade-up">
  <div class="container">
    <div class="cta-banner-ref">
      <div class="cta-banner-ref-left">
        <div class="cta-banner-ref-icon-wrap">
          <i class="bi bi-chat-heart cta-banner-bi-icon"></i>
        </div>
        <div>
          <h2 class="cta-banner-ref-title">Rejoignez la communaute Luma</h2>
          <p class="cta-banner-ref-text">Rejoignez des milliers de mamans bienveillantes pour echanger et partager</p>
        </div>
      </div>
      <div class="cta-banner-ref-features">
        <div class="cta-feature-ref-item">
          <i class="bi bi-shield-check cta-feature-bi-icon"></i>
          <span>Bienveillance garantie</span>
        </div>
        <div class="cta-feature-ref-item">
          <i class="bi bi-lock cta-feature-bi-icon"></i>
          <span>Groupe prive</span>
        </div>
        <div class="cta-feature-ref-item">
          <i class="bi bi-star cta-feature-bi-icon"></i>
          <span>Conseils d'experts</span>
        </div>
      </div>
      <div class="cta-banner-ref-btn-wrap">
        <a href="<?= \App\Core\Session::has('user_id') ? '#communaute-posts' : '/auth/register' ?>" class="cta-banner-ref-btn">
          Rejoindre la communaute →
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Discussions -->
<section class="section-luma communaute-posts" id="communaute-posts" data-animate="fade-up">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-5">
      <div>
        <h2 class="section-heading-luma" style="font-size:clamp(1.5rem,3vw,2.5rem);">Discussions recentes</h2>
        <p class="section-subheading-luma" style="font-size:clamp(0.9rem,1.4vw,1.125rem);">Participez aux echanges et partagez votre experience</p>
      </div>
      <?php if (\App\Core\Session::has('user_id')): ?>
        <button class="btn-cta btn-cta--ghost" data-bs-toggle="modal" data-bs-target="#newPostModal">
          <span>Nouveau sujet</span>
          <svg class="btn-arrow" width="34" height="12" viewBox="0 0 34 12" fill="none" aria-hidden="true">
            <path d="M0 6H28M28 6L22 1M28 6L22 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      <?php else: ?>
        <a href="/auth/login" class="btn-cta btn-cta--ghost">
          <span>Connectez-vous pour poster</span>
          <svg class="btn-arrow" width="34" height="12" viewBox="0 0 34 12" fill="none" aria-hidden="true">
            <path d="M0 6H28M28 6L22 1M28 6L22 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
      <?php endif; ?>
    </div>

    <?php if (!empty($posts)): ?>
      <div class="animate-stagger">
      <?php foreach ($posts as $p): ?>
      <div class="post-card" data-animate="fade-up">
        <div class="post-card-avatar">
          <div class="post-avatar-circle"><?= strtoupper(mb_substr(htmlspecialchars($p['author_name']), 0, 1)) ?></div>
        </div>
        <div class="post-card-content">
          <div class="post-card-top">
            <a href="/communaute/<?= $p['id'] ?>" class="post-card-title"><?= htmlspecialchars($p['title']) ?></a>
            <?php if (!empty($p['is_expert'])): ?>
              <span class="post-expert-badge">Experte</span>
            <?php endif; ?>
          </div>
          <div class="post-card-meta">
            <span class="post-card-author">Par <?= htmlspecialchars($p['author_name'] ?? 'Anonyme') ?></span>
            <span class="post-card-date"><?= date('d/m/Y', strtotime($p['created_at'])) ?></span>
          </div>
          <p class="post-card-text"><?= nl2br(htmlspecialchars(mb_substr($p['content'], 0, 250))) ?><?= mb_strlen($p['content']) > 250 ? '...' : '' ?></p>
          <div class="post-card-actions">
            <span class="post-action"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg> <?= $p['likes_count'] ?? 0 ?></span>
            <span class="post-action"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> <?= $p['comments_count'] ?? 0 ?></span>
          </div>
        </div>
        <a href="/communaute/<?= $p['id'] ?>" class="post-card-arrow">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        </a>
      </div>
      <?php endforeach; ?>
      </div>

      <?php if ($totalPages > 1): ?>
      <nav class="mt-5">
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
      <p class="text-white-50">Aucun sujet pour le moment. Soyez la premiere !</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Nouveau sujet Modal -->
<?php if (\App\Core\Session::has('user_id')): ?>
<div class="modal fade" id="newPostModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-luma-glass">
      <form method="POST" action="/communaute">
        <?= \App\Core\Session::csrf_field() ?>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  const links = document.querySelectorAll('.sommaire-link');
  const sections = document.querySelectorAll('[id^="communaute-"]');
  if (links.length && sections.length) {
    window.addEventListener('scroll', function() {
      let current = '';
      sections.forEach(s => {
        if (window.scrollY >= s.offsetTop - 200) current = s.id;
      });
      links.forEach(l => {
        l.classList.toggle('active', l.getAttribute('href') === '#' + current);
      });
    });
  }
  const searchInput = document.getElementById('communaute-search');
  const postCards = document.querySelectorAll('.post-card');
  if (searchInput && postCards.length) {
    searchInput.addEventListener('input', function() {
      const q = this.value.toLowerCase();
      postCards.forEach(card => {
        const title = card.querySelector('.post-card-title')?.textContent?.toLowerCase() || '';
        const text = card.querySelector('.post-card-text')?.textContent?.toLowerCase() || '';
        card.style.display = (!q || title.includes(q) || text.includes(q)) ? '' : 'none';
      });
    });
  }
});
</script>
