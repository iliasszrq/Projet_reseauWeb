<?php
/**
 * Modèle User
 * Fusion Dev 1 (adapté pour nouvelle structure)
 */

class User extends Model
{
    protected $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    public function findById(int $id): ?array
    {
        return $this->find($id);
    }

    public function createUser(array $data): int
    {
        return $this->create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'password' => Security::hashPassword($data['password']),
            'role' => $data['role'] ?? ROLE_PROFESSEUR,
            'departement' => $data['departement'] ?? null,
            'actif' => 1
        ]);
    }

    public function updateLoginAttempts(int $userId, int $attempts, ?string $lockTime): bool
    {
        return $this->update($userId, [
            'tentatives_connexion' => $attempts,
            'date_blocage' => $lockTime
        ]);
    }

    public function resetLoginAttempts(int $userId): bool
    {
        return $this->update($userId, [
            'tentatives_connexion' => 0,
            'date_blocage' => null
        ]);
    }

    public function saveResetToken(int $userId, string $token): bool
    {
        $sql = "UPDATE users 
                SET token_reset = :token,
                    token_expiration = DATE_ADD(NOW(), INTERVAL 1 HOUR)
                WHERE id = :id";
        return $this->db->execute($sql, ['token' => $token, 'id' => $userId]);
    }

    public function findByResetToken(string $token): ?array
    {
        $sql = "SELECT * FROM users 
                WHERE token_reset = :token 
                AND token_expiration > NOW()";
        return $this->db->fetch($sql, ['token' => $token]);
    }

    public function updatePassword(int $userId, string $password): bool
    {
        return $this->update($userId, [
            'password' => Security::hashPassword($password),
            'token_reset' => null,
            'token_expiration' => null
        ]);
    }

    public function updateLastLogin(int $userId): bool
    {
        return $this->update($userId, [
            'derniere_connexion' => date('Y-m-d H:i:s')
        ]);
    }

    public function findByRole(string $role): array
    {
        return $this->findAllBy('role', $role);
    }

    public function getFullName(array $user): string
    {
        return $user['prenom'] . ' ' . $user['nom'];
    }
}
