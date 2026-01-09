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
    <title>Statistiques - GestioSeances</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --primary-color: #4f46e5; --secondary-color: #6366f1; }
        body { background-color: #f1f5f9; }
        .navbar { background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-number { font-size: 2rem; font-weight: 700; }
        .stat-label { color: #718096; font-size: 0.9rem; }
        .btn-primary { background: var(--primary-color); border-color: var(--primary-color); }
        .chart-container { position: relative; height: 300px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= APP_URL ?>/demandes"><i class="bi bi-calendar-check me-2"></i>GestioSeances</a>
            <div class="navbar-nav ms-auto flex-row align-items-center">
                <a href="<?= APP_URL ?>/admin/users" class="nav-link text-white me-3"><i class="bi bi-people me-1"></i> Utilisateurs</a>
                <a href="<?= APP_URL ?>/stats" class="nav-link text-white me-3 active"><i class="bi bi-graph-up me-1"></i> Statistiques</a>
                <a href="<?= APP_URL ?>/admin/settings" class="nav-link text-white me-3"><i class="bi bi-gear me-1"></i> Parametres</a>
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
            <h2><i class="bi bi-graph-up me-2"></i>Tableau de Bord</h2>
            <div>
                <a href="<?= APP_URL ?>/stats/export/excel" class="btn btn-success me-2"><i class="bi bi-file-earmark-excel me-1"></i> Excel</a>
                <a href="<?= APP_URL ?>/stats/export/pdf" class="btn btn-danger"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stat-card h-100" style="border-left: 4px solid #4f46e5;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div><div class="stat-number text-primary"><?= $stats['total'] ?></div><div class="stat-label">Total Demandes</div></div>
                        <i class="bi bi-folder fs-1 text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stat-card h-100" style="border-left: 4px solid #f6ad55;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div><div class="stat-number" style="color: #f6ad55;"><?= $stats['en_attente'] ?></div><div class="stat-label">En Attente</div></div>
                        <i class="bi bi-hourglass-split fs-1" style="color: #f6ad55; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stat-card h-100" style="border-left: 4px solid #48bb78;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div><div class="stat-number" style="color: #48bb78;"><?= $stats['approuvees'] ?></div><div class="stat-label">Approuvees</div></div>
                        <i class="bi bi-check-circle fs-1" style="color: #48bb78; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stat-card h-100" style="border-left: 4px solid #fc8181;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div><div class="stat-number" style="color: #fc8181;"><?= $stats['rejetees'] ?></div><div class="stat-label">Rejetees</div></div>
                        <i class="bi bi-x-circle fs-1" style="color: #fc8181; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Evolution (6 mois)</h5></div>
                    <div class="card-body"><div class="chart-container"><canvas id="monthlyChart"></canvas></div></div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Par Type</h5></div>
                    <div class="card-body"><div class="chart-container"><canvas id="typeChart"></canvas></div></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-people me-2"></i>Top Professeurs</h5></div>
                    <div class="card-body"><div class="chart-container"><canvas id="professeurChart"></canvas></div></div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-activity me-2"></i>Activite Recente</h5></div>
                    <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                        <?php if (empty($recentActivity)): ?>
                            <p class="text-muted text-center">Aucune activite</p>
                        <?php else: ?>
                            <?php foreach ($recentActivity as $activity): ?>
                            <div style="border-left: 3px solid #4f46e5; padding-left: 15px; margin-bottom: 15px;">
                                <strong><?= htmlspecialchars($activity['professeur_prenom'] . ' ' . $activity['professeur_nom']) ?></strong>
                                <span class="badge bg-<?= $activity['type'] === 'changement' ? 'primary' : 'warning' ?> ms-2"><?= ucfirst($activity['type']) ?></span>
                                <span class="badge bg-<?= match($activity['statut']) { 'approuvee' => 'success', 'rejetee' => 'danger', 'en_attente' => 'warning', default => 'secondary' } ?>"><?= ucfirst($activity['statut']) ?></span>
                                <div style="font-size: 0.8rem; color: #718096;"><i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($activity['updated_at'])) ?></div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center py-3 text-muted"><small>&copy; <?= date('Y') ?> GestioSeances - UEMF</small></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const monthlyData = <?= $monthlyStats ?>;
        const professeurData = <?= $statsByProfesseur ?>;
        const typeData = <?= $statsByType ?>;

        if (monthlyData && monthlyData.length > 0) {
            new Chart(document.getElementById('monthlyChart'), {
                type: 'bar',
                data: {
                    labels: monthlyData.map(d => d.month),
                    datasets: [
                        { label: 'Total', data: monthlyData.map(d => d.total), backgroundColor: 'rgba(79, 70, 229, 0.8)', borderRadius: 5 },
                        { label: 'Approuvees', data: monthlyData.map(d => d.approuvees), backgroundColor: 'rgba(72, 187, 120, 0.8)', borderRadius: 5 },
                        { label: 'Rejetees', data: monthlyData.map(d => d.rejetees), backgroundColor: 'rgba(252, 129, 129, 0.8)', borderRadius: 5 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        }

        if (typeData && typeData.length > 0) {
            new Chart(document.getElementById('typeChart'), {
                type: 'doughnut',
                data: {
                    labels: typeData.map(d => d.type === 'changement' ? 'Changement' : 'Annulation'),
                    datasets: [{ data: typeData.map(d => d.total), backgroundColor: ['rgba(79, 70, 229, 0.8)', 'rgba(246, 173, 85, 0.8)'], borderWidth: 0 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });
        }

        if (professeurData && professeurData.length > 0) {
            new Chart(document.getElementById('professeurChart'), {
                type: 'bar',
                data: {
                    labels: professeurData.map(d => d.prenom + ' ' + d.nom.charAt(0) + '.'),
                    datasets: [{ label: 'Demandes', data: professeurData.map(d => d.total), backgroundColor: 'rgba(99, 102, 241, 0.8)', borderRadius: 5 }]
                },
                options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
            });
        }
    </script>
</body>
</html>
