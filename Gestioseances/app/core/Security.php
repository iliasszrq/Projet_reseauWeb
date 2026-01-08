<?php
/**
 * Classe Security - Sécurité CSRF, XSS, mots de passe
 * Par Dev 1
 */

class Security
{
    // --- CSRF ---
    public static function generateCsrfToken(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        return $token;
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        return $token !== null && Session::get('csrf_token') === $token;
    }

    public static function csrfField(): void
    {
        $token = Session::get('csrf_token') ?? self::generateCsrfToken();
        echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    public static function getCsrfToken(): string
    {
        return Session::get('csrf_token') ?? self::generateCsrfToken();
    }

    // --- XSS ---
    public static function escape(?string $string): string
    {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }

    // Alias court
    public static function e(?string $string): string
    {
        return self::escape($string);
    }

    // --- Password bcrypt ---
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    // --- Tokens reset ---
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(50));
    }

    // --- Brute force ---
    public static function isAccountLocked(int $attempts, ?string $lockTime): bool
    {
        if ($attempts < MAX_LOGIN_ATTEMPTS) {
            return false;
        }

        if ($lockTime === null) {
            return false;
        }

        return (time() - strtotime($lockTime)) < LOCKOUT_TIME;
    }

    // --- Validation des entrées ---
    public static function sanitizeString(?string $string): string
    {
        return trim(strip_tags($string ?? ''));
    }

    public static function sanitizeEmail(?string $email): string
    {
        return filter_var($email ?? '', FILTER_SANITIZE_EMAIL);
    }

    public static function validateEmail(?string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
