<?php

$pageTitle = 'Modifier demande #' . ($demande['id'] ?? '');
?>
<?php ob_start(); ?>
<div class="container-fluid">
    <div class="page-header">
        <h1><i class="bi bi-pencil"></i> Modifier la demande #<?= $demande['id'] ?></h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <?php if (!empty($_SESSION['form_errors'])): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($_SESSION['form_errors'] as $error): ?>
                            <li><?= Security::e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['form_errors']); ?>
                    <?php endif; ?>

                    <form method="POST" action="<?= APP_URL ?>/demandes/<?= $demande['id'] ?>/update" enctype="multipart/form-data">
                        <?php Security::csrfField(); ?>

                        <!-- Type -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Type de demande *</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check card p-3">
                                        <input class="form-check-input" type="radio" name="type" id="type_changement" 
                                               value="changement" <?= $demande['type'] === TYPE_CHANGEMENT ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="type_changement">
                                            <strong>Changement</strong>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check card p-3">
                                        <input class="form-check-input" type="radio" name="type" id="type_annulation" 
                                               value="annulation" <?= $demande['type'] === TYPE_ANNULATION ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="type_annulation">
                                            <strong>Annulation</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Séance -->
                        <div class="mb-3">
                            <label for="seance_id" class="form-label fw-bold">Séance concernée *</label>
                            <select class="form-select" id="seance_id" name="seance_id" required>
                                <?php foreach ($seances ?? [] as $seance): ?>
                                <option value="<?= $seance['id'] ?>" <?= $demande['seance_id'] == $seance['id'] ? 'selected' : '' ?>>
                                    <?= Security::e($seance['matiere_nom']) ?> - 
                                    <?= ucfirst($seance['jour']) ?> 
                                    <?= substr($seance['heure_debut'], 0, 5) ?>-<?= substr($seance['heure_fin'], 0, 5) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Motif -->
                        <div class="mb-3">
                            <label for="motif" class="form-label fw-bold">Motif *</label>
                            <textarea class="form-control" id="motif" name="motif" rows="4" required><?= Security::e($demande['motif']) ?></textarea>
                        </div>

                        <!-- Options changement -->
                        <div id="changement_options" style="display: <?= $demande['type'] === TYPE_CHANGEMENT ? 'block' : 'none' ?>">
                            <hr>
                            <h6 class="text-primary mb-3">Options de changement</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="nouvelle_date" class="form-label">Nouvelle date</label>
                                    <input type="date" class="form-control" id="nouvelle_date" name="nouvelle_date"
                                           value="<?= $demande['nouvelle_date'] ?? '' ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="nouvelle_heure_debut" class="form-label">Nouvelle heure début</label>
                                    <input type="time" class="form-control" id="nouvelle_heure_debut" name="nouvelle_heure_debut"
                                           value="<?= substr($demande['nouvelle_heure_debut'] ?? '', 0, 5) ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="nouvelle_heure_fin" class="form-label">Nouvelle heure fin</label>
                                    <input type="time" class="form-control" id="nouvelle_heure_fin" name="nouvelle_heure_fin"
                                           value="<?= substr($demande['nouvelle_heure_fin'] ?? '', 0, 5) ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="nouvelle_salle_id" class="form-label">Nouvelle salle</label>
                                <select class="form-select" id="nouvelle_salle_id" name="nouvelle_salle_id">
                                    <option value="">-- Même salle --</option>
                                    <?php foreach ($salles ?? [] as $salle): ?>
                                    <option value="<?= $salle['id'] ?>" <?= $demande['nouvelle_salle_id'] == $salle['id'] ? 'selected' : '' ?>>
                                        <?= Security::e($salle['nom']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Commentaire -->
                        <div class="mb-3">
                            <label for="commentaire_professeur" class="form-label">Commentaire</label>
                            <textarea class="form-control" id="commentaire_professeur" name="commentaire_professeur" rows="2"><?= Security::e($demande['commentaire_professeur'] ?? '') ?></textarea>
                        </div>

                        <!-- Urgence -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="urgente" name="urgente" value="1"
                                       <?= $demande['urgente'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="urgente">
                                    <i class="bi bi-exclamation-triangle text-danger"></i> Marquer comme urgente
                                </label>
                            </div>
                        </div>

                        <!-- Pièces jointes existantes -->
                        <?php if (!empty($piecesJointes)): ?>
                        <div class="mb-3">
                            <label class="form-label">Pièces jointes existantes</label>
                            <ul class="list-group">
                                <?php foreach ($piecesJointes as $pj): ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span><i class="<?= PieceJointe::getIcone($pj['type_mime']) ?>"></i> <?= Security::e($pj['nom_original']) ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <!-- Nouvelles pièces jointes -->
                        <div class="mb-4">
                            <label for="pieces_jointes" class="form-label">Ajouter des pièces jointes</label>
                            <input type="file" class="form-control" id="pieces_jointes" name="pieces_jointes[]" multiple>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Enregistrer
                            </button>
                            <a href="<?= APP_URL ?>/demandes/<?= $demande['id'] ?>" class="btn btn-secondary">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('input[name="type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('changement_options').style.display = 
            this.value === 'changement' ? 'block' : 'none';
    });
});
</script>
<?php $content = ob_get_clean(); ?>

<?php include APP_ROOT . '/app/views/layouts/main.php'; ?>
