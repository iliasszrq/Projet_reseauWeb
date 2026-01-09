<?php
$userName = Session::get('user_prenom') . ' ' . Session::get('user_nom');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parametres - GestioSeances</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary-color: #4f46e5; --secondary-color: #6366f1; }
        body { background-color: #f1f5f9; }
        .navbar { background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .btn-primary { background: var(--primary-color); border-color: var(--primary-color); }
        .nav-pills .nav-link.active { background: var(--primary-color); }
        .nav-pills .nav-link { color: #4a5568; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= APP_URL ?>/demandes"><i class="bi bi-calendar-check me-2"></i>GestioSeances</a>
            <div class="navbar-nav ms-auto flex-row align-items-center">
                <a href="<?= APP_URL ?>/admin/users" class="nav-link text-white me-3"><i class="bi bi-people me-1"></i> Utilisateurs</a>
                <a href="<?= APP_URL ?>/stats" class="nav-link text-white me-3"><i class="bi bi-graph-up me-1"></i> Statistiques</a>
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($userName) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= APP_URL ?>/logout"><i class="bi bi-box-arrow-right me-2"></i>Deconnexion</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <h2 class="mb-4"><i class="bi bi-gear me-2"></i>Parametres</h2>

        <?php if (!empty($flash)): ?>
            <?php foreach ($flash as $type => $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#general"><i class="bi bi-sliders me-2"></i>General</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#notifications"><i class="bi bi-bell me-2"></i>Notifications</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#securite"><i class="bi bi-shield-lock me-2"></i>Securite</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <form action="<?= APP_URL ?>/admin/settings" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="general">
                            <div class="card">
                                <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Parametres Generaux</h5></div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nom de l'Application</label>
                                        <input type="text" class="form-control" name="app_name" value="GestioSeances">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Etablissement</label>
                                        <input type="text" class="form-control" name="etablissement" value="EIDIA - UEMF">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Annee Universitaire</label>
                                        <input type="text" class="form-control" name="annee" value="2025-2026">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="notifications">
                            <div class="card">
                                <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-bell me-2"></i>Notifications</h5></div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="notif_email" name="notif_email" checked>
                                        <label class="form-check-label" for="notif_email">Envoyer des emails</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="notif_urgence" name="notif_urgence" checked>
                                        <label class="form-check-label" for="notif_urgence">Alertes urgentes</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="securite">
                            <div class="card">
                                <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Securite</h5></div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Tentatives max</label>
                                        <input type="number" class="form-control" name="max_attempts" value="5" min="3" max="10">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Duree blocage (min)</label>
                                        <input type="number" class="form-control" name="lockout_time" value="15" min="5" max="60">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="text-center py-3 text-muted"><small>&copy; <?= date('Y') ?> GestioSeances - UEMF</small></footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
