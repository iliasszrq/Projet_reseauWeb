<?php
/**
 * Point d'entrée de l'application GestioSeances
 * Fusion Dev 1 + Dev 2
 */

// Charger la configuration
require_once dirname(__DIR__) . '/config.php';

// Autoloader
spl_autoload_register(function ($class) {
    $directories = [
        APP_ROOT . '/app/core/',
        APP_ROOT . '/app/models/',
        APP_ROOT . '/app/controllers/',
    ];

    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Démarrer la session
Session::start();

// Créer le routeur
$router = new Router();

// ============================================
// ROUTES AUTH (Dev 1)
// ============================================
$router->get('/login', 'AuthController', 'showLogin');
$router->post('/login', 'AuthController', 'login');
$router->get('/logout', 'AuthController', 'logout');
$router->get('/forgot-password', 'AuthController', 'showForgotPassword');
$router->post('/forgot-password', 'AuthController', 'forgotPassword');
$router->get('/reset-password', 'AuthController', 'showResetPassword');
$router->post('/reset-password', 'AuthController', 'resetPassword');

// ============================================
// ROUTES DEMANDES (Dev 2)
// ============================================
$router->get('/demandes', 'DemandeController', 'index');
$router->get('/demandes/create', 'DemandeController', 'create');
$router->post('/demandes/store', 'DemandeController', 'store');
$router->get('/demandes/{id}', 'DemandeController', 'show');
$router->get('/demandes/{id}/edit', 'DemandeController', 'edit');
$router->post('/demandes/{id}/update', 'DemandeController', 'update');
$router->post('/demandes/{id}/soumettre', 'DemandeController', 'soumettre');
$router->post('/demandes/{id}/annuler', 'DemandeController', 'annuler');

// Routes Assistante
$router->get('/demandes/file-attente', 'DemandeController', 'fileAttente');
$router->post('/demandes/{id}/valider', 'DemandeController', 'valider');
$router->post('/demandes/{id}/rejeter-assistante', 'DemandeController', 'rejeterAssistante');

// Routes Directeur
$router->get('/demandes/a-approuver', 'DemandeController', 'aApprouver');
$router->post('/demandes/{id}/approuver', 'DemandeController', 'approuver');
$router->post('/demandes/{id}/rejeter-directeur', 'DemandeController', 'rejeterDirecteur');

// ============================================
// ROUTES NOTIFICATIONS (Dev 2)
// ============================================
$router->get('/notifications', 'NotificationController', 'index');
$router->post('/notifications/{id}/lue', 'NotificationController', 'marquerLue');
$router->post('/notifications/toutes-lues', 'NotificationController', 'marquerToutesLues');
$router->get('/notifications/count', 'NotificationController', 'count');
$router->get('/notifications/recent', 'NotificationController', 'recent');

// ============================================
// ROUTE ACCUEIL
// ============================================
$router->get('/', 'AuthController', 'showLogin');

// Résoudre la route
$router->resolve();
