<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="page-header-dashboard">
      <h1 class="page-title-dashboard"><i class="bi bi-emoji-smile me-2"></i>Mon Bébé</h1>
    </div>

    <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
      <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($flash) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <?php if (!empty($baby)): ?>
    <div class="stats-row-dashboard mb-4">
      <div class="stat-card-dashboard">
        <div class="stat-card-icon"><i class="bi bi-person"></i></div>
        <div class="stat-card-info">
          <span class="stat-card-number text-truncate"><?= htmlspecialchars($baby['name']) ?></span>
          <span class="stat-card-label">Prénom</span>
        </div>
      </div>
      <div class="stat-card-dashboard">
        <div class="stat-card-icon"><i class="bi bi-calendar"></i></div>
        <div class="stat-card-info">
          <span class="stat-card-number"><?= date('d/m/Y', strtotime($baby['date_of_birth'])) ?></span>
          <span class="stat-card-label">Naissance</span>
        </div>
      </div>
      <div class="stat-card-dashboard">
        <div class="stat-card-icon"><i class="bi bi-gender-<?= $baby['gender'] === 'girl' ? 'female' : 'male' ?>"></i></div>
        <div class="stat-card-info">
          <span class="stat-card-number"><?= $baby['gender'] === 'girl' ? 'Fille' : ($baby['gender'] === 'boy' ? 'Garçon' : 'Autre') ?></span>
          <span class="stat-card-label">Sexe</span>
        </div>
      </div>
      <div class="stat-card-dashboard">
        <div class="stat-card-icon"><i class="bi bi-speedometer2"></i></div>
        <div class="stat-card-info">
          <span class="stat-card-number"><?= number_format($baby['last_weight'] ?? 0, 2) ?> kg</span>
          <span class="stat-card-label">Poids actuel</span>
        </div>
      </div>
    </div>

    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title" style="color: var(--dprimary);">Modifier les informations</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/dashboard/bebe" class="form-dashboard">
          <?= \App\Core\Session::csrf_field() ?>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" name="name" class="form-control" id="floatingName" placeholder="Prénom" value="<?= htmlspecialchars($baby['name']) ?>" required>
                <label for="floatingName">Prénom</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="date" name="birth_date" class="form-control" id="floatingBirthDate" placeholder="Date de naissance" value="<?= htmlspecialchars($baby['date_of_birth']) ?>" required>
                <label for="floatingBirthDate">Date de naissance</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <select name="gender" class="form-select" id="floatingGender" required>
                  <option value="girl" <?= $baby['gender'] === 'girl' ? 'selected' : '' ?>>Fille</option>
                  <option value="boy" <?= $baby['gender'] === 'boy' ? 'selected' : '' ?>>Garçon</option>
                  <option value="other" <?= $baby['gender'] === 'other' ? 'selected' : '' ?>>Autre</option>
                </select>
                <label for="floatingGender">Sexe</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="number" step="0.01" name="weight" class="form-control" id="floatingWeight" placeholder="Poids" value="<?= htmlspecialchars($baby['last_weight'] ?? '') ?>">
                <label for="floatingWeight">Poids (kg)</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="number" step="0.1" name="height" class="form-control" id="floatingHeight" placeholder="Taille" value="<?= htmlspecialchars($baby['last_height'] ?? '') ?>">
                <label for="floatingHeight">Taille (cm)</label>
              </div>
            </div>
          </div>
          <button type="submit" class="btn-dashboard btn-dashboard-primary mt-3">Enregistrer</button>
        </form>
      </div>
    </div>

    <div class="card-dashboard mb-4">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title" style="color: var(--dprimary);"><i class="bi bi-award me-2"></i>Étapes de développement</h5>
      </div>
      <div class="card-dashboard-body">
        <p style="color: var(--dtext-muted); font-size: 0.85rem; margin-bottom: 16px;">Cochez les étapes franchies par votre bébé pour suivre son éveil.</p>
        
        <form method="POST" action="/dashboard/bebe/milestones">
          <?= \App\Core\Session::csrf_field() ?>
          <div class="row g-3">
            <?php
            $milestoneList = [
                'sourire' => 'Premier sourire social (2-3 mois)',
                'tete' => 'Tient sa tête droite (3-4 mois)',
                'retourne' => 'Se retourne sur le ventre (5-6 mois)',
                'assis' => 'Tient assis sans soutien (7-8 mois)',
                'quatre_pattes' => 'Marche à quatre pattes (9-10 mois)',
                'debout' => 'Se tient debout avec appui (10-11 mois)',
                'premiers_pas' => 'Fait ses premiers pas seul (12-14 mois)',
                'mots' => 'Dit ses premiers mots (12-15 mois)'
            ];
            foreach ($milestoneList as $key => $label):
                $checked = isset($milestones[$key]);
            ?>
              <div class="col-md-6">
                <div class="form-check p-3 rounded-3 d-flex align-items-center gap-2 border" style="background: var(--dprimary-subtle);">
                  <input class="form-check-input ms-0 mt-0" type="checkbox" name="milestones[<?= $key ?>]" value="1" id="ms_<?= $key ?>" <?= $checked ? 'checked' : '' ?>>
                  <label class="form-check-label ms-2" for="ms_<?= $key ?>" style="color: var(--dtext-dark);">
                    <?= $label ?>
                    <?php if ($checked): ?>
                      <span style="color: var(--dprimary); font-size: 0.8rem;"><i class="bi bi-calendar-check me-1"></i>Acquis le <?= date('d/m/Y', strtotime($milestones[$key])) ?></span>
                    <?php endif; ?>
                  </label>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <button type="submit" class="btn-dashboard btn-dashboard-primary mt-3">Enregistrer les étapes</button>
        </form>
      </div>
    </div>

    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title" style="color: var(--dprimary);"><i class="bi bi-journal-heart me-2"></i>Journal des souvenirs</h5>
        <button class="btn-dashboard btn-dashboard-outline btn-dashboard-sm" data-bs-toggle="collapse" data-bs-target="#newMemoryForm"><i class="bi bi-plus-circle me-1"></i>Nouveau souvenir</button>
      </div>
      <div class="card-dashboard-body">
        <div class="collapse mb-4" id="newMemoryForm">
          <form method="POST" action="/dashboard/bebe/memories" class="p-3 rounded-3 form-dashboard" style="background: var(--dprimary-subtle); border: 1px solid var(--dborder);">
            <?= \App\Core\Session::csrf_field() ?>
            <div class="form-floating mb-3">
              <input type="text" name="title" class="form-control" id="floatingMemoryTitle" placeholder="Titre" required>
              <label for="floatingMemoryTitle">Titre de l'événement</label>
            </div>
            <div class="form-floating mb-3">
              <textarea name="content" class="form-control" id="floatingMemoryContent" placeholder="Description" rows="3" style="min-height: 100px;" required></textarea>
              <label for="floatingMemoryContent">Description</label>
            </div>
            <div class="form-floating mb-3">
              <input type="date" name="event_date" class="form-control" id="floatingMemoryDate" placeholder="Date" value="<?= date('Y-m-d') ?>" required>
              <label for="floatingMemoryDate">Date</label>
            </div>
            <button type="submit" class="btn-dashboard btn-dashboard-primary btn-dashboard-sm">Publier le souvenir</button>
          </form>
        </div>

        <?php if (!empty($memories)): ?>
          <div class="d-grid gap-3">
            <?php foreach ($memories as $m): ?>
              <div class="p-3 rounded-3 d-flex justify-content-between align-items-start" style="background: var(--dprimary-subtle); border: 1px solid var(--dborder);">
                <div>
                  <span class="badge-dashboard" style="background: var(--dprimary-subtle); color: var(--dprimary); margin-bottom: 8px;"><?= date('d/m/Y', strtotime($m['event_date'])) ?></span>
                  <h6 style="font-weight: 600; color: var(--dtext-dark); margin-bottom: 4px;"><?= htmlspecialchars($m['title']) ?></h6>
                  <p style="color: var(--dtext-muted); margin-bottom: 0; font-size: 0.875rem;"><?= nl2br(htmlspecialchars($m['content'])) ?></p>
                </div>
                <form method="POST" action="/dashboard/bebe/memories/delete/<?= $m['id'] ?>">
                  <?= \App\Core\Session::csrf_field() ?>
                  <button type="submit" class="btn-icon danger" title="Supprimer" data-action="delete" data-url="/dashboard/bebe/memories/delete/<?= $m['id'] ?>" data-confirm="Supprimer ce souvenir ?"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="empty-state-dashboard">
            <i class="bi bi-journal-heart"></i>
            <p>Aucun souvenir enregistré. Capturez ses premiers moments précieux !</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <?php else: ?>
    <div class="card-dashboard">
      <div class="card-dashboard-header">
        <h5 class="card-dashboard-title" style="color: var(--dprimary);">Ajouter mon bébé</h5>
      </div>
      <div class="card-dashboard-body">
        <form method="POST" action="/dashboard/bebe" class="form-dashboard">
          <?= \App\Core\Session::csrf_field() ?>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" name="name" class="form-control" id="floatingBabyName" placeholder="Prénom" required>
                <label for="floatingBabyName">Prénom</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="date" name="birth_date" class="form-control" id="floatingBabyBirth" placeholder="Date de naissance" required>
                <label for="floatingBabyBirth">Date de naissance</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <select name="gender" class="form-select" id="floatingBabyGender" required>
                  <option value="">Choisir...</option>
                  <option value="girl">Fille</option>
                  <option value="boy">Garçon</option>
                  <option value="other">Autre</option>
                </select>
                <label for="floatingBabyGender">Sexe</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="number" step="0.01" name="weight" class="form-control" id="floatingBabyWeight" placeholder="Poids">
                <label for="floatingBabyWeight">Poids (kg)</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="number" step="0.1" name="height" class="form-control" id="floatingBabyHeight" placeholder="Taille">
                <label for="floatingBabyHeight">Taille (cm)</label>
              </div>
            </div>
          </div>
          <button type="submit" class="btn-dashboard btn-dashboard-primary mt-3">Créer</button>
        </form>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
