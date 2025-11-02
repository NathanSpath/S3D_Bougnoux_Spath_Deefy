<?php
declare(strict_types=1);
namespace iutnc\deefy\audio\lists;
use iutnc\deefy\audio\lists\AudioList;
class Album extends AudioList {

    private string $artiste;
    private string $dateSortie;

    public function __construct(string $name, array $tracks, string $artiste = "", string $dateSortie = "") {
        if(count($tracks) === 0){
            throw new Exception("Un album doit contenir au moins une piste !");
        }
        parent::__construct($name, $tracks);
        $this->artiste = $artiste;
        $this->dateSortie = $dateSortie;
    }

    /**
     * @param string $artiste
     */
    public function setArtiste(string $artiste): void
    {
        $this->artiste = $artiste;
    }

    /**
     * @param string $dateSortie
     */
    public function setDateSortie(string $dateSortie): void
    {
        $this->dateSortie = $dateSortie;
    }



}