<?php

$pageTitle = 'Mes notifications';
?>
<?php ob_start(); ?>
<div class="container-fluid">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h1><i class="bi bi-bell"></i> Mes notifications</h1>
        <?php if ($countNonLues > 0): ?>
        <form method="POST" action="<?= APP_URL ?>/notifications/toutes-lues">
            <?php Security::csrfField(); ?>
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-check-all"></i> Tout marquer comme lu
            </button>
        </form>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="bi bi-list"></i> 
            <?php if ($countNonLues > 0): ?>
            <span class="badge bg-danger"><?= $countNonLues ?> non lue(s)</span>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <?php if (empty($notifications)): ?>
            <div class="text-center py-5">
                <i class="bi bi-bell-slash fs-1 text-muted"></i>
                <p class="text-muted mt-3">Aucune notification</p>
            </div>
            <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $notif): ?>
                <div class="list-group-item <?= $notif['lue'] ? '' : 'bg-light' ?>">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="d-flex align-items-start">
                            <i class="bi <?= Notification::getIcone($notif['type']) ?> <?= Notification::getTypeClass($notif['type']) ?> fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-1 <?= $notif['lue'] ? '' : 'fw-bold' ?>">
                                    <?= Security::e($notif['titre']) ?>
                                </h6>
                                <p class="mb-1 text-muted"><?= Security::e($notif['message']) ?></p>
                                <small class="text-muted">
                                    <?= Notification::formatDateRelative($notif['created_at']) ?>
                                </small>
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <?php if ($notif['demande_id']): ?>
                            <a href="<?= APP_URL ?>/demandes/<?= $notif['demande_id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (!$notif['lue']): ?>
                            <form method="POST" action="<?= APP_URL ?>/notifications/<?= $notif['id'] ?>/lue" class="d-inline">
                                <?php Security::csrfField(); ?>
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Marquer comme lu">
                                    <i class="bi bi-check"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>

<?php include APP_ROOT . '/app/views/layouts/main.php'; ?>
