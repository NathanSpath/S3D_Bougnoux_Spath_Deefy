<?php
declare(strict_types=1);

require_once 'AudioTrack.php';
require_once 'AlbumTrack.php';
require_once 'PodcastTrack.php';
require_once 'Renderer.php';
require_once 'AudioTrackRenderer.php';
require_once 'AlbumTrackRenderer.php';
require_once 'PodcastRenderer.php';
require_once 'InvalidPropertyNameException.php';

$piste1 = new AlbumTrack("Track1", "/audio/01-Im_with_you_BB-King-Lucille.mp3", "Album1", 1);
$piste2 = new AlbumTrack("Track2", "/audio/02-I_Need_Your_Love-BB_King-Lucille.mp3", "Album1", 2);
$podcast1 = new PodcastTrack("Pod1","/audio/03-Country_Girl-BB_King-Lucille.mp3","10/09/2025");
$audio1 = new AlbumTrackRenderer($piste1);
$audio2 = new AlbumTrackRenderer($piste2);
$pod1 = new PodcastRenderer($podcast1);
echo "<pre>";
//printf($piste1 -> toString());
//printf($piste2 -> toString() );
echo($piste1)."\n";
$audio1->render(0);
echo($piste2)."\n";
$audio2->render(0);
//echo($podcast1)."\n";
$pod1->render(0);
echo "</pre>";
echo $audio1->render(Renderer::COMPACT);
echo $audio2->render(Renderer::COMPACT);
echo $pod1->render(Renderer::COMPACT);

echo $audio1->render(Renderer::LONG);
echo $audio2->render(Renderer::LONG);
echo $pod1->render(Renderer::LONG);
echo "</pre>";

try {
    echo $piste1->__get("duree");
    echo $piste1->__get("blabla");
} catch (InvalidPropertyNameException $e) {
    echo "Erreur : " . $e->getMessage();
}
echo "</pre>";