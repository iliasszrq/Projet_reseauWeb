<?php
/**
 * Classe Controller - Classe de base pour tous les contrôleurs
 * Fusion Dev 1 + Dev 2 - CORRIGÉ
 */

class Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Charger une vue
     */
    protected function view(string $viewName, array $data = []): void
    {
        extract($data);
        
        $viewPath = APP_ROOT . '/app/views/' . $viewName . '.php';
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("Vue non trouvée : {$viewName}");
        }
    }

    /**
     * Rediriger vers une URL
     */
    protected function redirect(string $url): void
    {
        header("Location: " . APP_URL . $url);
        exit;
    }

    /**
     * Retourner une réponse JSON
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    protected function get(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Messages flash - Définir
     */
    protected function setFlash(string $type, string $message): void
    {
        Session::setFlash($type, $message);
    }

    /**
     * Messages flash - Récupérer un type spécifique OU tous les flash
     */
    protected function getFlash(?string $type = null)
    {
        if ($type === null) {
            // Retourner tous les flash messages
            return Session::getAllFlash();
        }
        return Session::getFlash($type);
    }

    /**
     * Récupérer tous les flash messages
     */
    protected function getAllFlash(): array
    {
        return Session::getAllFlash();
    }

    /**
     * Vérifications d'authentification
     */
    protected function isLoggedIn(): bool
    {
        return Session::isLoggedIn();
    }

    protected function getUserId(): ?int
    {
        return Session::get('user_id');
    }

    protected function getUserRole(): ?string
    {
        return Session::get('user_role');
    }

    protected function hasRole(string $role): bool
    {
        return $this->getUserRole() === $role;
    }

    protected function requireLogin(): void
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('danger', 'Vous devez être connecté pour accéder à cette page.');
            $this->redirect('/login');
        }
    }

    protected function requireRole(string $role): void
    {
        $this->requireLogin();
        
        if (!$this->hasRole($role)) {
            $this->setFlash('danger', 'Vous n\'avez pas les droits pour accéder à cette page.');
            $this->redirect('/');
        }
    }

    /**
     * Vérification CSRF
     */
    protected function verifyCsrf(): bool
    {
        $token = $this->post('csrf_token');
        return Security::verifyCsrfToken($token);
    }

    protected function requireCsrf(): void
    {
        if (!$this->verifyCsrf()) {
            $this->setFlash('danger', 'Token de sécurité invalide. Veuillez réessayer.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }
}