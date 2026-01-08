<?php
/**
 * Configuration principale de GestioSeances
 * Fusion Dev 1 + Dev 2
 */

// ============== BASE DE DONNÉES ==============
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestioseances');
define('DB_USER', 'root');
define('DB_PASS', '');

// ============== APPLICATION ==============
define('APP_NAME', 'GestioSeances');
define('APP_URL', 'http://localhost/GestioSeances/public');
define('APP_ROOT', dirname(__FILE__));
define('DEBUG', true);

// Afficher les erreurs en mode debug
if (DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// ============== UPLOAD ==============
define('UPLOAD_PATH', APP_ROOT . '/storage/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 Mo
define('ALLOWED_EXTENSIONS', ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx']);

// ============== EMAIL (PHPMailer) ==============
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'tonemail@gmail.com');
define('MAIL_PASSWORD', 'motdepasse-app');
define('MAIL_FROM', 'noreply@gestioseances.ma');
define('MAIL_FROM_NAME', 'GestioSeances');

// ============== SÉCURITÉ ==============
define('CSRF_TOKEN_NAME', 'csrf_token');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes

// ============== STATUTS DES DEMANDES ==============
define('STATUT_BROUILLON', 'brouillon');
define('STATUT_EN_ATTENTE', 'en_attente');
define('STATUT_VALIDEE_ASSISTANTE', 'validee_assistante');
define('STATUT_APPROUVEE', 'approuvee');
define('STATUT_REJETEE', 'rejetee');
define('STATUT_ANNULEE', 'annulee');

// ============== TYPES DE DEMANDES ==============
define('TYPE_CHANGEMENT', 'changement');
define('TYPE_ANNULATION', 'annulation');

// ============== RÔLES ==============
define('ROLE_PROFESSEUR', 'professeur');
define('ROLE_ASSISTANTE', 'assistante');
define('ROLE_DIRECTEUR', 'directeur');

// ============== CONFIG ARRAY (pour compatibilité Dev 1) ==============
$config = [
    'db' => [
        'host' => DB_HOST,
        'dbname' => DB_NAME,
        'user' => DB_USER,
        'password' => DB_PASS
    ],
    'mail' => [
        'smtp_host' => MAIL_HOST,
        'smtp_port' => MAIL_PORT,
        'username' => MAIL_USERNAME,
        'password' => MAIL_PASSWORD,
        'from' => MAIL_FROM,
        'from_name' => MAIL_FROM_NAME
    ],
    'app' => [
        'name' => APP_NAME,
        'url' => APP_URL,
        'debug' => DEBUG
    ],
    'security' => [
        'max_login_attempts' => MAX_LOGIN_ATTEMPTS,
        'lock_duration' => LOCKOUT_TIME
    ]
];

return $config;
