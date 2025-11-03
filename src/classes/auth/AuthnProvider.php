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

        // HYPOTHÈSE : Votre méthode getUserByEmail() doit récupérer
        // l'id, l'email, le passwd ET le rôle de l'utilisateur pour que cela fonctionne.
        $user = $repo->getUserByEmail($email);

        if (!$user) {
            throw new AuthnException("email not found");
        }

        if (!password_verify($passwd2check, $user['passwd'])) {
            throw new AuthnException("invalid password");
        }

        // AJOUT : Démarrage de la session (si pas déjà fait)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // AJOUT : Stockage de l'utilisateur en session
        // On ne stocke pas le mot de passe, juste les infos utiles.
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
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
        // ... (votre méthode register() reste inchangée)
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
            // Le rôle 1 est assigné par défaut
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