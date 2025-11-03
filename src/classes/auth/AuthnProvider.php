<?php
declare(strict_types=1);
namespace iutnc\deefy\auth;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;

class AuthnProvider
{
    /**
     * Sign in a user by verifying email and password.
     * @param string $email
     * @param string $passwd2check
     * @return void
     * @throws AuthnException
     */
    public static function signin(string $email, string $passwd2check): void {
        $repo = DeefyRepository::getInstance();

        $user = $repo->getUserByEmail($email);

        if (!$user) {
            throw new AuthnException("email not found");
        }

        if (!password_verify($passwd2check, $user['passwd'])) {
            throw new AuthnException("invalid password");
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'email' => $user['email'],
            'role' => (int)$user['role']
        ];
    }

    /**
     * Register a new user with email and password.
     * @param string $email
     * @param string $pass
     * @return void
     * @throws AuthnException
     */
    public static function register( string $email,string $pass): void {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new AuthnException("Invalid user email format");
        }

        if (strlen($pass) < 10) {
            throw new AuthnException("Password is too short (10 characters minimum)");
        }

        $repo = DeefyRepository::getInstance();

        $existingUser = $repo->getUserByEmail($email);
        if ($existingUser) {
            throw new AuthnException("Email already in use");
        }

        $hash = password_hash($pass, PASSWORD_DEFAULT, ['cost' => 12]);

        try {
            $repo->insertUser($email, $hash, 1);
        } catch (\Exception $e) {
            throw new AuthnException("Failed to register user: " . $e->getMessage());
        }
    }

    /**
     * AJOUT : Récupère l'utilisateur connecté depuis la session.
     *
     * @return array Les données de l'utilisateur (id, email, role).
     * @throws AuthnException Si aucun utilisateur n'est authentifié.
     */
    public static function getSignedInUser(): array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            throw new AuthnException("User not authenticated");
        }

        return $_SESSION['user'];
    }
}