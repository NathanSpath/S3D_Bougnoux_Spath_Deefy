<?php
declare(strict_types=1);
namespace iutnc\deefy\action;
use iutnc\deefy\action\action;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\repository\DeefyRepository;

class AddPlaylistAction extends Action
{
    public function __invoke(): string {
        if($this->http_method === 'GET'){
        return "<form method='POST' action='http://".$this->hostname.$this->script_name."?action=add-playlist'>;
                    <label for='name'>Nom de la playlist :</label>
                    <input type='text' id='name' name='name' required>
                    <input type='submit' value='Créer la playlist'>
                </form>";
        } elseif($this->http_method === 'POST'){
            $name = $_POST['name'];
            $name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
            $playlist = new Playlist($name);
            //$_SESSION['playlist'] = new Playlist($name);
            $r  =  DeefyRepository::getInstance();
            $playlist = $r->saveEmptyPlaylist( $playlist );
            $playlist_id = $playlist->getId();
            return <<<Fin
                        <div>Playlist '".$name."' créée avec succès !</div>;
                        <a href="?action=add-track&playlist_id={$playlist_id}">Ajouter une piste</a>
                    Fin;
        } else {
            return "<div>Méthode HTTP non supportée.</div>";
        }
    }


}