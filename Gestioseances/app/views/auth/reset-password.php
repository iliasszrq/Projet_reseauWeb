<?php

$pageTitle = 'Réinitialiser le mot de passe';
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
        body {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            max-width: 400px;
            width: 100%;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 3rem rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock text-primary" style="font-size: 3rem;"></i>
                <h4 class="mt-2">Nouveau mot de passe</h4>
            </div>

            <?php if (!empty($flash['danger'])): ?>
            <div class="alert alert-danger">
                <?= Security::e($flash['danger']) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= APP_URL ?>/reset-password">
                <?php Security::csrfField(); ?>
                <input type="hidden" name="token" value="<?= Security::e($token ?? '') ?>">

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Nouveau mot de passe" required minlength="8">
                    <label for="password">Nouveau mot de passe</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                           placeholder="Confirmer le mot de passe" required>
                    <label for="password_confirm">Confirmer le mot de passe</label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Réinitialiser
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
