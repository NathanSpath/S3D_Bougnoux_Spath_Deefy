<?php
declare(strict_types=1);

class Autoloader
{
    private string $prefixe;
    private string $racine;

    public function __construct(string $prefixe, string $racine){
        $this->prefixe = $prefixe;
        $this->racine = rtrim($racine, DIRECTORY_SEPARATOR); // évite les doublons de /
    }

    public function loadClass(string $classname): void {
        // Remplace le préfixe par le chemin racine
        $class = str_replace($this->prefixe, $this->racine . DIRECTORY_SEPARATOR, $classname) . ".php";

        // Remplace les backslashes des namespaces par le séparateur de dossier
        $fichier = str_replace('\\', DIRECTORY_SEPARATOR, $class);

        if (file_exists($fichier)) {
            require_once $fichier;
        }
    }

    public function register(): void {
        spl_autoload_register([$this, 'loadClass']);
    }
}
