<?php
declare(strict_types=1);
namespace iutnc\deefy\dispatch;
use iutnc\deefy\action\Action;
use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastTrackAction;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\SigninAction;
use iutnc\deefy\action\AddUserAction;

class Dispatcher {
    private string $action;

    public function __construct(string $act) {
        $this->action = $act;
    }

    public function run() : void {
        switch ($this->action) {
            case "playlist":
                $id_playlist = $_GET['id'] ?? null;
                if ($id_playlist === null || !is_numeric($id_playlist)) {
                    $action = new DefaultAction();
                } else {
                    $action = new DisplayPlaylistAction((int)$id_playlist);
                }
                break;
            case "add-playlist":
                $action = new AddPlaylistAction();
                break;

            case "add-track":
                $action = new AddPodcastTrackAction();
                break;

            case "add-user":
                $action = new AddUserAction();
                break;

            case "signin":
                $action = new SigninAction();
                break;

            default:
                $action = new DefaultAction();
                break;
        }

        $html = $action();
        $this->renderPage($html);
    }
    private function renderPage(string $html) : void {
        echo <<<FIN
        <!DOCTYPE html>
        <html lang="fr">
            <head>
                <title>Deefy App</title>
                <meta charset="utf-8" />
            </head>
            <body>
                <h1>Deefy App</h1>
                <nav>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="?action=add-user">Inscription</a></li>
                        <li><a href="?action=signin">Connexion</a></li>
                        <li><a href="?action=add-playlist">Créer une playlist</a></li>
                        <li><a href="?action=playlist">Afficher la Playlist</a></li>
                        <li><a href="?action=add-track">Uploader une piste d'album</a></li>
                        <li><a href="?action=upload-cover">Uploader une pochette d'album</a></li>
                    </ul>
                </nav>
                $html
            </body>
        </html>
    FIN;
    }


}