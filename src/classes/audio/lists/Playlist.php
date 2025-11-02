<?php
declare(strict_types=1);
namespace iutnc\deefy\audio\lists;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\lists\AudioList;
class Playlist extends AudioList {

    public function __construct(string $name){
        parent::__construct($name);
    }


    public function ajouterPiste(AudioTrack $track) : void{
        if (!in_array($track, $this->tracks, true)) {
            $this->tracks[] = $track;
            $this->nbPistes++;
            $this->duree += $track->duree;
        };
    }

    public function supprimerPiste(int $index) : void
    {
        if (isset($this->tracks[$index])) {
            $track = $this->tracks[$index];
            unset($this->tracks[$index]);
            $this->nbPistes--;
            $this->duree -= $track->duree;
        }
    }

    public function ajouterListe(array $tracks) : void{
        foreach ($tracks as $track) {
            $this->ajouterPiste($track);
        }
    }
}