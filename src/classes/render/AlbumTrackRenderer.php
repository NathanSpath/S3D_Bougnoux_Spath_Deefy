<?php
declare(strict_types=1);
namespace iutnc\deefy\render;
//require_once 'AlbumTrack.php';
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\render\AudioTrackRenderer;
class AlbumTrackRenderer extends AudioTrackRenderer{

    private AlbumTrack $track;
    public function __construct(AlbumTrack $album){
        $this->track = $album;
    }

    protected function long() : string{
        return sprintf(
            '<div class="track long">
                <h2>%s</h2>
                <p>Fichier : %s</p>
                <audio controls>
                    <source src="%s" type="audio/flac">
                    Votre navigateur ne supporte pas l’élément audio.
                </audio>
             </div>',
            htmlspecialchars($this->track->__get("titre")),
            htmlspecialchars($this->track->__get("nomFichier")),
            htmlspecialchars($this->track->__get("nomFichier"))
        );
    }

    public function compact() : string{
        return sprintf(
            '<div class="track compact">
                <span>%s</span>
                <audio controls src="%s"></audio>
             </div>',
            htmlspecialchars($this->track->__get("titre")),
            htmlspecialchars($this->track->__get("nomFichier"))
        );
    }


}