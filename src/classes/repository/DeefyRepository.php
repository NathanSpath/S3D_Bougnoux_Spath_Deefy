<?php
declare(strict_types=1);
namespace iutnc\deefy\repository;
use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\PodcastTrack;
use PDO;

class DeefyRepository
{
    private \PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf)
    {
        $this->pdo = new \PDO($conf['dsn'], $conf['user'], $conf['pass'],
            [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => true,
            ]);
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $file)
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Error reading configuration file");
        }
        // Dans la méthode setConfig
        $driver = $conf['driver'];
        $host = $conf['host'];
        $database = $conf['database'];
        self::$config = [
            'dsn' => "$driver:host=$host;dbname=$database",
            'user' => $conf['username'],
            'pass' => $conf['password']
        ];

    }

    //public function findPlaylistById(int $id): Playlist {}

    public function saveEmptyPlaylist(Playlist $pl): Playlist {
        $query = "INSERT INTO playlist (nom) VALUES (:nom)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['nom' => $pl->name]);
        $pl->setID(intval($this->pdo->lastInsertId()));
        return $pl;
    }

    /**
     * Récupère une playlist par son ID.
     */
    public function findPlaylistById(int $id): ?Playlist {
        $query = "SELECT * FROM playlist WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $playlist = new Playlist($data['nom']);
            $playlist->setID(intval($data['id']));
            return $playlist;
        }
        return null;
    }

    /**
     * Récupère toutes les pistes (sous forme de tableau) pour un ID de playlist donné.
     * @param int $playlistId
     * @return array
     */
    public function findTracksByPlaylistId(int $playlistId): array {
        // Jointure entre la table 'track' et la table de liaison 'playlist2track'
        $query = "SELECT t.* FROM track AS t
                  INNER JOIN playlist2track AS p2t ON t.id = p2t.id_track
                  WHERE p2t.id_pl = :playlist_id";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['playlist_id' => $playlistId]);

        // Retourne toutes les pistes trouvées sous forme de tableaux associatifs
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Sauvegarde une nouvelle piste de podcast dans la base de données.
     * Note : J'invente les noms de table/colonnes. Adaptez-les !
     */
    /**
     * Sauvegarde une nouvelle piste de podcast dans la base de données.
     * Mise à jour selon la structure de la table 'track'.
     */
    public function savePodcastTrack(PodcastTrack $track): PodcastTrack {

        // Requête basée sur les colonnes de votre image
        $query = "INSERT INTO track (titre, filename, date_posdcast, type) 
                  VALUES (:titre, :filename, :date_podcast, 'podcast')";

        $stmt = $this->pdo->prepare($query);

        // Assurez-vous que les noms des propriétés ($track->name, etc.)
        // correspondent bien à votre classe PodcastTrack.
        $stmt->execute([
            'titre' => $track->titre,
            'filename' => $track->nomFichier,
            'date_podcast' => $track->date
        ]);

        // Mettre à jour l'objet $track avec l'ID de la nouvelle ligne
        $track->setID(intval($this->pdo->lastInsertId()));
        return $track;
    }

    /**
     * Lie une piste à une playlist dans la table de jointure.
     * Note : J'invente le nom de la table 'playlist_track'. Adaptez-le !
     */
    public function addTrackToPlaylist(int $trackId, int $playlistId): bool {
        $query = "INSERT INTO playlist2track (id_pl, id_track) 
                  VALUES (:playlist_id, :track_id)";
        $stmt = $this->pdo->prepare($query);

        return $stmt->execute([
            'playlist_id' => $playlistId,
            'track_id' => $trackId
        ]);
    }

    /**
     * Récupère un utilisateur par son email.
     * @param string $email
     * @return array|null
     */
    public function getUserByEmail(string $email): ?array {
        $query = "SELECT id, email, passwd FROM User WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Insère un nouvel utilisateur dans la base de données.
     * @param string $email
     * @param string $hashedPasswd
     * @param int $role
     * @return void
     */
    public function insertUser(string $email, string $hashedPasswd, int $role): void {
        $query = "INSERT INTO User (email, passwd, role) VALUES (:email, :passwd, :role)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'email' => $email,
            'passwd' => $hashedPasswd,
            'role' => $role
        ]);
    }

}
