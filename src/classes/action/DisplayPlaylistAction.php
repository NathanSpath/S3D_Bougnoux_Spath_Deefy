<?php
declare(strict_types=1);
namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\auth\Authz; // 1. Importer la classe d'autorisation
use iutnc\deefy\exception\AuthnException; // 2. Importer l'exception d'authentification
use \Exception; // 3. Importer l'exception générale pour les droits

class DisplayPlaylistAction extends Action{
    private int $id;

    public function __construct(int $id) {
        parent::__construct();
        $this->id = $id;
    }

    public function __invoke(): string
    {
        try {
            // 4. VÉRIFICATION DES DROITS
            // Cette méthode lève une exception si l'utilisateur n'est pas :
            // - Connecté (AuthnException)
            // - Le propriétaire ou Admin (Exception)
            Authz::checkPlaylistOwner($this->id);

            // --- Si la ligne ci-dessus passe, l'utilisateur est autorisé ---
            // Le reste de votre code ne s'exécute que si l'autorisation est validée.

            $repo = DeefyRepository::getInstance();
            $playlist = $repo->findPlaylistById($this->id);

            // Note : L'exception "Playlist not found" peut déjà être levée
            // par checkPlaylistOwner, mais une double vérification ici est sûre.
            if ($playlist === null) {
                return "<div>Playlist non trouvée.</div>";
            }

            $tracks = $repo->findTracksByPlaylistId($this->id);

            $html_output = "<h2>Playlist : " . htmlspecialchars($playlist->__get('name')) . "</h2>";

            if (empty($tracks)) {
                $html_output .= "<p>Cette playlist est vide.</p>";
                return $html_output;
            }

            $html_output .= "<ul>";

            foreach ($tracks as $track) {
                $titre = htmlspecialchars($track['titre']);
                $artiste = htmlspecialchars($track['artiste'] ?? 'Artiste inconnu');
                $html_output .= "<li>" . $titre . " - " . $artiste . "</li>";
            }

            $html_output .= "</ul>";

            return $html_output;

        } catch (AuthnException $e) {
            // 5. Gérer le cas où l'utilisateur n'est pas connecté
            return "<p>Erreur : Vous devez être connecté pour accéder à cette ressource.</p>";

        } catch (Exception $e) {
            // 6. Gérer le cas où l'utilisateur est connecté mais non autorisé
            // (ou si la playlist n'existe pas, selon votre implémentation de Authz)
            return "<p>Accès refusé : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}