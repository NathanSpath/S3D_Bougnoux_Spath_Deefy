<?php
declare(strict_types=1);
namespace iutnc\deefy\audio\lists;
use iutnc\deefy\exception\InvalidPropertyNameException;
class AudioList {
    private string $name;
    private int $duree=0;
    private ?int $id = null;
    protected int $nbPistes =0;
    protected array $tracks = [];

    public function __construct(string $name,array $tracks=[ ]){
        $this->name = $name;
        $this->duree = 0;
        $this->nbPistes = 0;
        foreach ($tracks as $track) {
            $this->duree = $this->duree+$track->duree;
            $this->nbPistes = $this->nbPistes + 1;
        }
    }

    public function __get(string $name) : mixed {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new InvalidPropertyNameException($name);
    }
    /**
     * Permet au DeefyRepository de définir l'ID après l'insertion en BD.
     */
    public function setID(int $id): void {
        $this->id = $id;
    }

    /**
     * Permet aux Actions de récupérer l'ID pour le passer dans l'URL.
     */
    public function getId(): int {
        if ($this->id === null) {
            throw new \Exception("Tentative de récupération d'un ID sur une playlist non sauvegardée.");
        }
        return $this->id;
    }

}