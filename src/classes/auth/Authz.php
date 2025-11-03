<?php
declare(strict_types=1);
namespace iutnc\deefy\auth;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\exception\AuthnException; // Levée par getSignedInUser
use \Exception; // Pour les erreurs d'autorisation

/**
 * Classe pour la gestion des autorisations (Authorization).
 */
class Authz
{
    /**
     * Le rôle Administrateur
     */
    public const ROLE_ADMIN = 100;

    /**
     * Vérifie que le rôle de l’utilisateur authentifié est conforme.
     *
     * @param int $expectedRole Le rôle attendu.
     * @return void
     * @throws AuthnException Si l'utilisateur n'est pas connecté.
     * @throws Exception Si l'utilisateur n'a pas le bon rôle.
     */
    public static function checkRole(int $expectedRole): void {
        // Récupère l'utilisateur ou lève une AuthnException
        $user = AuthnProvider::getSignedInUser();

        if ($user['role'] !== $expectedRole) {
            // L'utilisateur est connecté, mais n'a pas les droits
            throw new Exception("Insufficient permissions.");
        }
    }

    /**
     * Vérifie que la playlist appartient à l'utilisateur connecté
     * ou que l'utilisateur est ADMIN (rôle 100).
     *
     * @param int $playlistId ID de la playlist.
     * @return void
     * @throws AuthnException Si l'utilisateur n'est pas connecté.
     * @throws Exception Si la playlist n'existe pas ou si l'utilisateur n'est pas autorisé.
     */
    public static function checkPlaylistOwner(int $playlistId): void {

        // 1. Récupérer l'utilisateur (lève AuthnException si non connecté)
        $user = AuthnProvider::getSignedInUser();

        // 2. Vérifier si l'utilisateur est Admin
        if ($user['role'] === self::ROLE_ADMIN) {
            return; // L'admin a tous les droits, on s'arrête ici.
        }

        // 3. Si ce n'est pas l'admin, récupérer la playlist
        $repo = DeefyRepository::getInstance();
        $playlist = $repo->findPlaylistById($playlistId);

        if ($playlist === null) {
            throw new Exception("Playlist not found (ID: $playlistId).");
        }

        // 4. Récupérer l'ID du propriétaire de la playlist
        // HYPOTHÈSE : Votre table 'playlist' a une colonne 'user_id'
        // et votre objet Playlist la rend accessible (ex: via __get).
        $ownerId = $playlist->__get('user_id');

        // 5. Comparer l'ID de l'utilisateur avec l'ID du propriétaire
        if ($user['id'] !== (int)$ownerId) {
            throw new Exception("User is not the owner of this playlist.");
        }

        // Si on arrive ici, le user est le propriétaire.
    }
}