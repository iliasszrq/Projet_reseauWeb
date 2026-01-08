<?php

$pageTitle = 'Connexion';
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
        .login-card {
            max-width: 400px;
            width: 100%;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 3rem rgba(0, 0, 0, 0.2);
        }
        .login-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-radius: 1rem 1rem 0 0;
            padding: 2rem;
            text-align: center;
        }
        .login-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .login-body {
            padding: 2rem;
        }
        .form-floating > label {
            color: #6c757d;
        }
        .btn-login {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #224abe 0%, #1a3a8a 100%);
        }
    </style>
</head>
<body>
    <div class="login-card bg-white">
        <div class="login-header">
            <i class="bi bi-calendar-check"></i>
            <h4 class="mb-0"><?= APP_NAME ?></h4>
            <small>Gestion des demandes de séances</small>
        </div>

        <div class="login-body">
            <?php if (!empty($flash['danger'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= Security::e($flash['danger']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (!empty($flash['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= Security::e($flash['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= APP_URL ?>/login">
                <?php Security::csrfField(); ?>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="email@example.com" required autofocus>
                    <label for="email"><i class="bi bi-envelope"></i> Adresse email</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Mot de passe" required>
                    <label for="password"><i class="bi bi-lock"></i> Mot de passe</label>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="bi bi-box-arrow-in-right"></i> Se connecter
                    </button>
                </div>

                <div class="text-center">
                    <a href="<?= APP_URL ?>/forgot-password" class="text-decoration-none">
                        Mot de passe oublié ?
                    </a>
                </div>
            </form>

            <hr class="my-4">

            <div class="text-center text-muted small">
                <p class="mb-1"><strong>Comptes de test :</strong></p>
                <p class="mb-0">prof@uemf.ac.ma / password123</p>
                <p class="mb-0">assistante@uemf.ac.ma / password123</p>
                <p class="mb-0">directeur@uemf.ac.ma / password123</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
