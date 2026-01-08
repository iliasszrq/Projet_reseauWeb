<?php

$pageTitle = 'Mot de passe oublié';
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
                <i class="bi bi-key text-primary" style="font-size: 3rem;"></i>
                <h4 class="mt-2">Mot de passe oublié</h4>
                <p class="text-muted">Entrez votre email pour recevoir un lien de réinitialisation</p>
            </div>

            <?php if (!empty($flash['success'])): ?>
            <div class="alert alert-success">
                <?= Security::e($flash['success']) ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($flash['danger'])): ?>
            <div class="alert alert-danger">
                <?= Security::e($flash['danger']) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= APP_URL ?>/forgot-password">
                <?php Security::csrfField(); ?>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="email@example.com" required>
                    <label for="email">Adresse email</label>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Envoyer le lien
                    </button>
                </div>

                <div class="text-center">
                    <a href="<?= APP_URL ?>/login" class="text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Retour à la connexion
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
