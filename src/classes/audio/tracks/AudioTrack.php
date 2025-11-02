<?php
declare(strict_types=1);
namespace iutnc\deefy\audio\tracks;
use iutnc\deefy\exception\InvalidPropertyNameException;
class AudioTrack{

    private int $id;
    protected string $titre;
    protected string $artiste="inconnu";
    protected int $annee=2000;
    protected string $genre;
    protected int $duree=100;
    protected string $nomFichier;


    public function __construct(string $titrePiste,string $cheminFich){
        $this->titre = $titrePiste;
        $this->nomFichier = $cheminFich;
    }

    public function __get(string $prop) {
        if (!property_exists($this, $prop)) {
            throw new InvalidPropertyNameException($prop);
        }
        return $this->$prop;
    }

    public function getID(): int {
        return $this->id;
    }

    public function setID(int $id): void {
        $this->id = $id;
    }

}