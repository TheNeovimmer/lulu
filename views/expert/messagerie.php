<div class="row g-4">
  <div class="col-md-4">
    <div class="card-dashboard h-100 d-flex flex-column" style="min-height: 500px;">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title"><i class="bi bi-chat-dots me-2"></i>Mamans</h5>
      </div>
      <div class="card-dashboard-body flex-grow-1 overflow-auto pe-1">
        <?php if (!empty($conversations)): ?>
          <div class="d-grid gap-2">
            <?php foreach ($conversations as $c): ?>
              <?php $active = (isset($activePartner) && $activePartner['id'] == $c['id']); ?>
              <a href="/expert/messagerie?partner_id=<?= $c['id'] ?>"
                 class="d-flex align-items-center gap-3 p-3 text-decoration-none rounded-3 position-relative"
                 style="transition: all 0.2s; <?= $active ? 'background: var(--dprimary-subtle); border: 1px solid var(--dprimary);' : 'border: 1px solid transparent;' ?>">
                <div class="position-relative">
                  <img src="<?= $c['avatar'] ?: '/assets/images/home/avatar-placeholder.png' ?>"
                       alt="" class="rounded-circle" style="width: 44px; height: 44px; object-fit: cover;">
                  <?php if (($c['unread_count'] ?? 0) > 0): ?>
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: var(--dprimary); font-size: 0.65rem;">
                    <?= $c['unread_count'] ?>
                  </span>
                  <?php endif; ?>
                </div>
                <div class="flex-grow-1 min-width-0">
                  <div style="font-weight: 600; color: var(--dtext-dark); margin-bottom: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <?= htmlspecialchars($c['name']) ?>
                  </div>
                  <small style="color: var(--dtext-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block;">
                    <?= htmlspecialchars(mb_substr($c['last_message'] ?? '', 0, 50)) ?>
                  </small>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-5">
            <i class="bi bi-chat-heart" style="color: var(--dprimary); font-size: 2rem; margin-bottom: 12px;"></i>
            <p style="color: var(--dtext-muted); margin-bottom: 0;">Aucune conversation pour le moment.</p>
            <small style="color: var(--dtext-muted);">Les messages des mamans apparaîtront ici.</small>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card-dashboard h-100 d-flex flex-column" style="min-height: 500px;">
      <?php if ($activePartner): ?>
        <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom" style="border-color: var(--dborder-light) !important;">
          <img src="<?= $activePartner['avatar'] ?: '/assets/images/home/avatar-placeholder.png' ?>"
               alt="" class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover;">
          <div>
            <h6 style="font-weight: 600; color: var(--dprimary); margin-bottom: 0;"><?= htmlspecialchars($activePartner['name']) ?></h6>
            <small style="color: var(--dtext-muted);">Maman</small>
          </div>
        </div>

        <div class="flex-grow-1 overflow-auto mb-3 pe-1 d-flex flex-column gap-2" id="chatbox" style="max-height: 320px;">
          <?php if (!empty($chatHistory)): ?>
            <?php foreach ($chatHistory as $msg): ?>
              <?php $me = ($msg['sender_id'] == \App\Core\Session::get('user_id')); ?>
              <div class="d-flex <?= $me ? 'justify-content-end' : 'justify-content-start' ?>">
                <div class="p-3 rounded-3" style="max-width: 70%; border-radius: 16px; <?= $me ? 'background: var(--dprimary); color: #fff;' : 'background: var(--dprimary-subtle); border: 1px solid var(--dborder); color: var(--dtext-dark);' ?>">
                  <p class="mb-1 text-break"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                  <div class="text-end" style="font-size: 0.72rem; opacity: 0.6;">
                    <?= date('H:i', strtotime($msg['created_at'])) ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-center py-5 my-auto" style="color: var(--dtext-muted);">
              <i class="bi bi-chat-heart" style="color: var(--dprimary); font-size: 2rem; margin-bottom: 12px;"></i>
              <p>Commencez la conversation avec <?= htmlspecialchars($activePartner['name']) ?></p>
            </div>
          <?php endif; ?>
        </div>

        <form method="POST" action="/expert/messagerie/send" class="mt-auto">
          <?= \App\Core\Session::csrf_field() ?>
          <input type="hidden" name="receiver_id" value="<?= $activePartner['id'] ?>">
          <div class="input-group">
            <textarea name="message" class="form-control" placeholder="Écrivez votre message..." rows="1" required style="border: 1px solid var(--dborder); border-radius: var(--dradius-sm); font-family: var(--dfont);"></textarea>
            <button class="btn-dashboard btn-dashboard-primary px-4" type="submit"><i class="bi bi-send-fill"></i></button>
          </div>
        </form>
      <?php else: ?>
        <div class="my-auto text-center" style="color: var(--dtext-muted);">
          <i class="bi bi-chat-square-text-fill" style="color: var(--dprimary); font-size: 2rem; margin-bottom: 12px;"></i>
          <h5 style="font-weight: 600; color: var(--dtext-dark);">Messagerie</h5>
          <p class="mb-0">Sélectionnez une maman dans la liste pour échanger en toute sécurité.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  const cb = document.getElementById('chatbox');
  if (cb) {
    cb.scrollTop = cb.scrollHeight;
  }
</script>
