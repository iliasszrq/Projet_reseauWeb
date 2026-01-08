<?php

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function showLogin(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
        }

        $this->view('auth/login', [
            'flash' => $this->getAllFlash()
        ]);
    }

    public function login(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/login');
        }

        if (!Security::verifyCsrfToken($this->post('csrf_token'))) {
            $this->setFlash('danger', 'Token de sécurité invalide.');
            $this->redirect('/login');
        }

        $email = Security::sanitizeEmail($this->post('email'));
        $password = $this->post('password');

        if (empty($email) || empty($password)) {
            $this->setFlash('danger', 'Veuillez remplir tous les champs.');
            $this->redirect('/login');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $this->setFlash('danger', 'Identifiants incorrects.');
            $this->redirect('/login');
        }

        if (Security::isAccountLocked($user['tentatives_connexion'], $user['date_blocage'])) {
            $this->setFlash('danger', 'Compte temporairement bloqué. Réessayez dans 15 minutes.');
            $this->redirect('/login');
        }

        if (!Security::verifyPassword($password, $user['password'])) {
            $attempts = $user['tentatives_connexion'] + 1;
            $lockTime = ($attempts >= MAX_LOGIN_ATTEMPTS) ? date('Y-m-d H:i:s') : null;
            $this->userModel->updateLoginAttempts($user['id'], $attempts, $lockTime);

            $this->setFlash('danger', 'Identifiants incorrects.');
            $this->redirect('/login');
        }

        if (!$user['actif']) {
            $this->setFlash('danger', 'Votre compte est désactivé.');
            $this->redirect('/login');
        }

        $this->userModel->resetLoginAttempts($user['id']);
        $this->userModel->updateLastLogin($user['id']);

        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['role']);
        Session::set('user_nom', $user['nom']);
        Session::set('user_prenom', $user['prenom']);
        Session::set('user_email', $user['email']);

        $this->setFlash('success', 'Bienvenue, ' . $user['prenom'] . ' !');
        $this->redirectToDashboard();
    }

    public function logout(): void
    {
        Session::destroy();
        session_start();
        $this->setFlash('success', 'Vous avez été déconnecté.');
        $this->redirect('/login');
    }

    public function showForgotPassword(): void
    {
        $this->view('auth/forgot-password', [
            'flash' => $this->getAllFlash()
        ]);
    }

    public function forgotPassword(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/forgot-password');
        }

        $email = Security::sanitizeEmail($this->post('email'));
        $user = $this->userModel->findByEmail($email);

        $this->setFlash('success', 'Si cette adresse existe, un email de réinitialisation a été envoyé.');

        if ($user) {
            $token = Security::generateToken();
            $this->userModel->saveResetToken($user['id'], $token);

        }

        $this->redirect('/login');
    }

    public function showResetPassword(): void
    {
        $token = $this->get('token');

        if (!$token) {
            $this->setFlash('danger', 'Lien invalide.');
            $this->redirect('/login');
        }

        $user = $this->userModel->findByResetToken($token);

        if (!$user) {
            $this->setFlash('danger', 'Lien expiré ou invalide.');
            $this->redirect('/login');
        }

        $this->view('auth/reset-password', [
            'token' => $token,
            'flash' => $this->getAllFlash()
        ]);
    }

    public function resetPassword(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/login');
        }

        $token = $this->post('token');
        $password = $this->post('password');
        $passwordConfirm = $this->post('password_confirm');

        if ($password !== $passwordConfirm) {
            $this->setFlash('danger', 'Les mots de passe ne correspondent pas.');
            $this->redirect('/reset-password?token=' . $token);
        }

        if (strlen($password) < 8) {
            $this->setFlash('danger', 'Le mot de passe doit contenir au moins 8 caractères.');
            $this->redirect('/reset-password?token=' . $token);
        }

        $user = $this->userModel->findByResetToken($token);

        if (!$user) {
            $this->setFlash('danger', 'Lien expiré ou invalide.');
            $this->redirect('/login');
        }

        $this->userModel->updatePassword($user['id'], $password);

        $this->setFlash('success', 'Mot de passe modifié avec succès. Vous pouvez vous connecter.');
        $this->redirect('/login');
    }

    private function redirectToDashboard(): void
    {
        $role = Session::get('user_role');

        switch ($role) {
            case ROLE_PROFESSEUR:
                $this->redirect('/demandes');
                break;
            case ROLE_ASSISTANTE:
                $this->redirect('/demandes/file-attente');
                break;
            case ROLE_DIRECTEUR:
                $this->redirect('/demandes/a-approuver');
                break;
            default:
                $this->redirect('/');
        }
    }
}
