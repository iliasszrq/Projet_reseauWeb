<?php
$userName = Session::get('user_prenom') . ' ' . Session::get('user_nom');
$isEdit = $action === 'edit';
$title = $isEdit ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - GestioSeances</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary-color: #4f46e5; --secondary-color: #6366f1; }
        body { background-color: #f1f5f9; }
        .navbar { background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .btn-primary { background: var(--primary-color); border-color: var(--primary-color); }
        .form-label { font-weight: 500; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= APP_URL ?>/demandes"><i class="bi bi-calendar-check me-2"></i>GestioSeances</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-link text-white"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($userName) ?></span>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center mb-4">
                    <a href="<?= APP_URL ?>/admin/users" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
                    <h2 class="mb-0"><i class="bi bi-person-<?= $isEdit ? 'gear' : 'plus' ?> me-2"></i><?= $title ?></h2>
                </div>

                <div class="card">
                    <div class="card-body p-4">
                        <form action="<?= APP_URL ?><?= $isEdit ? '/admin/users/edit/' . $user['id'] : '/admin/users/create' ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="prenom" class="form-label">Prenom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Mot de passe <?= $isEdit ? '(laisser vide pour ne pas changer)' : '<span class="text-danger">*</span>' ?></label>
                                    <input type="password" class="form-control" id="password" name="password" <?= $isEdit ? '' : 'required' ?>>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="">-- Selectionner --</option>
                                        <option value="professeur" <?= ($user['role'] ?? '') === 'professeur' ? 'selected' : '' ?>>Professeur</option>
                                        <option value="assistante" <?= ($user['role'] ?? '') === 'assistante' ? 'selected' : '' ?>>Assistante</option>
                                        <option value="directeur" <?= ($user['role'] ?? '') === 'directeur' ? 'selected' : '' ?>>Directeur</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telephone" class="form-label">Telephone</label>
                                    <input type="tel" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="departement" class="form-label">Departement</label>
                                    <input type="text" class="form-control" id="departement" name="departement" value="<?= htmlspecialchars($user['departement'] ?? '') ?>">
                                </div>
                            </div>

                            <?php if ($isEdit): ?>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="actif" name="actif" <?= ($user['actif'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="actif">Compte actif</label>
                                </div>
                            </div>
                            <?php endif; ?>

                            <hr class="my-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?= APP_URL ?>/admin/users" class="btn btn-outline-secondary">Annuler</a>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Enregistrer' : 'Creer' ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
