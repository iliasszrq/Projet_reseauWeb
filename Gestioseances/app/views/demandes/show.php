<?php
/**
 * Détail d'une demande
 */
$pageTitle = 'Demande #' . ($demande['id'] ?? '');
$userRole = Session::get('user_role');
$userId = Session::get('user_id');
?>
<?php ob_start(); ?>
<div class="container-fluid">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h1>
            <i class="bi bi-file-text"></i> Demande #<?= $demande['id'] ?>
            <?php if ($demande['urgente']): ?>
            <span class="badge bg-danger">Urgent</span>
            <?php endif; ?>
        </h1>
        <a href="<?= APP_URL ?>/demandes" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row">
        <!-- Détails de la demande -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-info-circle"></i> Informations</span>
                    <span class="badge <?= Demande::getStatutBadgeClass($demande['statut']) ?> fs-6">
                        <?= Demande::getStatutLabel($demande['statut']) ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Type :</strong>
                            <?php if ($demande['type'] === TYPE_CHANGEMENT): ?>
                            <span class="badge bg-info">Changement</span>
                            <?php else: ?>
                            <span class="badge bg-secondary">Annulation</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Créée le :</strong> 
                            <?= date('d/m/Y à H:i', strtotime($demande['created_at'])) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Professeur :</strong>
                            <?= Security::e($demande['professeur_prenom'] . ' ' . $demande['professeur_nom']) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Email :</strong>
                            <?= Security::e($demande['professeur_email']) ?>
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-primary"><i class="bi bi-calendar"></i> Séance concernée</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Matière :</strong> <?= Security::e($demande['matiere_nom']) ?>
                            <span class="text-muted">(<?= Security::e($demande['matiere_code']) ?>)</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Groupe :</strong> <?= Security::e($demande['seance_groupe'] ?? '-') ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Jour :</strong> <?= ucfirst($demande['seance_jour']) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Horaire :</strong> 
                            <?= substr($demande['seance_heure_debut'], 0, 5) ?> - <?= substr($demande['seance_heure_fin'], 0, 5) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Salle :</strong> <?= Security::e($demande['salle_nom']) ?>
                        </div>
                    </div>

                    <?php if ($demande['type'] === TYPE_CHANGEMENT): ?>
                    <hr>
                    <h6 class="text-success"><i class="bi bi-arrow-right-circle"></i> Changements demandés</h6>
                    <div class="row">
                        <?php if ($demande['nouvelle_date']): ?>
                        <div class="col-md-4">
                            <strong>Nouvelle date :</strong><br>
                            <?= date('d/m/Y', strtotime($demande['nouvelle_date'])) ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($demande['nouvelle_heure_debut']): ?>
                        <div class="col-md-4">
                            <strong>Nouvel horaire :</strong><br>
                            <?= substr($demande['nouvelle_heure_debut'], 0, 5) ?> - <?= substr($demande['nouvelle_heure_fin'] ?? '', 0, 5) ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($demande['nouvelle_salle_nom']): ?>
                        <div class="col-md-4">
                            <strong>Nouvelle salle :</strong><br>
                            <?= Security::e($demande['nouvelle_salle_nom']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <hr>

                    <h6><i class="bi bi-chat-left-text"></i> Motif</h6>
                    <p class="bg-light p-3 rounded"><?= nl2br(Security::e($demande['motif'])) ?></p>

                    <?php if ($demande['commentaire_professeur']): ?>
                    <h6><i class="bi bi-chat-dots"></i> Commentaire</h6>
                    <p class="bg-light p-3 rounded"><?= nl2br(Security::e($demande['commentaire_professeur'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pièces jointes -->
            <?php if (!empty($piecesJointes)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-paperclip"></i> Pièces jointes (<?= count($piecesJointes) ?>)
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php foreach ($piecesJointes as $pj): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="<?= PieceJointe::getIcone($pj['type_mime']) ?>"></i>
                                <?= Security::e($pj['nom_original']) ?>
                                <small class="text-muted">(<?= PieceJointe::formatTaille($pj['taille']) ?>)</small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Actions selon le rôle et le statut -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-lightning"></i> Actions
                </div>
                <div class="card-body">
                    <?php if ($userRole === ROLE_PROFESSEUR && $demande['professeur_id'] == $userId): ?>
                        <?php if ($demande['statut'] === STATUT_BROUILLON): ?>
                        <div class="d-flex gap-2">
                            <a href="<?= APP_URL ?>/demandes/<?= $demande['id'] ?>/edit" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Modifier
                            </a>
                            <form method="POST" action="<?= APP_URL ?>/demandes/<?= $demande['id'] ?>/soumettre" class="d-inline">
                                <?php Security::csrfField(); ?>
                                <button type="submit" class="btn btn-success" onclick="return confirm('Soumettre cette demande ?')">
                                    <i class="bi bi-send"></i> Soumettre
                                </button>
                            </form>
                        </div>
                        <?php elseif (in_array($demande['statut'], [STATUT_EN_ATTENTE, STATUT_VALIDEE_ASSISTANTE])): ?>
                        <form method="POST" action="<?= APP_URL ?>/demandes/<?= $demande['id'] ?>/annuler">
                            <?php Security::csrfField(); ?>
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Annuler cette demande ?')">
                                <i class="bi bi-x-circle"></i> Annuler ma demande
                            </button>
                        </form>
                        <?php else: ?>
                        <p class="text-muted mb-0">Aucune action disponible pour cette demande.</p>
                        <?php endif; ?>

                    <?php elseif ($userRole === ROLE_ASSISTANTE && $demande['statut'] === STATUT_EN_ATTENTE): ?>
                        <form method="POST" class="mb-3">
                            <?php Security::csrfField(); ?>
                            <div class="mb-3">
                                <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                                <textarea class="form-control" id="commentaire" name="commentaire" rows="2"></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" formaction="<?= APP_URL ?>/demandes/<?= $demande['id'] ?>/valider" class="btn btn-success">
                                    <i class="bi bi-check"></i> Valider
                                </button>
                                <button type="submit" formaction="<?= APP_URL ?>/demandes/<?= $demande['id'] ?>/rejeter-assistante" class="btn btn-danger">
                                    <i class="bi bi-x"></i> Rejeter
                                </button>
                            </div>
                        </form>

                    <?php elseif ($userRole === ROLE_DIRECTEUR && $demande['statut'] === STATUT_VALIDEE_ASSISTANTE): ?>
                        <form method="POST" class="mb-3">
                            <?php Security::csrfField(); ?>
                            <div class="mb-3">
                                <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                                <textarea class="form-control" id="commentaire" name="commentaire" rows="2"></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" formaction="<?= APP_URL ?>/demandes/<?= $demande['id'] ?>/approuver" class="btn btn-success">
                                    <i class="bi bi-check2-all"></i> Approuver
                                </button>
                                <button type="submit" formaction="<?= APP_URL ?>/demandes/<?= $demande['id'] ?>/rejeter-directeur" class="btn btn-danger">
                                    <i class="bi bi-x"></i> Rejeter
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <p class="text-muted mb-0">Aucune action disponible.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Historique
                </div>
                <div class="card-body">
                    <?php if (empty($historique)): ?>
                    <p class="text-muted text-center">Aucune action enregistrée</p>
                    <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($historique as $event): ?>
                        <div class="timeline-item mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge bg-<?= Validation::getActionCouleur($event['action']) ?> me-2">
                                    <i class="bi <?= Validation::getActionIcone($event['action']) ?>"></i>
                                </span>
                                <strong><?= Validation::getActionLabel($event['action']) ?></strong>
                            </div>
                            <div class="small text-muted">
                                Par <?= Security::e($event['user_prenom'] . ' ' . $event['user_nom']) ?>
                                (<?= ucfirst($event['user_role']) ?>)
                            </div>
                            <div class="small text-muted">
                                <?= date('d/m/Y à H:i', strtotime($event['created_at'])) ?>
                            </div>
                            <?php if ($event['commentaire']): ?>
                            <div class="mt-2 small bg-light p-2 rounded">
                                <?= nl2br(Security::e($event['commentaire'])) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>

<?php include APP_ROOT . '/app/views/layouts/main.php'; ?>
