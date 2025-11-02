<?php
declare(strict_types=1);
namespace iutnc\deefy\action;
use iutnc\deefy\action\action;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\repository\DeefyRepository;

class AddPodcastTrackAction extends Action
{
    public function __invoke(): string
    {
        if ($this->http_method === 'GET') {
            // 1. Récupérer l'ID de la playlist depuis l'URL
            $playlist_id = filter_var($_GET['playlist_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);

            if (empty($playlist_id)) {
                return "<div>Erreur : ID de playlist manquant.</div>";
            }

            // 2. Ajouter l'ID dans un champ caché du formulaire
            return "<form method='POST' action='?action=add-track' enctype='multipart/form-data'>
                    <input type='hidden' name='playlist_id' value='{$playlist_id}'>
                    <label for='name'>Nom de la track :</label>
                    <input type='text' id='name' name='name' required>
                    <label for='date'>Date d'ajout :</label>
                    <input type='date' id='date' name='date' required>
                    <label for='audiofile'>Fichier audio :</label>
                    <input type='file' id='audiofile' name='audiofile' accept='.mp3,audio/mpeg' required>
                    <input type='submit' value='Ajouter la track'>
                </form>";

        } elseif ($this->http_method === 'POST') {

            // 1. Récupérer l'ID de la playlist depuis le champ caché
            $playlist_id = filter_var($_POST['playlist_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);
            if (empty($playlist_id)) {
                return "<div>Erreur : ID de playlist manquant lors de la soumission.</div>";
            }

            $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
            $date = filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS);

            if (isset($_FILES['audiofile']) && $_FILES['audiofile']['error'] === UPLOAD_ERR_OK) {
                // ... (votre logique de validation de fichier est bonne) ...
                $fileInfo = $_FILES['audiofile'];
                // ... (fileName, fileType, etc.) ...
                $isMp3Extension = substr($fileInfo['name'], -4) === '.mp3';
                $isMpegType = $fileInfo['type'] === 'audio/mpeg';
                $isNotAPhpFile = strpos(strtolower($fileInfo['name']), '.php') === false;

                if ($isMp3Extension && $isMpegType && $isNotAPhpFile) {
                    // ... (votre logique d'upload de fichier est bonne) ...
                    $uploadDir = __DIR__ . '/../../audio/';
                    // ... (mkdir) ...
                    $randomFileName = bin2hex(random_bytes(16)) . '.mp3';
                    $destination = $uploadDir . $randomFileName;

                    if(move_uploaded_file($fileInfo['tmp_name'], $destination)) {

                        // --- LOGIQUE DE BASE DE DONNÉES ---

                        // 2. Initialiser le Repository
                        $r  =  DeefyRepository::getInstance();

                        // 3. Charger la playlist existante
                        $playlist = $r->findPlaylistById((int)$playlist_id);

                        if (!$playlist) {
                            return "<div>Erreur : Impossible de trouver la playlist ID {$playlist_id}.</div>";
                        }

                        // 4. Créer et Sauvegarder la nouvelle piste
                        $audioTrack = new PodcastTrack($name, $destination, $date);
                        $audioTrack = $r->savePodcastTrack($audioTrack); // Sauvegarde et récupère l'ID

                        // 5. Lier la piste à la playlist
                        $success = $r->addTrackToPlaylist($audioTrack->getId(), $playlist->getId());

                        if ($success) {
                            return <<<Fin
                                <div>Piste '{$name}' ajoutée avec succès !</div>
                                <a href="?action=add-track&playlist_id={$playlist_id}">Ajouter encore une piste</a>
                            Fin;
                        } else {
                            return "<div>Erreur lors de la liaison de la piste à la playlist.</div>";
                        }
                    }
                    else{
                        return "<div>Erreur lors du déplacement du fichier téléchargé.</div>";
                    }
                }
                else{
                    return "<div>Type de fichier non supporté. Veuillez télécharger un fichier MP3.</div>";
                }
            }
            else {
                $error = isset($_FILES['audiofile']) ? $_FILES['audiofile']['error'] : 'Fichier non envoyé';
                return "<div>Erreur lors du téléchargement du fichier. Code d'erreur : " . $error . "</div>";
            }
        } else {
            return "<div>Méthode HTTP non supportée.</div>";
        }
    }
}