<?php
declare(strict_types=1);
namespace iutnc\deefy\audio\tracks;
use iutnc\deefy\audio\tracks\AudioTrack;
class PodcastTrack extends AudioTrack {

    protected string $date;

    public function __construct(string $titrePiste, string $cheminFich, string $date) {
        parent::__construct($titrePiste, $cheminFich);
        $this->date = $date;
    }

}