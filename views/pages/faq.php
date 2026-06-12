<section class="py-5" data-animate="fade-up">
  <div class="container" style="max-width:900px;">
    <h1 class="section-title">FAQ</h1>
    <p class="section-subtitle">Vos questions les plus fréquentes</p>
    
    <?php foreach ($grouped as $category => $faqs): ?>
      <h2 class="font-heading text-light-pink mt-5 mb-3" style="font-size:32px;"><?= htmlspecialchars($category) ?></h2>
      <div class="animate-stagger">
      <?php foreach ($faqs as $faq): ?>
      <div class="card card-luma mb-3" data-animate="fade-up">
        <div class="card-body">
          <h5 class="card-title text-white fw-bold" style="cursor:pointer;" onclick="this.parentElement.nextElementSibling.classList.toggle('d-none')">
            <?= htmlspecialchars($faq['question']) ?>
          </h5>
          <div class="d-none mt-3 text-white-50">
            <?= nl2br(htmlspecialchars($faq['answer'])) ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>
