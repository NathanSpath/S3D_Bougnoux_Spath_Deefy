<?php
declare(strict_types=1);
namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\exception\AuthnException;
use \Exception;

class DisplayPlaylistAction extends Action{
    private int $id;

    public function __construct(int $id) {
        parent::__construct();
        $this->id = $id;
    }

    public function __invoke(): string
    {
        try {
            Authz::checkPlaylistOwner($this->id);


            $repo = DeefyRepository::getInstance();
            $playlist = $repo->findPlaylistById($this->id);

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
            return "<p>Erreur : Vous devez être connecté pour accéder à cette ressource.</p>";

        } catch (Exception $e) {
            return "<p>Accès refusé : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}