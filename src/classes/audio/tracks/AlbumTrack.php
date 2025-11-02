<?php
declare(strict_types=1);
namespace iutnc\deefy\audio\tracks;
use iutnc\deefy\audio\tracks\AudioTrack;

//Exercice 1
class AlbumTrack extends AudioTrack{
    protected string $album;
	protected int $numPiste;


	public function __construct(string $titrePiste,string $cheminFich,string $album,int $num ){
		parent::__construct($titrePiste,$cheminFich);
		$this->album = $album;
		$this->numPiste = $num;
	}

	public function __toString() : string{
		return "Titre :  ".json_encode(get_object_vars($this));
	}






}