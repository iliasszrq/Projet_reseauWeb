<?php

$pageTitle = 'File d\'attente';
?>
<?php ob_start(); ?>
<div class="container-fluid">
    <div class="page-header">
        <h1><i class="bi bi-inbox"></i> File d'attente des demandes</h1>
    </div>

    <!-- Demandes urgentes -->
    <?php if (!empty($urgentes)): ?>
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white">
            <i class="bi bi-exclamation-triangle"></i> Demandes urgentes (<?= count($urgentes) ?>)
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Professeur</th>
                            <th>Type</th>
                            <th>Matière</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($urgentes as $demande): ?>
                        <tr>
                            <td><?= $demande['id'] ?></td>
                            <td><?= Security::e($demande['professeur_prenom'] . ' ' . $demande['professeur_nom']) ?></td>
                            <td><span class="badge bg-<?= $demande['type'] === TYPE_CHANGEMENT ? 'info' : 'secondary' ?>"><?= ucfirst($demande['type']) ?></span></td>
                            <td><?= Security::e($demande['matiere_nom'] ?? '') ?></td>
                            <td><?= date('d/m/Y', strtotime($demande['created_at'])) ?></td>
                            <td>
                                <a href="<?= APP_URL ?>/demandes/<?= $demande['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Traiter
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Toutes les demandes en attente -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-list-check"></i> Demandes en attente de validation (<?= count($demandes ?? []) ?>)
        </div>
        <div class="card-body">
            <?php if (empty($demandes)): ?>
            <div class="text-center py-5">
                <i class="bi bi-check-circle fs-1 text-success"></i>
                <p class="text-muted mt-3">Aucune demande en attente. Bravo !</p>
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
                            <th>Soumise le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($demandes as $demande): ?>
                        <tr>
                            <td>
                                <?= $demande['id'] ?>
                                <?php if ($demande['urgente']): ?>
                                <span class="badge bg-danger">!</span>
                                <?php endif; ?>
                            </td>
                            <td><?= Security::e($demande['professeur_prenom'] . ' ' . $demande['professeur_nom']) ?></td>
                            <td><span class="badge bg-<?= $demande['type'] === TYPE_CHANGEMENT ? 'info' : 'secondary' ?>"><?= ucfirst($demande['type']) ?></span></td>
                            <td><?= Security::e($demande['matiere_nom'] ?? '') ?></td>
                            <td><?= ucfirst($demande['seance_jour'] ?? '') ?> <?= substr($demande['seance_heure_debut'] ?? '', 0, 5) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($demande['date_soumission'] ?? $demande['created_at'])) ?></td>
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
