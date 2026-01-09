<?php
$userName = Session::get('user_prenom') . ' ' . Session::get('user_nom');
$notificationModel = new Notification();
$unreadCount = $notificationModel->countNonLues(Session::get('user_id'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - GestioSeances</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary-color: #4f46e5; --secondary-color: #6366f1; }
        body { background-color: #f1f5f9; }
        .navbar { background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .btn-primary { background: var(--primary-color); border-color: var(--primary-color); }
        .table th { background: #f8fafc; font-weight: 600; }
        .status-active { color: #10b981; }
        .status-inactive { color: #ef4444; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= APP_URL ?>/demandes"><i class="bi bi-calendar-check me-2"></i>GestioSeances</a>
            <div class="navbar-nav ms-auto flex-row align-items-center">
                <a href="<?= APP_URL ?>/admin/users" class="nav-link text-white me-3"><i class="bi bi-people me-1"></i> Utilisateurs</a>
                <a href="<?= APP_URL ?>/stats" class="nav-link text-white me-3"><i class="bi bi-graph-up me-1"></i> Statistiques</a>
                <a href="<?= APP_URL ?>/notifications" class="nav-link text-white position-relative me-3">
                    <i class="bi bi-bell"></i>
                    <?php if ($unreadCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </a>
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

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-people me-2"></i>Gestion des Utilisateurs</h2>
            <a href="<?= APP_URL ?>/admin/users/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Nouvel Utilisateur</a>
        </div>

        <?php if (!empty($flash)): ?>
            <?php foreach ($flash as $type => $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr><th>#</th><th>Nom</th><th>Email</th><th>Role</th><th>Telephone</th><th>Statut</th><th>Cree le</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><strong><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></strong></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <?php $roleClass = match($u['role']) { 'directeur' => 'bg-danger', 'assistante' => 'bg-warning text-dark', 'professeur' => 'bg-primary', default => 'bg-secondary' }; ?>
                                    <span class="badge <?= $roleClass ?>"><?= ucfirst($u['role']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($u['telephone'] ?? '-') ?></td>
                                <td>
                                    <?php if ($u['actif']): ?>
                                        <span class="status-active"><i class="bi bi-check-circle-fill"></i> Actif</span>
                                    <?php else: ?>
                                        <span class="status-inactive"><i class="bi bi-x-circle-fill"></i> Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                                <td>
                                    <a href="<?= APP_URL ?>/admin/users/edit/<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <?php if ($u['id'] != Session::get('user_id')): ?>
                                    <form action="<?= APP_URL ?>/admin/users/delete/<?= $u['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Desactiver cet utilisateur ?')">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <footer class="text-center py-3 text-muted"><small>&copy; <?= date('Y') ?> GestioSeances - UEMF</small></footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
