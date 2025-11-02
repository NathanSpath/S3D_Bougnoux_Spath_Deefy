<?php
declare(strict_types=1);
namespace iutnc\deefy\auth;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;

class AuthnProvider
{
    /**
     * Vérifie les identifiants de connexion d'un utilisateur.
     *
     * @param string $email L'email fourni par l'utilisateur.
     * @param string $password_clear Le mot de passe en clair fourni.
     * @return array Les données de l'utilisateur (id, email) en cas de succès.
     * @throws AuthnException Si l'email n'est pas trouvé ou si le mot de passe est incorrect.
     */

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
}