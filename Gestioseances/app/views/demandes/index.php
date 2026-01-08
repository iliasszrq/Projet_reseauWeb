<?php
/**
 * Liste des demandes du professeur
 */
$pageTitle = 'Mes demandes';

// Récupérer les données pour la page
$isLoggedIn = Session::isLoggedIn();
$userRole = Session::get('user_role');
$userNom = Session::get('user_nom');
$userPrenom = Session::get('user_prenom');

// Notifications count
$notifCount = 0;
if ($isLoggedIn) {
    $notifModel = new Notification();
    $notifCount = $notifModel->countNonLues(Session::get('user_id'));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fc; }
        .navbar { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
        .card { border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); border-radius: 0.5rem; }
        .card-header { background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; font-weight: 600; }
        .border-left-primary { border-left: 4px solid #4e73df; }
        .border-left-warning { border-left: 4px solid #f6c23e; }
        .border-left-success { border-left: 4px solid #1cc88a; }
        .border-left-danger { border-left: 4px solid #e74a3b; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= APP_URL ?>">
                <i class="bi bi-calendar-check"></i> <?= APP_NAME ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= APP_URL ?>/demandes"><i class="bi bi-list-ul"></i> Mes demandes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/demandes/create"><i class="bi bi-plus-circle"></i> Nouvelle demande</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-bell fs-5"></i>
                            <?php if ($notifCount > 0): ?>
                            <span class="badge bg-danger position-absolute" style="top:0;right:0;transform:translate(25%,-25%)"><?= $notifCount ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/notifications">Voir les notifications</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= Security::e($userPrenom) ?> <?= Security::e($userNom) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text text-muted small"><?= ucfirst($userRole) ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= APP_URL ?>/logout"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="container-fluid mt-3">
        <?php 
        $flashTypes = ['success', 'danger', 'warning', 'info'];
        foreach ($flashTypes as $type):
            $message = Session::getFlash($type);
            if ($message):
        ?>
        <div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert">
            <?= Security::e($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; endforeach; ?>
    </div>

    <!-- Main Content -->
    <main class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800"><i class="bi bi-list-ul"></i> Mes demandes</h1>
            <a href="<?= APP_URL ?>/demandes/create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nouvelle demande
            </a>
        </div>

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-primary h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total</div>
                                <div class="h5 mb-0 font-weight-bold"><?= $stats['total'] ?? 0 ?></div>
                            </div>
                            <div class="col-auto"><i class="bi bi-folder fs-2 text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-warning h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">En attente</div>
                                <div class="h5 mb-0 font-weight-bold"><?= $stats['en_attente'] ?? 0 ?></div>
                            </div>
                            <div class="col-auto"><i class="bi bi-hourglass-split fs-2 text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-success h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approuvées</div>
                                <div class="h5 mb-0 font-weight-bold"><?= $stats['approuvees'] ?? 0 ?></div>
                            </div>
                            <div class="col-auto"><i class="bi bi-check-circle fs-2 text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-danger h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rejetées</div>
                                <div class="h5 mb-0 font-weight-bold"><?= $stats['rejetees'] ?? 0 ?></div>
                            </div>
                            <div class="col-auto"><i class="bi bi-x-circle fs-2 text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des demandes -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-table"></i> Liste des demandes
            </div>
            <div class="card-body">
                <?php if (empty($demandes)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-3">Vous n'avez pas encore de demandes.</p>
                    <a href="<?= APP_URL ?>/demandes/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Créer ma première demande
                    </a>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Matière</th>
                                <th>Séance</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($demandes as $demande): ?>
                            <tr>
                                <td><?= $demande['id'] ?></td>
                                <td>
                                    <?php if ($demande['type'] === 'changement'): ?>
                                    <span class="badge bg-info">Changement</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Annulation</span>
                                    <?php endif; ?>
                                    <?php if (!empty($demande['urgente']) && $demande['urgente']): ?>
                                    <span class="badge bg-danger">Urgent</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= Security::e($demande['matiere_nom'] ?? '') ?></td>
                                <td>
                                    <?= ucfirst($demande['seance_jour'] ?? '') ?>
                                    <?= substr($demande['seance_heure_debut'] ?? '', 0, 5) ?>
                                </td>
                                <td>
                                    <?php
                                    $statutClass = [
                                        'brouillon' => 'bg-secondary',
                                        'en_attente' => 'bg-warning',
                                        'validee_assistante' => 'bg-info',
                                        'approuvee' => 'bg-success',
                                        'rejetee' => 'bg-danger',
                                        'annulee' => 'bg-dark'
                                    ];
                                    $statutLabel = [
                                        'brouillon' => 'Brouillon',
                                        'en_attente' => 'En attente',
                                        'validee_assistante' => 'Validée',
                                        'approuvee' => 'Approuvée',
                                        'rejetee' => 'Rejetée',
                                        'annulee' => 'Annulée'
                                    ];
                                    $statut = $demande['statut'] ?? 'brouillon';
                                    ?>
                                    <span class="badge <?= $statutClass[$statut] ?? 'bg-secondary' ?>">
                                        <?= $statutLabel[$statut] ?? $statut ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($demande['created_at'])) ?></td>
                                <td>
                                    <a href="<?= APP_URL ?>/demandes/<?= $demande['id'] ?>" class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($demande['statut'] === 'brouillon'): ?>
                                    <a href="<?= APP_URL ?>/demandes/<?= $demande['id'] ?>/edit" class="btn btn-sm btn-outline-secondary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white py-3 mt-auto border-top">
        <div class="container-fluid text-center text-muted">
            <small>&copy; <?= date('Y') ?> <?= APP_NAME ?> - UEMF</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>