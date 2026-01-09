<?php

class AdminController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function users(): void
    {
        $this->requireRole(ROLE_DIRECTEUR);
        $users = $this->getAllUsers();
        $this->view('admin/users', [
            'users' => $users,
            'flash' => $this->getFlash()
        ]);
    }

    public function createUser(): void
    {
        $this->requireRole(ROLE_DIRECTEUR);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $this->setFlash('error', 'Token invalide');
                $this->redirect('/admin/users/create');
                return;
            }

            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';
            $telephone = trim($_POST['telephone'] ?? '');
            $departement = trim($_POST['departement'] ?? '');

            if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($role)) {
                $this->setFlash('error', 'Champs obligatoires manquants');
                $this->redirect('/admin/users/create');
                return;
            }

            if ($this->userModel->findByEmail($email)) {
                $this->setFlash('error', 'Email existe deja');
                $this->redirect('/admin/users/create');
                return;
            }

            $hashedPassword = Security::hashPassword($password);

            $db = Database::getInstance()->getConnection();
            $sql = "INSERT INTO users (nom, prenom, email, password, role, telephone, departement, actif, created_at) VALUES (:nom, :prenom, :email, :password, :role, :telephone, :departement, 1, NOW())";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $role,
                'telephone' => $telephone,
                'departement' => $departement
            ]);

            if ($result) {
                $this->setFlash('success', 'Utilisateur cree');
                $this->redirect('/admin/users');
            } else {
                $this->setFlash('error', 'Erreur creation');
                $this->redirect('/admin/users/create');
            }
            return;
        }

        $this->view('admin/user-form', [
            'user' => null,
            'action' => 'create',
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }

    public function editUser(int $id): void
    {
        $this->requireRole(ROLE_DIRECTEUR);

        $user = $this->getUserById($id);
        if (!$user) {
            $this->setFlash('error', 'Utilisateur non trouve');
            $this->redirect('/admin/users');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $this->setFlash('error', 'Token invalide');
                $this->redirect('/admin/users/edit/' . $id);
                return;
            }

            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = $_POST['role'] ?? '';
            $telephone = trim($_POST['telephone'] ?? '');
            $departement = trim($_POST['departement'] ?? '');
            $actif = isset($_POST['actif']) ? 1 : 0;
            $newPassword = $_POST['password'] ?? '';

            $db = Database::getInstance()->getConnection();
            
            if (!empty($newPassword)) {
                $hashedPassword = Security::hashPassword($newPassword);
                $sql = "UPDATE users SET nom = :nom, prenom = :prenom, email = :email, password = :password, role = :role, telephone = :telephone, departement = :departement, actif = :actif WHERE id = :id";
                $params = ['nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'password' => $hashedPassword, 'role' => $role, 'telephone' => $telephone, 'departement' => $departement, 'actif' => $actif, 'id' => $id];
            } else {
                $sql = "UPDATE users SET nom = :nom, prenom = :prenom, email = :email, role = :role, telephone = :telephone, departement = :departement, actif = :actif WHERE id = :id";
                $params = ['nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'role' => $role, 'telephone' => $telephone, 'departement' => $departement, 'actif' => $actif, 'id' => $id];
            }

            $stmt = $db->prepare($sql);
            $result = $stmt->execute($params);

            if ($result) {
                $this->setFlash('success', 'Utilisateur modifie');
                $this->redirect('/admin/users');
            } else {
                $this->setFlash('error', 'Erreur modification');
                $this->redirect('/admin/users/edit/' . $id);
            }
            return;
        }

        $this->view('admin/user-form', [
            'user' => $user,
            'action' => 'edit',
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }

    public function deleteUser(int $id): void
    {
        $this->requireRole(ROLE_DIRECTEUR);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
            return;
        }

        if ($id == $this->getUserId()) {
            $this->setFlash('error', 'Impossible de supprimer votre compte');
            $this->redirect('/admin/users');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE users SET actif = 0 WHERE id = :id";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute(['id' => $id]);

        if ($result) {
            $this->setFlash('success', 'Utilisateur desactive');
        } else {
            $this->setFlash('error', 'Erreur desactivation');
        }
        $this->redirect('/admin/users');
    }

    public function settings(): void
    {
        $this->requireRole(ROLE_DIRECTEUR);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $this->setFlash('error', 'Token invalide');
                $this->redirect('/admin/settings');
                return;
            }
            $this->setFlash('success', 'Parametres enregistres');
            $this->redirect('/admin/settings');
            return;
        }

        $this->view('admin/settings', [
            'csrf_token' => Security::generateCsrfToken(),
            'flash' => $this->getFlash()
        ]);
    }

    private function getAllUsers(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    private function getUserById(int $id): ?array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }
}