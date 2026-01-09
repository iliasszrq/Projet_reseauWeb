<?php

require_once dirname(__DIR__) . '/config.php';

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

Session::start();

$router = new Router();

$router->get('/login', 'AuthController', 'showLogin');
$router->post('/login', 'AuthController', 'login');
$router->get('/logout', 'AuthController', 'logout');
$router->get('/forgot-password', 'AuthController', 'showForgotPassword');
$router->post('/forgot-password', 'AuthController', 'forgotPassword');
$router->get('/reset-password', 'AuthController', 'showResetPassword');
$router->post('/reset-password', 'AuthController', 'resetPassword');

$router->get('/demandes', 'DemandeController', 'index');
$router->get('/demandes/create', 'DemandeController', 'create');
$router->post('/demandes/store', 'DemandeController', 'store');
$router->get('/demandes/{id}', 'DemandeController', 'show');
$router->get('/demandes/{id}/edit', 'DemandeController', 'edit');
$router->post('/demandes/{id}/update', 'DemandeController', 'update');
$router->post('/demandes/{id}/soumettre', 'DemandeController', 'soumettre');
$router->post('/demandes/{id}/annuler', 'DemandeController', 'annuler');

$router->get('/demandes/file-attente', 'DemandeController', 'fileAttente');
$router->post('/demandes/{id}/valider', 'DemandeController', 'valider');
$router->post('/demandes/{id}/rejeter-assistante', 'DemandeController', 'rejeterAssistante');

$router->get('/demandes/a-approuver', 'DemandeController', 'aApprouver');
$router->post('/demandes/{id}/approuver', 'DemandeController', 'approuver');
$router->post('/demandes/{id}/rejeter-directeur', 'DemandeController', 'rejeterDirecteur');

$router->get('/notifications', 'NotificationController', 'index');
$router->post('/notifications/{id}/lue', 'NotificationController', 'marquerLue');
$router->post('/notifications/toutes-lues', 'NotificationController', 'marquerToutesLues');
$router->get('/notifications/count', 'NotificationController', 'count');
$router->get('/notifications/recent', 'NotificationController', 'recent');

$router->get('/admin/users', 'AdminController', 'users');
$router->get('/admin/users/create', 'AdminController', 'createUser');
$router->post('/admin/users/create', 'AdminController', 'createUser');
$router->get('/admin/users/edit/{id}', 'AdminController', 'editUser');
$router->post('/admin/users/edit/{id}', 'AdminController', 'editUser');
$router->post('/admin/users/delete/{id}', 'AdminController', 'deleteUser');

$router->get('/admin/settings', 'AdminController', 'settings');
$router->post('/admin/settings', 'AdminController', 'settings');

$router->get('/stats', 'StatsController', 'index');
$router->get('/stats/export/excel', 'StatsController', 'exportExcel');
$router->get('/stats/export/pdf', 'StatsController', 'exportPdf');

$router->get('/', 'AuthController', 'showLogin');

$router->resolve();