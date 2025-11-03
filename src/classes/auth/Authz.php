<?php
declare(strict_types=1);
namespace iutnc\deefy\auth;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\exception\AuthnException;
use \Exception;

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
        $user = AuthnProvider::getSignedInUser();

        if ($user['role'] !== $expectedRole) {
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

        $user = AuthnProvider::getSignedInUser();

        if ($user['role'] === self::ROLE_ADMIN) {
            return;
        }

        $repo = DeefyRepository::getInstance();
        $playlist = $repo->findPlaylistById($playlistId);

        if ($playlist === null) {
            throw new Exception("Playlist not found (ID: $playlistId).");
        }

        $ownerId = $playlist->__get('id');

        if ($user['id'] !== (int)$ownerId) {
            throw new Exception("User is not the owner of this playlist.");
        }

    }
}