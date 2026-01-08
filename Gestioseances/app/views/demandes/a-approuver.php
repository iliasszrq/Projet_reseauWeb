<?php

$pageTitle = 'Demandes à approuver';
?>
<?php ob_start(); ?>
<div class="container-fluid">
    <div class="page-header">
        <h1><i class="bi bi-check2-square"></i> Demandes à approuver</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="bi bi-list-check"></i> Demandes validées par l'assistante (<?= count($demandes ?? []) ?>)
        </div>
        <div class="card-body">
            <?php if (empty($demandes)): ?>
            <div class="text-center py-5">
                <i class="bi bi-check-circle fs-1 text-success"></i>
                <p class="text-muted mt-3">Aucune demande en attente d'approbation.</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Professeur</th>
                            <th>Type</th>
                            <th>Matière</th>
                            <th>Séance</th>
                            <th>Validée le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($demandes as $demande): ?>
                        <tr>
                            <td>
                                <?= $demande['id'] ?>
                                <?php if ($demande['urgente']): ?>
                                <span class="badge bg-danger">Urgent</span>
                                <?php endif; ?>
                            </td>
                            <td><?= Security::e($demande['professeur_prenom'] . ' ' . $demande['professeur_nom']) ?></td>
                            <td><span class="badge bg-<?= $demande['type'] === TYPE_CHANGEMENT ? 'info' : 'secondary' ?>"><?= ucfirst($demande['type']) ?></span></td>
                            <td><?= Security::e($demande['matiere_nom'] ?? '') ?></td>
                            <td><?= ucfirst($demande['seance_jour'] ?? '') ?> <?= substr($demande['seance_heure_debut'] ?? '', 0, 5) ?></td>
                            <td><?= $demande['date_validation_assistante'] ? date('d/m/Y H:i', strtotime($demande['date_validation_assistante'])) : '-' ?></td>
                            <td>
                                <a href="<?= APP_URL ?>/demandes/<?= $demande['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Examiner
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>

<?php include APP_ROOT . '/app/views/layouts/main.php'; ?>
