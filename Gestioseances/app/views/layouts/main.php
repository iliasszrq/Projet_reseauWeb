<?php
/**
 * Layout principal
 * Toutes les pages héritent de ce template
 */

// Vérifier si l'utilisateur est connecté pour le menu
$isLoggedIn = Session::isLoggedIn();
$userRole = Session::get('user_role');
$userNom = Session::get('user_nom');
$userPrenom = Session::get('user_prenom');

// Récupérer le nombre de notifications non lues
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
    <title><?= $pageTitle ?? 'GestioSeances' ?> - <?= APP_NAME ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
        }
        
        .sidebar {
            min-height: calc(100vh - 56px);
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem 1.5rem;
            font-weight: 500;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            border-radius: 0.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        
        .badge-statut {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(25%, -25%);
        }
        
        .content-wrapper {
            padding: 1.5rem;
        }
        
        .page-header {
            margin-bottom: 1.5rem;
        }
        
        .page-header h1 {
            color: #5a5c69;
            font-size: 1.75rem;
            font-weight: 400;
        }
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
                <?php if ($isLoggedIn): ?>
                <ul class="navbar-nav me-auto">
                    <?php if ($userRole === ROLE_PROFESSEUR): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/demandes"><i class="bi bi-list-ul"></i> Mes demandes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/demandes/create"><i class="bi bi-plus-circle"></i> Nouvelle demande</a>
                    </li>
                    <?php elseif ($userRole === ROLE_ASSISTANTE): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/demandes/file-attente"><i class="bi bi-inbox"></i> File d'attente</a>
                    </li>
                    <?php elseif ($userRole === ROLE_DIRECTEUR): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/demandes/a-approuver"><i class="bi bi-check2-square"></i> À approuver</a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <!-- Notifications -->
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-bell fs-5"></i>
                            <?php if ($notifCount > 0): ?>
                            <span class="badge bg-danger notification-badge"><?= $notifCount ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/notifications">
                                Voir toutes les notifications
                                <?php if ($notifCount > 0): ?>
                                <span class="badge bg-primary"><?= $notifCount ?></span>
                                <?php endif; ?>
                            </a></li>
                        </ul>
                    </li>
                    
                    <!-- User Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= Security::e($userPrenom) ?> <?= Security::e($userNom) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text text-muted small"><?= ucfirst($userRole) ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= APP_URL ?>/logout">
                                <i class="bi bi-box-arrow-right"></i> Déconnexion
                            </a></li>
                        </ul>
                    </li>
                </ul>
                <?php endif; ?>
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
        <?php 
            endif;
        endforeach; 
        ?>
    </div>

    <!-- Main Content -->
    <main class="content-wrapper">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white py-3 mt-auto border-top">
        <div class="container-fluid text-center text-muted">
            <small>&copy; <?= date('Y') ?> <?= APP_NAME ?> - UEMF</small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
