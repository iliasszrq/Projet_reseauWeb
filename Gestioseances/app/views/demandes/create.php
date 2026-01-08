<?php

$pageTitle = 'Nouvelle demande';
?>
<?php ob_start(); ?>
<div class="container-fluid">
    <div class="page-header">
        <h1><i class="bi bi-plus-circle"></i> Nouvelle demande</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-file-earmark-plus"></i> Créer une demande de changement ou annulation
                </div>
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

                    <form method="POST" action="<?= APP_URL ?>/demandes/store" enctype="multipart/form-data">
                        <?php Security::csrfField(); ?>

                        <!-- Type de demande -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Type de demande *</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check card p-3">
                                        <input class="form-check-input" type="radio" name="type" id="type_changement" 
                                               value="changement" checked>
                                        <label class="form-check-label" for="type_changement">
                                            <i class="bi bi-arrow-repeat text-info"></i>
                                            <strong>Changement</strong>
                                            <br><small class="text-muted">Modifier la date, l'heure ou la salle</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check card p-3">
                                        <input class="form-check-input" type="radio" name="type" id="type_annulation" 
                                               value="annulation">
                                        <label class="form-check-label" for="type_annulation">
                                            <i class="bi bi-x-circle text-danger"></i>
                                            <strong>Annulation</strong>
                                            <br><small class="text-muted">Annuler complètement la séance</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Séance concernée -->
                        <div class="mb-3">
                            <label for="seance_id" class="form-label fw-bold">Séance concernée *</label>
                            <select class="form-select" id="seance_id" name="seance_id" required>
                                <option value="">-- Sélectionner une séance --</option>
                                <?php foreach ($seances ?? [] as $seance): ?>
                                <option value="<?= $seance['id'] ?>">
                                    <?= Security::e($seance['matiere_nom']) ?> - 
                                    <?= ucfirst($seance['jour']) ?> 
                                    <?= substr($seance['heure_debut'], 0, 5) ?>-<?= substr($seance['heure_fin'], 0, 5) ?>
                                    (<?= Security::e($seance['salle_nom']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Motif -->
                        <div class="mb-3">
                            <label for="motif" class="form-label fw-bold">Motif de la demande *</label>
                            <textarea class="form-control" id="motif" name="motif" rows="4" required
                                      placeholder="Expliquez la raison de votre demande (minimum 20 caractères)..."><?= Security::e($_SESSION['form_data']['motif'] ?? '') ?></textarea>
                            <div class="form-text">Minimum 20 caractères</div>
                        </div>

                        <!-- Options de changement (affichées si type = changement) -->
                        <div id="changement_options">
                            <hr>
                            <h6 class="text-primary mb-3"><i class="bi bi-gear"></i> Options de changement</h6>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="nouvelle_date" class="form-label">Nouvelle date</label>
                                    <input type="date" class="form-control" id="nouvelle_date" name="nouvelle_date"
                                           min="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="nouvelle_heure_debut" class="form-label">Nouvelle heure début</label>
                                    <input type="time" class="form-control" id="nouvelle_heure_debut" name="nouvelle_heure_debut">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="nouvelle_heure_fin" class="form-label">Nouvelle heure fin</label>
                                    <input type="time" class="form-control" id="nouvelle_heure_fin" name="nouvelle_heure_fin">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="nouvelle_salle_id" class="form-label">Nouvelle salle</label>
                                <select class="form-select" id="nouvelle_salle_id" name="nouvelle_salle_id">
                                    <option value="">-- Même salle --</option>
                                    <?php foreach ($salles ?? [] as $salle): ?>
                                    <option value="<?= $salle['id'] ?>">
                                        <?= Security::e($salle['nom']) ?> (capacité: <?= $salle['capacite'] ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Commentaire -->
                        <div class="mb-3">
                            <label for="commentaire_professeur" class="form-label">Commentaire additionnel</label>
                            <textarea class="form-control" id="commentaire_professeur" name="commentaire_professeur" 
                                      rows="2" placeholder="Informations complémentaires (optionnel)..."></textarea>
                        </div>

                        <!-- Urgence -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="urgente" name="urgente" value="1">
                                <label class="form-check-label" for="urgente">
                                    <i class="bi bi-exclamation-triangle text-danger"></i>
                                    Marquer comme <strong>urgente</strong>
                                </label>
                            </div>
                        </div>

                        <!-- Pièces jointes -->
                        <div class="mb-4">
                            <label for="pieces_jointes" class="form-label">Pièces jointes</label>
                            <input type="file" class="form-control" id="pieces_jointes" name="pieces_jointes[]" multiple
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <div class="form-text">Formats acceptés : PDF, JPG, PNG, DOC, DOCX (max 5 Mo par fichier)</div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Enregistrer (brouillon)
                            </button>
                            <a href="<?= APP_URL ?>/demandes" class="btn btn-secondary">
                                <i class="bi bi-x"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Aide -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-info-circle"></i> Aide
                </div>
                <div class="card-body">
                    <h6>Processus de validation</h6>
                    <ol class="small">
                        <li>Vous créez une demande (brouillon)</li>
                        <li>Vous la soumettez pour validation</li>
                        <li>L'assistante vérifie la faisabilité</li>
                        <li>Le directeur approuve ou rejette</li>
                    </ol>

                    <hr>

                    <h6>Conseils</h6>
                    <ul class="small">
                        <li>Soyez précis dans votre motif</li>
                        <li>Joignez des justificatifs si nécessaire</li>
                        <li>Utilisez l'option "urgent" avec parcimonie</li>
                    </ul>
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
